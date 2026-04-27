<?php

namespace client\controllers;

use common\models\Event;
use common\models\MomentusShape;
use common\models\MomentusShapeSearch;
use common\models\MomentusServiceOrder;
use common\models\ShapeMomentusMapping;
use common\components\MomentusClient;
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MomentusController extends Controller
{
    public function getViewPath()
    {
        return \Yii::getAlias('@app/views/shape');
    }

    /**
     * Disable CSRF for API-style actions called from Draw.io (no CSRF token available).
     */
    public function beforeAction($action)
    {
        $csrfExempt = [
            'assign-mapping',
            'unassign-mapping',
            'add-shape',
            'add-service-order-item',
            'update-service-order-item',
            'delete-service-order-item',
            'save-new-item-to-package',
            'add-price-list-item',
            'update-price-list-item',
            'delete-price-list-item',
            'update-event-space-diagram-url',
            'get-event-space-diagram',
            'save-event-png',
            'save-service-order-mapping',
            'sync-service-order-items',
            'upsert-service-order',
            'save-event-svg-s3',
        ];
        if (in_array($action->id, $csrfExempt)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => [
                        'http://yii2a',
                        'http://localhost',
                        'https://momentusstaging.eventdrawus.com',
                        'https://momentus.eventdrawus.com',
                    ],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // Public read endpoint used by front-end mapping checks.
                        'actions' => ['shapes'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        // Read-only Momentus proxy endpoints (Service Order dialog + sidebar) without Yii login.
                        // Still uses server-side Momentus API credentials; revisit before production if needed.
                        'actions' => [
                            'list-functions',
                            'list-price-lists',
                            'get-price-list-items-by-list',
                            'get-event-service-order',
                            'get-event-space-diagram',
                            'upsert-service-order',
                            'sync-service-order-items',
                        ],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        // Floor plan exports from Draw.io (no client-app session). PNG: server trusts event id; SVG: event + userid must match DB.
                        'actions' => ['save-event-svg-s3', 'save-event-png', 'update-event-space-diagram-url'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        // Shape mapping actions called from Draw.io — no client-app session available.
                        'actions' => [
                            'assign-mapping',
                            'unassign-mapping',
                            'add-shape',
                            'search-resources',
                            'shape-primary-resource',
                        ],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => [
                            'shape-manager',
                            'view',
                            'create',
                            'update',
                            'delete',
                            // Service Orders
                            'list-service-orders',
                            'list-service-order-items',
                            'get-service-order-item',
                            'add-service-order-item',
                            'update-service-order-item',
                            'delete-service-order-item',
                            'save-new-item-to-package',
                            // Functions + Price Lists + Items
                            'search-price-list-items',
                            'get-price-list-item',
                            'add-price-list-item',
                            'update-price-list-item',
                            'delete-price-list-item',
                            // Service Order mapping + sync
                            'save-service-order-mapping',
                            
                            
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'shapes' => ['GET'],
                    'resources' => ['GET'],
                    'shape-primary-resource' => ['GET'],
                    'assign-mapping' => ['POST'],
                    'unassign-mapping' => ['POST'],
                    'add-shape' => ['POST'],
                    // Service Orders
                    'list-service-orders' => ['GET'],
                    'list-service-order-items' => ['GET'],
                    'get-service-order-item' => ['GET'],
                    'add-service-order-item' => ['POST'],
                    'update-service-order-item' => ['PUT', 'POST'],
                    'delete-service-order-item' => ['DELETE', 'POST'],
                    'save-new-item-to-package' => ['POST'],
                    // Functions + Price Lists + Items
                    'list-functions' => ['GET'],
                    'list-price-lists' => ['GET'],
                    'search-price-list-items' => ['GET'],
                    'get-price-list-item' => ['GET'],
                    'add-price-list-item' => ['POST'],
                    'update-price-list-item' => ['PUT', 'POST'],
                    'delete-price-list-item' => ['DELETE', 'POST'],
                    'get-price-list-items-by-list' => ['GET'],
                    // Saved Layout link + PNG
                    'get-event-space-diagram' => ['GET'],
                    'update-event-space-diagram-url' => ['POST'],
                    'save-event-png' => ['POST'],
                    'save-event-svg-s3' => ['POST'],
                    // Service Order mapping + sync
                    'get-event-service-order' => ['GET'],
                    'save-service-order-mapping' => ['POST'],
                    'sync-service-order-items' => ['POST'],
                    'upsert-service-order' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Resolve org_code: request param first, then logged-in user's client org code.
     */
    private function getOrgCode()
    {
        $fromRequest = trim((string) (\Yii::$app->request->get('org_code') ?: \Yii::$app->request->post('org_code', '')));
        if ($fromRequest !== '') {
            return $fromRequest;
        }

        if (!\Yii::$app->user->isGuest) {
            $clientId = \Yii::$app->user->identity->clientid;
            $client = \common\models\Client::findOne($clientId);
            if ($client) {
                $fromDb = trim((string) $client->momentusOrgCode);
                if ($fromDb !== '') {
                    return $fromDb;
                }
            }
        }

        return trim((string) (\Yii::$app->params['momentus']['orgCode'] ?? ''));
    }

    public function actionSearchResources()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $query = trim((string) \Yii::$app->request->get('q', ''));
        $page = \Yii::$app->request->get('page');
        $pageSize = \Yii::$app->request->get('pageSize');
        $order = \Yii::$app->request->get('order');
        // Only filter by org_code when explicitly provided.
        // Default behavior returns mappings across all org codes.
        $orgCode = trim((string) Yii::$app->request->get('org_code', ''));

        try {
            $client = new MomentusClient();
            $result = $client->searchResources($query, $page, $pageSize, $order, $orgCode);

            return $this->formatResources($result);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    public function actionShapeManager()
    {
        $searchModel = new MomentusShapeSearch();
        $orgCode = $this->getOrgCode();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $orgCode);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'orgCode' => $orgCode,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new MomentusShape();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        $orgCode = trim((string) $request->post('org_code', $request->get('org_code', '')));
        $fallbackUrl = ['shape-manager'];
        if ($orgCode !== '') {
            $fallbackUrl['org_code'] = $orgCode;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $returnUrl = trim((string) $request->post('return_url', $request->get('return_url', '')));
            if ($returnUrl !== '' && Url::isRelative($returnUrl)) {
                return $this->redirect($returnUrl);
            }
            return $this->redirect($fallbackUrl);
        }

        return $this->render('update', [
            'model' => $model,
            'returnUrl' => trim((string) $request->get('return_url', '')),
            'orgCode' => $orgCode,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['shape-manager']);
    }

    /**
     * JSON API: shapes with LEFT JOIN on mapping table, filtered by org_code.
     */
    public function actionShapes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $search = trim((string) Yii::$app->request->get('q', ''));

        $query = (new \yii\db\Query())
            ->select([
                's.id',
                's.source_id',
                's.shapeType',
                's.category',
                's.description',
                's.model',
                'm.id AS mapping_id',
                'm.momentus_resource_code',
                'm.momentus_resource_description',
                'm.sequence',
                'm.org_code',
            ])
            ->from('{{%shapes}} s');

        $query->leftJoin('{{%shape_momentus_mapping}} m', 's.id = m.shape_id');

        if ($search !== '') {
            $query->andWhere([
                'or',
                ['like', 's.shapeType', $search],
                ['like', 'm.momentus_resource_code', $search],
                ['like', 'm.momentus_resource_description', $search],
            ]);
        }

        $query->orderBy(['s.shapeType' => SORT_ASC, 'm.sequence' => SORT_ASC]);
        $rows = $query->all(Yii::$app->db);

        $shapesMap = [];
        foreach ($rows as $row) {
            $sid = (int) $row['id'];
            if (!isset($shapesMap[$sid])) {
                $shapesMap[$sid] = [
                    'id' => $sid,
                    'source_id' => (int) $row['source_id'],
                    'shapeType' => $row['shapeType'],
                    'category' => (int) $row['category'],
                    'description' => $row['description'],
                    'model' => $row['model'],
                    // Backward-compatible fields used by older 3D clients.
                    'linkedToMomentus' => false,
                    'momentus_resource_code' => null,
                    'momentus_resource_description' => null,
                    'momentus_sequence' => null,
                    'mappings' => [],
                ];
            }

            if ($row['mapping_id'] !== null) {
                $shapesMap[$sid]['mappings'][] = [
                    'mapping_id' => (int) $row['mapping_id'],
                    'resource_code' => $row['momentus_resource_code'],
                    'resource_description' => $row['momentus_resource_description'],
                    'sequence' => (int) $row['sequence'],
                    'org_code' => $row['org_code'],
                ];

                // Preserve old flat response shape using the first (primary) mapping.
                if ($shapesMap[$sid]['linkedToMomentus'] === false) {
                    $shapesMap[$sid]['linkedToMomentus'] = true;
                    $shapesMap[$sid]['momentus_resource_code'] = $row['momentus_resource_code'];
                    $shapesMap[$sid]['momentus_resource_description'] = $row['momentus_resource_description'];
                    $shapesMap[$sid]['momentus_sequence'] = (int) $row['sequence'];
                }
            }
        }

        return array_values($shapesMap);
    }

    /**
     * Given org_code + shape_id, return highest priority resource (lowest sequence).
     */
    public function actionShapePrimaryResource()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $shapeId = (int) Yii::$app->request->get('shape_id', 0);
        $orgCode = $this->getOrgCode();

        if (!$shapeId) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'shape_id is required.'];
        }
        if ($orgCode === '') {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'org_code is not configured (set params[momentus][orgCode] or pass org_code).'];
        }

        $mapping = ShapeMomentusMapping::findPrimaryMapping($shapeId, $orgCode);

        if ($mapping === null) {
            return [
                'shape_id' => $shapeId,
                'org_code' => $orgCode,
                'resource_code' => null,
                'resource_description' => null,
                'sequence' => null,
            ];
        }

        return [
            'shape_id' => $shapeId,
            'org_code' => $orgCode,
            'resource_code' => $mapping->momentus_resource_code,
            'resource_description' => $mapping->momentus_resource_description,
            'sequence' => $mapping->sequence,
        ];
    }

    /**
     * Creates/updates a mapping between a shape and a Momentus resource for a given org_code.
     */
    public function actionAssignMapping()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $shapeId = (int) $request->post('shape_id', 0);
        $orgCode = $this->getOrgCode();
        $resourceCode = trim((string) $request->post('resource_code', ''));
        $resourceDescription = trim((string) $request->post('resource_description', ''));
        $sequence = (int) $request->post('sequence', 1);

        if (!$shapeId) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'shape_id is required.'];
        }
        if ($orgCode === '') {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'org_code is not configured (set params[momentus][orgCode] or pass org_code).'];
        }

        $shape = MomentusShape::findOne($shapeId);
        if ($shape === null) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Shape not found.'];
        }

        $codeForDb = ($resourceCode !== '') ? $resourceCode : null;

        // Delete any existing mappings for this shape + org to avoid stale duplicates,
        // then create a single fresh row.
        ShapeMomentusMapping::deleteAll([
            'shape_id' => $shapeId,
            'org_code'  => $orgCode,
        ]);

        $mapping = new ShapeMomentusMapping();
        $mapping->shape_id                      = $shapeId;
        $mapping->org_code                      = $orgCode;
        $mapping->momentus_resource_code        = $codeForDb;
        $mapping->momentus_resource_description = $resourceDescription ?: null;
        $mapping->sequence                      = $sequence ?: 1;

        if (!$mapping->save()) {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Failed to save.', 'details' => $mapping->getErrors()];
        }

        return ['success' => true, 'mapping_id' => $mapping->id];
    }

    /**
     * Removes a shape-resource mapping.
     * Accepts either mapping_id (preferred) or shape_id + org_code (removes primary mapping for that shape/org).
     */
    public function actionUnassignMapping()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $mappingId = (int) Yii::$app->request->post('mapping_id', 0);
        $shapeId = (int) Yii::$app->request->post('shape_id', 0);
        $orgCode = $this->getOrgCode();

        if ($mappingId) {
            $mapping = ShapeMomentusMapping::findOne($mappingId);
            if ($mapping === null) {
                Yii::$app->response->statusCode = 404;
                return ['error' => 'Mapping not found.'];
            }
            $mapping->delete();
            return ['success' => true];
        }

        if ($shapeId && $orgCode !== '') {
            $mapping = ShapeMomentusMapping::findPrimaryMapping($shapeId, $orgCode);
            if ($mapping === null) {
                return ['success' => true];
            }
            $mapping->delete();
            return ['success' => true];
        }

        Yii::$app->response->statusCode = 400;
        return ['error' => 'Either mapping_id or shape_id is required. When using shape_id, org_code must be configured.'];
    }

    public function actionAddShape()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $shapeType = trim((string) Yii::$app->request->post('shape_type', ''));
        if ($shapeType === '') {
            Yii::$app->response->statusCode = 400;
            return ['ok' => false, 'error' => 'shape_type is required.'];
        }

        // Return existing record if the shapeType is already in the DB (idempotent).
        $existing = MomentusShape::find()->where(['shapeType' => $shapeType])->one();
        if ($existing !== null) {
            return [
                'ok' => true,
                'shape' => [
                    'id'        => (int) $existing->id,
                    'source_id' => (int) $existing->source_id,
                    'shapeType' => $existing->shapeType,
                    'mappings'  => [],
                ],
            ];
        }

        // Auto-generate source_id as MAX(source_id) + 1.
        $maxSourceId = (int) (new \yii\db\Query())
            ->from('{{%shapes}}')
            ->max('source_id', Yii::$app->db);
        $newSourceId = $maxSourceId + 1;

        $shape = new MomentusShape();
        $shape->source_id   = $newSourceId;
        $shape->shapeType   = $shapeType;
        $shape->category    = 0;
        $shape->elevate     = 0;
        $shape->height      = 0;
        $shape->model       = 'standard';
        $shape->description = $shapeType;

        if (!$shape->save(false)) {
            Yii::$app->response->statusCode = 500;
            return ['ok' => false, 'error' => 'Failed to save shape to database.'];
        }

        return [
            'ok' => true,
            'shape' => [
                'id'        => (int) $shape->id,
                'source_id' => (int) $shape->source_id,
                'shapeType' => $shape->shapeType,
                'mappings'  => [],
            ],
        ];
    }

    // ---------------------------------------------------------------
    // Service Orders (read-only — create order API not yet available)
    // ---------------------------------------------------------------

    /**
     * GET /momentus/list-service-orders?ODataQuery=...
     * List service orders via OData.
     */
    public function actionListServiceOrders()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $odataQuery = trim((string) Yii::$app->request->get('ODataQuery', ''));

        try {
            $client = new MomentusClient();
            return $client->listServiceOrders($odataQuery);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // Service Order Items
    // ---------------------------------------------------------------

    /**
     * GET /momentus/list-service-order-items?ODataQuery=...
     * List service order items via OData.
     */
    public function actionListServiceOrderItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $odataQuery = trim((string) Yii::$app->request->get('ODataQuery', ''));

        try {
            $client = new MomentusClient();
            return $client->listServiceOrderItems($odataQuery);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * GET /momentus/get-service-order-item?order_number=...&order_line_number=...&org_code=...
     */
    public function actionGetServiceOrderItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $orderNumber = Yii::$app->request->get('order_number');
        $orderLineNumber = Yii::$app->request->get('order_line_number');

        if ($orderNumber === null || $orderLineNumber === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'order_number and order_line_number are required.'];
        }

        try {
            $client = new MomentusClient();
            return $client->getServiceOrderItem($orgCode, $orderNumber, $orderLineNumber);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * POST /momentus/add-service-order-item
     * Body: JSON ServiceOrderItemsModel
     */
    public function actionAddServiceOrderItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        if (empty($payload['OrganizationCode'])) {
            $payload['OrganizationCode'] = $this->getOrgCode();
        }

        try {
            $client = new MomentusClient();
            return $client->addServiceOrderItem($payload);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * PUT|POST /momentus/update-service-order-item
     * Body: JSON ServiceOrderItemsModel
     * Requires: org_code (or default), order_number, order_line_number in body or query.
     */
    public function actionUpdateServiceOrderItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        $orgCode = !empty($payload['OrganizationCode'])
            ? (string) $payload['OrganizationCode']
            : $this->getOrgCode();
        $orderNumber = isset($payload['OrderNumber'])
            ? $payload['OrderNumber']
            : Yii::$app->request->get('order_number');
        $orderLineNumber = isset($payload['OrderLineNumber'])
            ? $payload['OrderLineNumber']
            : Yii::$app->request->get('order_line_number');

        if ($orderNumber === null || $orderLineNumber === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'OrderNumber and OrderLineNumber are required.'];
        }

        try {
            $client = new MomentusClient();
            return $client->updateServiceOrderItem($orgCode, $orderNumber, $orderLineNumber, $payload);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * DELETE|POST /momentus/delete-service-order-item?org_code=...&order_number=...&order_line_number=...
     */
    public function actionDeleteServiceOrderItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $orderNumber = Yii::$app->request->get('order_number', Yii::$app->request->post('order_number'));
        $orderLineNumber = Yii::$app->request->get('order_line_number', Yii::$app->request->post('order_line_number'));

        if ($orderNumber === null || $orderLineNumber === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'order_number and order_line_number are required.'];
        }

        try {
            $client = new MomentusClient();
            $client->deleteServiceOrderItem($orgCode, $orderNumber, $orderLineNumber);
            return ['success' => true];
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * POST /momentus/save-new-item-to-package
     * Body: JSON SaveNewItemToExistingPackageModel
     */
    public function actionSaveNewItemToPackage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        if (empty($payload['OrganizationCode'])) {
            $payload['OrganizationCode'] = $this->getOrgCode();
        }

        try {
            $client = new MomentusClient();
            return $client->saveNewItemToExistingPackage($payload);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // Functions
    // ---------------------------------------------------------------

    /**
     * GET /momentus/list-functions?org_code=...&event_id=...&ODataQuery=...
     *
     * Returns functions. If event_id is provided, filters to that event.
     */
    public function actionListFunctions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $eventId = Yii::$app->request->get('event_id');
        $odataQuery = trim((string) Yii::$app->request->get('ODataQuery', ''));

        if ($odataQuery === '' && $eventId !== null && $eventId !== '') {
            $odataQuery = '$filter=OrganizationCode eq \'' . addslashes($orgCode)
                . '\' and EventID eq ' . (int) $eventId;
        }

        try {
            $client = new MomentusClient();
            $result = $client->listFunctions($odataQuery ?: null, $orgCode);

            $items = isset($result['value']) ? $result['value'] : (is_array($result) ? $result : []);

            return $items;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // Price Lists + Items
    // ---------------------------------------------------------------

    /**
     * GET /momentus/list-price-lists?org_code=...&ODataQuery=...
     *
     * Returns all price lists for the org. Filters out retired lists by default.
     * Pass include_retired=1 to include retired price lists.
     */
    public function actionListPriceLists()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $odataQuery = trim((string) Yii::$app->request->get('ODataQuery', ''));
        $includeRetired = Yii::$app->request->get('include_retired', '0');

        try {
            $client = new MomentusClient();
            $result = $client->listPriceLists($odataQuery ?: null, $orgCode);

            $items = isset($result['value']) ? $result['value'] : (is_array($result) ? $result : []);

            if ($includeRetired !== '1') {
                $items = array_values(array_filter($items, function ($pl) {
                    return !isset($pl['Retire']) || $pl['Retire'] !== 'Y';
                }));
            }

            return $items;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * GET /momentus/search-price-list-items?q=...&org_code=...
     */
    public function actionSearchPriceListItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $search = trim((string) Yii::$app->request->get('q', ''));
        $orgCode = $this->getOrgCode();

        try {
            $client = new MomentusClient();
            return $client->searchPriceListItems($search, $orgCode);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * GET /momentus/get-price-list-item?price_list=...&sequence=...&org_code=...
     */
    public function actionGetPriceListItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $priceList = Yii::$app->request->get('price_list');
        $sequence = Yii::$app->request->get('sequence');

        if ($priceList === null || $sequence === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'price_list and sequence are required.'];
        }

        try {
            $client = new MomentusClient();
            return $client->getPriceListItem($orgCode, $priceList, $sequence);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * POST /momentus/add-price-list-item
     * Body: JSON PriceListItemsModel
     */
    public function actionAddPriceListItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        if (empty($payload['OrganizationCode'])) {
            $payload['OrganizationCode'] = $this->getOrgCode();
        }

        try {
            $client = new MomentusClient();
            return $client->addPriceListItem($payload);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * PUT|POST /momentus/update-price-list-item
     * Body: JSON PriceListItemsModel
     */
    public function actionUpdatePriceListItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        $orgCode = !empty($payload['OrganizationCode'])
            ? (string) $payload['OrganizationCode']
            : $this->getOrgCode();
        $priceList = isset($payload['PriceList']) ? $payload['PriceList'] : null;
        $sequence = isset($payload['Sequence']) ? $payload['Sequence'] : null;

        if ($priceList === null || $sequence === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'PriceList and Sequence are required in the body.'];
        }

        try {
            $client = new MomentusClient();
            return $client->updatePriceListItem($orgCode, $priceList, $sequence, $payload);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * DELETE|POST /momentus/delete-price-list-item?price_list=...&sequence=...&org_code=...
     */
    public function actionDeletePriceListItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $priceList = Yii::$app->request->get('price_list', Yii::$app->request->post('price_list'));
        $sequence = Yii::$app->request->get('sequence', Yii::$app->request->post('sequence'));

        if ($priceList === null || $sequence === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'price_list and sequence are required.'];
        }

        try {
            $client = new MomentusClient();
            $client->deletePriceListItem($orgCode, $priceList, $sequence);
            return ['success' => true];
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * GET /momentus/get-price-list-items-by-list?price_list=EDDEMO&org_code=...
     *
     * Returns all price list items for a specific price list code.
     * Tries REST endpoint first (/PriceListItems/{OrgCode}/{PriceList}),
     * falls back to OData (/odata/PriceListItems) if REST returns 404.
     */
    public function actionGetPriceListItemsByList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();
        $priceList = trim((string) Yii::$app->request->get('price_list', ''));

        if ($priceList === '') {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'price_list is required.'];
        }

        try {
            $client = new MomentusClient();
            $result = $client->listPriceListItemsByCode($priceList, $orgCode);
            $items = $this->extractItems(is_array($result) ? $result : []);

            return $items;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // Event Space Diagram URL
    // ---------------------------------------------------------------

    /**
     * POST /momentus/update-event-space-diagram-url
     * Body: {
     *   "eventdraw_event_id": 187763,
     *   "event_space_diagram_id": 2110,   (optional – defaults to 2110 if not supplied)
     *   "org_code": "10"                  (optional)
     * }
     *
     * Writes the EventDraw floor plan URL into the EventdrawDiagramUrl field of
     * the given Momentus EventSpaceDiagram via PUT /EventSpaceDiagrams/{ID}.
     *
     * Important notes (from Momentus API docs):
     *  - The ID in the request body must match the ID in the URL path parameter.
     *  - A GET is performed first to retrieve current values; only EventdrawDiagramUrl is changed.
     *  - Read-only fields (EnteredBy, EnteredOn, ChangedBy, ChangedOn) are ignored on update.
     */
    /**
     * GET /momentus/get-event-space-diagram?id=2111&org_code=10
     *
     * Returns the Momentus EventSpaceDiagram record for the given ID.
     * The frontend uses DefaultOrderFunctionID and DefaultOrderPriceList
     * to pre-select the defaults in the Create Service Order dialog.
     */
    public function actionGetEventSpaceDiagram()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        Yii::$app->response->headers->set('Pragma', 'no-cache');

        $id = (int) Yii::$app->request->get('id', 0);
        if ($id <= 0) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'id is required and must be a positive integer.'];
        }

        try {
            $client = new MomentusClient();
            $result = $client->getEventSpaceDiagram($id);
            return $result;
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionUpdateEventSpaceDiagramUrl()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            $payload = Yii::$app->request->post();
        }

        $eventdrawEventId    = isset($payload['eventdraw_event_id']) ? $payload['eventdraw_event_id'] : null;
        $eventSpaceDiagramId = isset($payload['event_space_diagram_id']) ? (int) $payload['event_space_diagram_id'] : 0;
        $orgCode             = isset($payload['org_code']) ? (string) $payload['org_code'] : $this->getOrgCode();

        if ($eventdrawEventId === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'eventdraw_event_id is required.'];
        }

        if ($eventSpaceDiagramId <= 0) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'event_space_diagram_id is required and must be a positive integer.'];
        }

        // Prefer the URL provided by the caller (e.g. the check-guid URL from momentus-copy)
        // so the link is server-aware. Fall back to a plain EventID URL if not supplied.
        if (!empty($payload['eventdraw_url'])) {
            $eventdrawUrl = (string) $payload['eventdraw_url'];
        } else {
            $frontendBase = rtrim((string) (\Yii::$app->params['frontendBaseUrl'] ?? 'https://momentusstaging.eventdrawus.com/frontend/web/site'), '/');
            $eventdrawUrl = $frontendBase . '/eventdraw?EventID=' . rawurlencode((string) $eventdrawEventId);
        }

        try {
            $client = new MomentusClient();
            $result = $client->updateEventSpaceDiagramUrl($eventSpaceDiagramId, $eventdrawUrl, $orgCode);
            return [
                'success' => true,
                'url' => $eventdrawUrl,
                'event_space_diagram_id' => $eventSpaceDiagramId,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // PNG Export to S3
    // ---------------------------------------------------------------

    /**
     * POST /momentus/save-event-png
     * Body: { "eventdraw_event_id": 187763, "image": "data:image/png;base64,..." }
     *
     * Saves a floor plan PNG to s3://eventdraw-public/test_folder/{EventID}.png
     */
    public function actionSaveEventPng()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $eventId = Yii::$app->request->post('eventdraw_event_id');
        $imageData = Yii::$app->request->post('image');

        if ($eventId === null || $imageData === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'eventdraw_event_id and image are required.'];
        }

        if (strpos($imageData, 'base64,') !== false) {
            $imageData = substr($imageData, strpos($imageData, 'base64,') + 7);
        }
        $binaryData = base64_decode($imageData);
        if ($binaryData === false) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid base64 image data.'];
        }

        try {
            $s3 = Yii::$app->get('s3');
            $s3Key = 'test_folder/' . $eventId . '.png';
            $s3->commands()->put($s3Key, $binaryData)
                ->inBucket('eventdraw-public')
                ->withContentType('image/png')
                ->withAcl('public-read')
                ->execute();

            $publicUrl = 'https://eventdraw-public.s3.ap-southeast-2.amazonaws.com/' . $s3Key;

            return ['success' => true, 'url' => $publicUrl];
        } catch (\Exception $e) {
            Yii::error('S3 PNG upload failed: ' . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Failed to upload PNG: ' . $e->getMessage()];
        }
    }
    
    /**
     * Store rendered floorplan SVG on the public bucket for direct HTTPS access:
     * https://eventdraw-public.s3.ap-southeast-2.amazonaws.com/test_folder/{EventID}.svg
     */
    public function saveEventSvg_S3($eventID, $svgContent)
    {
        if ($eventID <= 0 || $svgContent === null || $svgContent === '') {
            return false;
        }
        $s3 = Yii::$app->get('s3');
        $key = 'test_folder/' . strval((int) $eventID) . '.svg';
        $s3->commands()
            ->put($key, $svgContent)
            ->inBucket('eventdraw-public')
            ->withAcl('public-read')
            ->withContentType('image/svg+xml')
            ->execute();

        return 'https://eventdraw-public.s3.ap-southeast-2.amazonaws.com/' . $key;
    }

    /**
     * POST: eventid, userid, multipart field "svg" (or post body svg). Background client upload after save.
     */
    public function actionSaveEventSvgS3()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $eventid = (int) $request->post('eventid');
        $userid = (int) $request->post('userid');

        $svg = '';
        if (!empty($_FILES['svg']) && UPLOAD_ERR_OK === (int) $_FILES['svg']['error']) {
            $svg = file_get_contents($_FILES['svg']['tmp_name']);
        }
        if ($svg === '' || $svg === false) {
            $svg = (string) $request->post('svg', '');
        }

        if ($eventid <= 0 || $userid <= 0) {
            return ['ok' => false, 'error' => 'bad_params'];
        }
        if ($svg === '') {
            return ['ok' => false, 'error' => 'empty_svg'];
        }

        $evnt = Event::findOne(['id' => $eventid]);
        if (!$evnt || (int) $evnt->userid !== $userid) {
            return ['ok' => false, 'error' => 'forbidden'];
        }

        try {
            $url = $this->saveEventSvg_S3($eventid, $svg);
            return ['ok' => true, 'url' => $url];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------
    // Service Order Upsert + Mapping + Sync
    // ---------------------------------------------------------------

    /**
     * POST /momentus/upsert-service-order
     * Body: {
     *   "eventdraw_space_diagram_id": 2114,   // EventSpaceDiagramID — one SO per Room Diagram
     *   "eventdraw_event_id": 187763,          // optional, stored for reference
     *   "momentus_event_id": 9427,
     *   "momentus_function_id": 123,
     *   "price_list": "PRICELIST01",
     *   "org_code": "10",
     *   "account": "ACCT001",               // optional
     *   "bill_to_account": "ACCT001",       // optional
     *   "requester_account": "ACCT001",     // optional
     *   "po_number": "PO-123",              // optional
     *   "order_status": "OP",               // optional
     *   "order_date": "2026-03-18T00:00:00Z"// optional
     * }
     *
     * Behavior:
     * - If the Room Diagram has no mapped order: create Service Order (POST /ServiceOrders).
     * - If a mapped order already exists for this diagram: update it (PUT /ServiceOrders/{OrgCode}/{OrderNumber}).
     * - Keeps local mapping table in sync, keyed by eventdraw_space_diagram_id.
     */
    public function actionUpsertServiceOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        $spaceDiagramId   = isset($payload['eventdraw_space_diagram_id']) ? (int) $payload['eventdraw_space_diagram_id'] : 0;
        $eventdrawEventId = isset($payload['eventdraw_event_id']) ? (int) $payload['eventdraw_event_id'] : 0;
        $momentusEventId = isset($payload['momentus_event_id']) ? (int) $payload['momentus_event_id'] : 0;
        $momentusFunctionId = isset($payload['momentus_function_id']) ? (int) $payload['momentus_function_id'] : 0;
        $orgCode = isset($payload['org_code']) ? (string) $payload['org_code'] : $this->getOrgCode();
        $priceList = isset($payload['price_list']) ? trim((string) $payload['price_list']) : '';

        $account = isset($payload['account']) ? trim((string) $payload['account']) : '';
        if ($account === '' && isset($payload['account_code'])) {
            $account = trim((string) $payload['account_code']);
        }
        // Temporary business rule: always use hardcoded Bill-To account.
        $billToAccount = '0001271';
        $account = '0001271';
        $requesterAccount = isset($payload['requester_account']) ? trim((string) $payload['requester_account']) : '';
        $poNumber = isset($payload['po_number']) ? trim((string) $payload['po_number']) : '';
        $orderStatus = isset($payload['order_status']) ? trim((string) $payload['order_status']) : '';
        $orderDate = isset($payload['order_date']) ? trim((string) $payload['order_date']) : '';
        $department = isset($payload['department']) ? trim((string) $payload['department']) : '';
        $taxable = isset($payload['taxable']) ? trim((string) $payload['taxable']) : '';

        if (!$spaceDiagramId || !$momentusEventId || !$momentusFunctionId || $priceList === '') {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'eventdraw_space_diagram_id, momentus_event_id, momentus_function_id, and price_list are required.'];
        }
        if ($orgCode === '') {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'org_code is required (or configure params[momentus][orgCode]).'];
        }

        $client = new MomentusClient();
        $mapping = MomentusServiceOrder::findBySpaceDiagram($spaceDiagramId, $orgCode);

        try {
            if ($mapping !== null) {
                // Verify the Momentus order's Event matches the expected one.
                $orderNumber = (int) $mapping->momentus_order_number;
                $eventMismatch = false;
                try {
                    $existingSO = $client->getServiceOrder($orgCode, $orderNumber);
                    if (is_array($existingSO) && isset($existingSO['Event']) && (int) $existingSO['Event'] !== $momentusEventId) {
                        $eventMismatch = true;
                    }
                } catch (\Exception $checkEx) {
                    if (strpos($checkEx->getMessage(), '404') !== false) {
                        $eventMismatch = true;
                    }
                }

                if ($eventMismatch) {
                    $mapping->status = 'deleted';
                    $mapping->save(false);
                    $mapping = null;
                }
            }

            if ($mapping !== null) {
                // Existing mapped order -> update.
                $orderNumber = (int) $mapping->momentus_order_number;
                $updatePayload = [
                    'OrganizationCode' => $orgCode,
                    'OrderNumber' => $orderNumber,
                    'BillToAccount' => $billToAccount,
                ];
                if ($account !== '') {
                    $updatePayload['Account'] = $account;
                }
                if ($requesterAccount !== '') {
                    $updatePayload['RequesterAccount'] = $requesterAccount;
                }
                if ($poNumber !== '') {
                    $updatePayload['PONumber'] = $poNumber;
                }
                if ($orderStatus !== '') {
                    $updatePayload['OrderStatus'] = $orderStatus;
                }
                if ($orderDate !== '') {
                    $updatePayload['OrderDate'] = $orderDate;
                }
                if ($department !== '') {
                    $updatePayload['Department'] = $department;
                }
                if ($taxable !== '') {
                    $updatePayload['Taxable'] = $taxable;
                }

                $serviceOrderResponse = $client->updateServiceOrder($orgCode, $orderNumber, $updatePayload);

                $mapping->eventdraw_event_id = $eventdrawEventId ?: null;
                $mapping->momentus_event_id = $momentusEventId;
                $mapping->momentus_function_id = $momentusFunctionId;
                $mapping->price_list = $priceList;
                if (!$mapping->save()) {
                    Yii::$app->response->statusCode = 422;
                    return ['error' => 'Failed to update service order mapping.', 'details' => $mapping->getErrors()];
                }

                return [
                    'success' => true,
                    'created' => false,
                    'updated' => true,
                    'order_number' => $orderNumber,
                    'org_code' => $orgCode,
                    'service_order' => $serviceOrderResponse,
                ];
            }

            // No mapped order -> create.
            $createPayload = [
                'OrganizationCode' => $orgCode,
                'Event' => $momentusEventId,
                'Function' => $momentusFunctionId,
                'PriceList' => $priceList,
                'BillToAccount' => $billToAccount,
            ];
            if ($account !== '') {
                $createPayload['Account'] = $account;
            }
            if ($requesterAccount !== '') {
                $createPayload['RequesterAccount'] = $requesterAccount;
            }
            if ($poNumber !== '') {
                $createPayload['PONumber'] = $poNumber;
            }
            if ($orderStatus !== '') {
                $createPayload['OrderStatus'] = $orderStatus;
            }
            if ($orderDate !== '') {
                $createPayload['OrderDate'] = $orderDate;
            } else {
                $createPayload['OrderDate'] = gmdate('c');
            }
            if ($department !== '') {
                $createPayload['Department'] = $department;
            }
            if ($taxable !== '') {
                $createPayload['Taxable'] = $taxable;
            }

            $createResponse = $client->addServiceOrder($createPayload);
            
            $orderNumber = $this->extractServiceOrderNumber($createResponse);
            if (!$orderNumber) {
                // Fallback: try to locate the newest SO for this Event/Function/Org.
                try {
                    $odataQuery = '$filter=Event eq ' . (int) $momentusEventId
                        . ' and Function eq ' . (int) $momentusFunctionId
                        . " and OrganizationCode eq '" . addslashes($orgCode) . "'"
                        . '&$orderby=OrderNumber desc&$top=1';
                    $searchResponse = $client->listServiceOrders($odataQuery);
                    $items = isset($searchResponse['value']) && is_array($searchResponse['value'])
                        ? $searchResponse['value']
                        : (is_array($searchResponse) ? $searchResponse : []);
                    if (isset($items[0]) && is_array($items[0]) && isset($items[0]['OrderNumber']) && is_numeric($items[0]['OrderNumber'])) {
                        $orderNumber = (int) $items[0]['OrderNumber'];
                    }
                } catch (\Exception $lookupEx) {
                    Yii::warning('Could not resolve created order number from OData fallback: ' . $lookupEx->getMessage(), __METHOD__);
                }
            }
            if (!$orderNumber) {
                Yii::$app->response->statusCode = 502;
                return [
                    'error' => 'Service order created but OrderNumber could not be resolved.',
                    'service_order' => $createResponse,
                ];
            }

            $mappingRow = MomentusServiceOrder::findAnyBySpaceDiagram($spaceDiagramId, $orgCode);
            if ($mappingRow !== null) {
                $mappingRow->eventdraw_event_id = $eventdrawEventId ?: null;
                $mappingRow->momentus_event_id = $momentusEventId;
                $mappingRow->momentus_function_id = $momentusFunctionId;
                $mappingRow->momentus_order_number = (int) $orderNumber;
                $mappingRow->price_list = $priceList;
                $mappingRow->status = 'active';
                if (!$mappingRow->save()) {
                    Yii::$app->response->statusCode = 422;
                    return ['error' => 'Failed to save service order mapping.', 'details' => $mappingRow->getErrors()];
                }
            } else {
                $newMapping = new MomentusServiceOrder();
                $newMapping->eventdraw_space_diagram_id = $spaceDiagramId;
                $newMapping->eventdraw_event_id = $eventdrawEventId ?: null;
                $newMapping->momentus_event_id = $momentusEventId;
                $newMapping->momentus_function_id = $momentusFunctionId;
                $newMapping->momentus_order_number = (int) $orderNumber;
                $newMapping->org_code = $orgCode;
                $newMapping->price_list = $priceList;

                if (!$newMapping->save()) {
                    Yii::$app->response->statusCode = 422;
                    return ['error' => 'Failed to save service order mapping.', 'details' => $newMapping->getErrors()];
                }
            }

            return [
                'success' => true,
                'created' => true,
                'updated' => false,
                'order_number' => (int) $orderNumber,
                'org_code' => $orgCode,
                'service_order' => $createResponse,
            ];
        } catch (\Exception $e) {
            Yii::error('Failed to upsert service order: ' . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Extract OrderNumber from Momentus create response.
     */
    private function extractServiceOrderNumber($response)
    {
        if (!is_array($response)) {
            return null;
        }
        if (isset($response['OrderNumber']) && is_numeric($response['OrderNumber'])) {
            return (int) $response['OrderNumber'];
        }
        if (isset($response['orderNumber']) && is_numeric($response['orderNumber'])) {
            return (int) $response['orderNumber'];
        }
        if (isset($response['value']) && is_array($response['value']) && isset($response['value'][0])) {
            $first = $response['value'][0];
            if (is_array($first) && isset($first['OrderNumber']) && is_numeric($first['OrderNumber'])) {
                return (int) $first['OrderNumber'];
            }
        }
        return null;
    }

    /**
     * GET /momentus/get-event-service-order?eventdraw_space_diagram_id=...&org_code=...
     *
     * Checks if a Momentus service order already exists for this Room Diagram.
     * Falls back to eventdraw_event_id for legacy callers that have not yet been
     * updated.  If found, also fetches current items from Momentus.
     */
    public function actionGetEventServiceOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        Yii::$app->response->headers->set('Pragma', 'no-cache');

        $spaceDiagramId   = Yii::$app->request->get('eventdraw_space_diagram_id');
        $eventdrawEventId = Yii::$app->request->get('eventdraw_event_id');
        $orgCode = $this->getOrgCode();

        if ($spaceDiagramId === null && $eventdrawEventId === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'eventdraw_space_diagram_id is required.'];
        }

        if ($spaceDiagramId !== null) {
            $mapping = MomentusServiceOrder::findBySpaceDiagram($spaceDiagramId, $orgCode);
        } else {
            // Legacy fallback
            $mapping = MomentusServiceOrder::findByEventDrawEvent($eventdrawEventId, $orgCode);
        }

        if ($mapping === null) {
            return [
                'exists' => false,
                'order_number' => null,
                'items' => [],
            ];
        }

        $client = new MomentusClient();

        try {
            $client->getServiceOrder($orgCode, $mapping->momentus_order_number);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '404') !== false) {
                $mapping->status = 'deleted';
                if (!$mapping->save(false)) {
                    Yii::warning('Could not mark orphaned SO mapping deleted.', __METHOD__);
                }
                return [
                    'exists' => false,
                    'order_number' => null,
                    'items' => [],
                    'unlinked' => true,
                    'reason' => 'Service order no longer exists in Momentus; local link was cleared.',
                ];
            }
            Yii::error('Failed to verify service order in Momentus: ' . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 502;
            return ['error' => $e->getMessage()];
        }

        $expectedOrderNumber = (int) $mapping->momentus_order_number;

        try {
            // Match actionSyncServiceOrderItems: filter by order + org only. Requiring Event eq
            // momentus_event_id fails when the DB mapping event id is stale or line items use
            // a different Event value than stored, which still yields a valid service order from
            // getServiceOrder but an empty OData result.
            $odataFilter = '$filter=OrderNumber eq ' . (int) $mapping->momentus_order_number
                . " and OrganizationCode eq '" . addslashes($orgCode) . "'";
            $itemsResult = $client->listServiceOrderItems($odataFilter);
            $items = isset($itemsResult['value']) ? $itemsResult['value'] : (is_array($itemsResult) ? $itemsResult : []);
        } catch (\Exception $e) {
            Yii::error('Failed to fetch SO items: ' . $e->getMessage(), __METHOD__);
            $items = [];
        }

        $items = $this->aggregateServiceOrderItemsByResourceCode($items);

        if (empty($items)) {
            return [
                'exists' => false,
                'order_number' => (int) $mapping->momentus_order_number,
                'momentus_event_id' => (int) $mapping->momentus_event_id,
                'momentus_function_id' => $mapping->momentus_function_id ? (int) $mapping->momentus_function_id : null,
                'price_list' => $mapping->price_list,
                'org_code' => $mapping->org_code,
                'created_at' => $mapping->created_at,
                'items' => [],
                'reason' => 'No service order items found for order ' . (int) $mapping->momentus_order_number
                    . ' (org ' . $mapping->org_code . ').',
            ];
        }

        return [
            'exists' => true,
            'order_number' => (int) $mapping->momentus_order_number,
            'momentus_event_id' => (int) $mapping->momentus_event_id,
            'momentus_function_id' => $mapping->momentus_function_id ? (int) $mapping->momentus_function_id : null,
            'price_list' => $mapping->price_list,
            'org_code' => $mapping->org_code,
            'created_at' => $mapping->created_at,
            'items' => $items,
        ];
    }

    /**
     * POST /momentus/save-service-order-mapping
     * Body: {
     *   "eventdraw_space_diagram_id": 2114,    // Room Diagram ID (primary key)
     *   "eventdraw_event_id": 187763,           // optional, stored for reference
     *   "momentus_event_id": 9427,
     *   "momentus_function_id": 123,
     *   "momentus_order_number": 132598,
     *   "org_code": "10",
     *   "price_list": "Corporate List 2026"
     * }
     *
     * Stores the link between an EventDraw Room Diagram and a Momentus service
     * order.  Called after a service order is created manually.
     */
    public function actionSaveServiceOrderMapping()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            $payload = Yii::$app->request->post();
        }

        $spaceDiagramId   = isset($payload['eventdraw_space_diagram_id']) ? (int) $payload['eventdraw_space_diagram_id'] : null;
        $eventdrawEventId = isset($payload['eventdraw_event_id']) ? (int) $payload['eventdraw_event_id'] : null;
        $momentusEventId = isset($payload['momentus_event_id']) ? (int) $payload['momentus_event_id'] : null;
        $momentusFunctionId = isset($payload['momentus_function_id']) ? (int) $payload['momentus_function_id'] : null;
        $orderNumber = isset($payload['momentus_order_number']) ? (int) $payload['momentus_order_number'] : null;
        $orgCode = isset($payload['org_code']) ? (string) $payload['org_code'] : $this->getOrgCode();
        $priceList = isset($payload['price_list']) ? (string) $payload['price_list'] : null;

        if (!$spaceDiagramId || !$momentusEventId || !$orderNumber) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'eventdraw_space_diagram_id, momentus_event_id, and momentus_order_number are required.'];
        }

        $existing = MomentusServiceOrder::findAnyBySpaceDiagram($spaceDiagramId, $orgCode);
        if ($existing !== null) {
            $existing->momentus_order_number = $orderNumber;
            $existing->momentus_event_id = $momentusEventId;
            $existing->momentus_function_id = $momentusFunctionId;
            $existing->price_list = $priceList;
            $existing->status = 'active';
            if (!$existing->save()) {
                Yii::$app->response->statusCode = 422;
                return ['error' => 'Failed to update mapping.', 'details' => $existing->getErrors()];
            }
            return ['success' => true, 'id' => $existing->id, 'updated' => true];
        }

        $model = new MomentusServiceOrder();
        $model->eventdraw_space_diagram_id = $spaceDiagramId;
        $model->eventdraw_event_id = $eventdrawEventId ?: null;
        $model->momentus_event_id = $momentusEventId;
        $model->momentus_function_id = $momentusFunctionId;
        $model->momentus_order_number = $orderNumber;
        $model->org_code = $orgCode;
        $model->price_list = $priceList;

        if (!$model->save()) {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Failed to save mapping.', 'details' => $model->getErrors()];
        }

        return ['success' => true, 'id' => $model->id, 'created' => true];
    }

    /**
     * POST /momentus/sync-service-order-items
     * Body: {
     *   "order_number": 132598,
     *   "org_code": "10",
     *   "momentus_event_id": 9427,
     *   "momentus_function_id": 123,
     *   "items": [
     *     { "resource_code": "TBL6FT", "units": 20, "description": "6ft Round Table", "price_list_detail_seq_nbr": 1481 },
     *     { "resource_code": "CHR_BQ", "units": 33, "description": "Banquet Chair" }
     *   ]
     * }
     *
     * Syncs diagram items with the existing Momentus service order:
     * - Items in diagram but not in SO → add (POST)
     * - Items in both with changed qty → update (PUT)
     * - Items in SO but not in diagram → delete (DELETE)
     */
    public function actionSyncServiceOrderItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $payload = $this->getJsonBody();
        if ($payload === null) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Invalid or missing JSON body.'];
        }

        $orderNumber = isset($payload['order_number']) ? (int) $payload['order_number'] : null;
        $orgCode = isset($payload['org_code']) ? (string) $payload['org_code'] : $this->getOrgCode();
        $momentusEventId = isset($payload['momentus_event_id']) ? (int) $payload['momentus_event_id'] : null;
        $momentusFunctionId = isset($payload['momentus_function_id']) ? (int) $payload['momentus_function_id'] : null;
        $orderPriceList = isset($payload['price_list']) ? trim((string) $payload['price_list']) : '';
        $diagramItems = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
        $startDate = isset($payload['start_date']) ? trim((string) $payload['start_date']) : '';
        $endDate = isset($payload['end_date']) ? trim((string) $payload['end_date']) : '';

        if (!$orderNumber) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'order_number is required.'];
        }

        $client = new MomentusClient();
        $results = ['added' => [], 'updated' => [], 'deleted' => [], 'skipped' => [], 'errors' => []];

        // 0) Resolve StartDate/EndDate from the function if not provided
        if (($startDate === '' || $endDate === '') && $momentusEventId && $momentusFunctionId) {
            try {
                $fnOdata = '$filter=OrganizationCode eq \'' . addslashes($orgCode)
                    . '\' and EventID eq ' . (int) $momentusEventId
                    . ' and FunctionID eq ' . (int) $momentusFunctionId;
                $fnResult = $client->listFunctions($fnOdata, $orgCode);
                $fnItems = isset($fnResult['value']) ? $fnResult['value'] : (is_array($fnResult) ? $fnResult : []);
                if (!empty($fnItems[0])) {
                    $fn = $fnItems[0];
                    if ($startDate === '' && !empty($fn['StartDate'])) {
                        $startDate = (string) $fn['StartDate'];
                    }
                    if ($endDate === '' && !empty($fn['EndDate'])) {
                        $endDate = (string) $fn['EndDate'];
                    }
                }
            } catch (\Exception $e) {
                Yii::warning('Failed to fetch function dates: ' . $e->getMessage(), __METHOD__);
            }
        }
        if ($startDate === '') {
            $startDate = gmdate('Y-m-d\TH:i:s\Z');
        }
        if ($endDate === '') {
            $endDate = $startDate;
        }

        // 1) Fetch current SO items from Momentus
        $currentItems = [];
        try {
            $odataFilter = '$filter=OrderNumber eq ' . $orderNumber
                . " and OrganizationCode eq '" . addslashes($orgCode) . "'";
            $response = $client->listServiceOrderItems($odataFilter);
            $currentItems = isset($response['value']) ? $response['value'] : (is_array($response) ? $response : []);
        } catch (\Exception $e) {
            Yii::error('Failed to fetch SO items for sync: ' . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Failed to fetch current service order items: ' . $e->getMessage()];
        }

        // 2) Build lookup of current SO items by ResourceCode
        $currentByResource = [];
        foreach ($currentItems as $item) {
            $rc = isset($item['ResourceCode']) ? (string) $item['ResourceCode'] : '';
            if ($rc !== '') {
                $currentByResource[$rc] = $item;
            }
        }

        // 3) Build lookup of diagram items by resource_code
        $diagramByResource = [];
        foreach ($diagramItems as $di) {
            $rc = isset($di['resource_code']) ? (string) $di['resource_code'] : '';
            if ($rc !== '') {
                $diagramByResource[$rc] = $di;
            }
        }

        // 4) Items in diagram but not in SO → add
        foreach ($diagramByResource as $resourceCode => $di) {
            if (!isset($currentByResource[$resourceCode])) {
                $addPayload = [
                    'OrganizationCode' => $orgCode,
                    'OrderNumber' => $orderNumber,
                    'ResourceCode' => $resourceCode,
                    'Units' => isset($di['units']) ? (int) $di['units'] : 1,
                    'Description' => isset($di['description']) ? (string) $di['description'] : '',
                    'StartDate' => $startDate,
                    'EndDate' => $endDate,
                ];
                if ($momentusEventId) {
                    $addPayload['Event'] = $momentusEventId;
                }
                if ($momentusFunctionId) {
                    $addPayload['Function'] = $momentusFunctionId;
                }
                $itemPriceList = '';
                if (!empty($di['price_list'])) {
                    $itemPriceList = trim((string) $di['price_list']);
                } elseif ($orderPriceList !== '') {
                    $itemPriceList = $orderPriceList;
                }
                if ($itemPriceList !== '') {
                    $addPayload['PriceList'] = $itemPriceList;
                }
                if (!empty($di['price_list_detail_seq_nbr'])) {
                    $addPayload['PriceListDetailSeqNbr'] = (int) $di['price_list_detail_seq_nbr'];
                }
                if (!isset($addPayload['PriceListDetailSeqNbr']) && $itemPriceList !== '') {
                    $resolvedSeq = $this->resolvePriceListDetailSeqNbr($client, $orgCode, $itemPriceList, $resourceCode);
                    if ($resolvedSeq !== null) {
                        $addPayload['PriceListDetailSeqNbr'] = (int) $resolvedSeq;
                    }
                }
                if (!isset($addPayload['PriceListDetailSeqNbr'])) {
                    $results['errors'][] = [
                        'action' => 'add',
                        'resource_code' => $resourceCode,
                        'error' => 'Could not resolve PriceListDetailSeqNbr for resource ' . $resourceCode
                            . ' using price list ' . ($itemPriceList !== '' ? $itemPriceList : '(none)') . '.',
                    ];
                    continue;
                }

                try {
                    $result = $client->addServiceOrderItem($addPayload);
                    $results['added'][] = [
                        'resource_code' => $resourceCode,
                        'units' => $addPayload['Units'],
                        'response' => $result,
                    ];
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'action' => 'add',
                        'resource_code' => $resourceCode,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        // 5) Items in both → update if qty changed
        foreach ($diagramByResource as $resourceCode => $di) {
            if (isset($currentByResource[$resourceCode])) {
                $currentItem = $currentByResource[$resourceCode];
                $newUnits = isset($di['units']) ? (int) $di['units'] : 1;
                $oldUnits = isset($currentItem['Units']) ? (int) $currentItem['Units'] : 0;
                $orderLineNumber = isset($currentItem['OrderLineNumber']) ? (int) $currentItem['OrderLineNumber'] : null;

                if ($newUnits !== $oldUnits && $orderLineNumber !== null) {
                    $updatePayload = [
                        'OrganizationCode' => $orgCode,
                        'OrderNumber' => $orderNumber,
                        'OrderLineNumber' => $orderLineNumber,
                        'Units' => $newUnits,
                    ];

                    try {
                        $result = $client->updateServiceOrderItem($orgCode, $orderNumber, $orderLineNumber, $updatePayload);
                        $results['updated'][] = [
                            'resource_code' => $resourceCode,
                            'old_units' => $oldUnits,
                            'new_units' => $newUnits,
                            'order_line_number' => $orderLineNumber,
                            'response' => $result,
                        ];
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'action' => 'update',
                            'resource_code' => $resourceCode,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }
        }

        // 6) Items in SO but not in diagram → delete (excludes system tax/gratuity at fetch; see catch for API guard)
        foreach ($currentByResource as $resourceCode => $currentItem) {
            if (!isset($diagramByResource[$resourceCode])) {
                $orderLineNumber = isset($currentItem['OrderLineNumber']) ? (int) $currentItem['OrderLineNumber'] : null;
                if ($orderLineNumber !== null) {
                    try {
                        $client->deleteServiceOrderItem($orgCode, $orderNumber, $orderLineNumber);
                        $results['deleted'][] = [
                            'resource_code' => $resourceCode,
                            'order_line_number' => $orderLineNumber,
                        ];
                    } catch (\Exception $e) {
                        if ($this->isServiceOrderItemNonDeletableException($e)) {
                            $results['skipped'][] = [
                                'action' => 'delete',
                                'resource_code' => $resourceCode,
                                'order_line_number' => $orderLineNumber,
                                'reason' => 'non_deletable',
                                'message' => $e->getMessage(),
                            ];
                        } else {
                            $results['errors'][] = [
                                'action' => 'delete',
                                'resource_code' => $resourceCode,
                                'error' => $e->getMessage(),
                            ];
                        }
                    }
                }
            }
        }

        $hasErrors = !empty($results['errors']);
        return [
            'success' => !$hasErrors,
            'order_number' => $orderNumber,
            'summary' => [
                'added' => count($results['added']),
                'updated' => count($results['updated']),
                'deleted' => count($results['deleted']),
                'skipped' => count($results['skipped']),
                'errors' => count($results['errors']),
            ],
            'details' => $results,
        ];
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * True when Momentus will not allow deleting a line (e.g. system-calculated tax/gratuity).
     */
    private function isServiceOrderItemNonDeletableException(\Exception $e)
    {
        $m = (string) $e->getMessage();
        if ($m === '') {
            return false;
        }
        if (stripos($m, 'system calculated') === false) {
            return false;
        }
        return (stripos($m, 'tax') !== false || stripos($m, 'gratuit') !== false);
    }

    /**
     * Parse JSON request body.
     */
    private function getJsonBody()
    {
        $rawBody = Yii::$app->request->getRawBody();
        if ($rawBody === '' || $rawBody === null) {
            return null;
        }
        $decoded = json_decode($rawBody, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Normalize EventDraw event id from query/body (handles "evt187763", numeric string, int).
     */
    private function normalizeEventDrawEventId($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }
        if (is_numeric($value)) {
            $n = (int) $value;
            return $n > 0 ? $n : null;
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        if (preg_match('/(\d+)$/', $s, $m)) {
            $n = (int) $m[1];
            return $n > 0 ? $n : null;
        }
        return null;
    }

    /**
     * Merge duplicate service order lines that share the same ResourceCode (sum Units).
     */
    private function aggregateServiceOrderItemsByResourceCode(array $items)
    {
        $byRc = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $rc = isset($item['ResourceCode']) ? strtoupper(trim((string) $item['ResourceCode'])) : '';
            if ($rc === '') {
                continue;
            }
            $units = isset($item['Units']) ? (int) $item['Units'] : 0;
            if (!isset($byRc[$rc])) {
                $byRc[$rc] = $item;
                $byRc[$rc]['Units'] = $units;
            } else {
                $byRc[$rc]['Units'] = (int) $byRc[$rc]['Units'] + $units;
            }
        }
        return array_values($byRc);
    }

    protected function findModel($id)
    {
        if (($model = MomentusShape::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function formatResources(array $payload)
    {
        $items = [];
        foreach ($this->extractItems($payload) as $resource) {
            // Only show Class 2 or Class 3 resources.
            $class = isset($resource['Class']) ? (string) $resource['Class'] : '';
            if ($class !== '2' && $class !== '3') {
                continue;
            }

            $description = isset($resource['ResourceCodeDescription']) ? (string) $resource['ResourceCodeDescription'] : '';
            $code = isset($resource['Code']) ? (string) $resource['Code'] : (isset($resource['ResourceCode']) ? (string) $resource['ResourceCode'] : '');
            $sequence = isset($resource['Sequence']) ? (int) $resource['Sequence'] : 1;
            $id = $code . '-' . $sequence;

            $items[] = [
                'id'          => $id,
                'description' => trim($description),
                'code'        => $code,
                'sequence'    => $sequence,
                'class'       => $class,
            ];
        }

        return $items;
    }

    private function extractItems(array $payload)
    {
        $keys = array_keys($payload);
        if ($keys === range(0, count($payload) - 1) || $payload === []) {
            return $payload;
        }

        $candidateKeys = ['Results', 'results', 'Value', 'value', 'Items', 'items'];
        foreach ($candidateKeys as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        return [];
    }

    /**
     * Resolve PriceListDetailSeqNbr for a resource within a given price list.
     * Tries keyword search first, then falls back to listing the full price list.
     */
    private function resolvePriceListDetailSeqNbr(MomentusClient $client, $orgCode, $priceList, $resourceCode)
    {
        try {
            $items = [];
            try {
                $response = $client->searchPriceListItems($resourceCode, $orgCode);
                $items = $this->extractItems(is_array($response) ? $response : []);
            } catch (\Exception $searchEx) {
                Yii::warning('searchPriceListItems failed, trying listPriceListItemsByCode: ' . $searchEx->getMessage(), __METHOD__);
            }

            if (empty($items)) {
                $response = $client->listPriceListItemsByCode($priceList, $orgCode);
                $items = $this->extractItems(is_array($response) ? $response : []);
            }
            $targetPriceList = strtoupper(trim((string) $priceList));
            $targetResource = strtoupper(trim((string) $resourceCode));

            // First pass: strict match on price list + resource code.
            foreach ($items as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $rowResource = strtoupper(trim((string) (
                    $row['ResourceCode'] ?? $row['Code'] ?? $row['Resource'] ?? ''
                )));
                $rowPriceList = strtoupper(trim((string) (
                    $row['PriceList'] ?? $row['PriceListCode'] ?? $row['ListCode'] ?? ''
                )));

                if ($rowResource === '' || $rowResource !== $targetResource) {
                    continue;
                }
                if ($rowPriceList !== '' && $rowPriceList !== $targetPriceList) {
                    continue;
                }

                foreach (['PriceListDetailSeqNbr', 'Sequence', 'SequenceNumber', 'DetailSeqNbr'] as $seqKey) {
                    if (isset($row[$seqKey]) && is_numeric($row[$seqKey])) {
                        return (int) $row[$seqKey];
                    }
                }
            }

            // No second pass -- returning any arbitrary sequence from the
            // price list would create the wrong item in Momentus and cause
            // duplicates on subsequent syncs.
        } catch (\Exception $e) {
            Yii::warning('Failed to resolve PriceListDetailSeqNbr: ' . $e->getMessage(), __METHOD__);
        }

        return null;
    }
}
