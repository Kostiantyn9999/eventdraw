<?php

namespace frontend\controllers;

use common\components\MomentusClient;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class MomentusController extends Controller
{
    /**
     * Domains allowed for CORS (shapes API may be called from eventdraw iframe or external pages).
     */
    public static function allowedDomains()
    {
        return [
            '*',
            'https://momentusstaging.eventdrawus.com',
            'https://momentus.eventdrawus.com',
            'http://localhost',
        ];
    }

    /**
     * Disable CSRF for API-style POST actions called from Draw.io (no CSRF token available).
     */
    public function beforeAction($action)
    {
        $csrfExempt = ['create-note', 'update-note', 'delete-note'];
        if (in_array($action->id, $csrfExempt)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => static::allowedDomains(),
                    'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'search-spaces',
                            'search-notes',
                            'get-note',
                            'create-note',
                            'update-note',
                            'delete-note',
                            'shapes-api',
                            'shape-manager',
                            'event-fields-for-floorplan',
                        ],
                        'allow' => true,
                        // 'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search-spaces'            => ['GET'],
                    'search-notes'             => ['GET'],
                    'get-note'                 => ['GET'],
                    'create-note'              => ['POST'],
                    'update-note'              => ['PUT', 'PATCH'],
                    'delete-note'              => ['DELETE'],
                    'shapes-api'               => ['GET'],
                    'shape-manager'            => ['GET'],
                    'event-fields-for-floorplan' => ['GET', 'OPTIONS'],
                ],
            ],
        ];
    }

    public function actionSearchSpaces()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $query = trim((string) \Yii::$app->request->get('q', ''));
        $page = \Yii::$app->request->get('page');
        $pageSize = \Yii::$app->request->get('pageSize');
        $order = \Yii::$app->request->get('order');

        try {
            $client = new MomentusClient();
            $result = $client->searchSpaces($query, $page, $pageSize, $order);

            return $this->formatSpaces($result);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    /**
     * Resolve org_code — prefer server config, fall back to request for dev/local.
     */
    private function getOrgCode()
    {
        $fromConfig = trim((string) (\Yii::$app->params['momentus']['orgCode'] ?? ''));
        if ($fromConfig !== '') {
            return $fromConfig;
        }
        return trim((string) (\Yii::$app->request->get('org_code') ?: \Yii::$app->request->post('org_code', '')));
    }

    public function actionSearchNotes()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $search = (string) \Yii::$app->request->get('search', \Yii::$app->request->get('q', ''));
        $page = \Yii::$app->request->get('page');
        $pageSize = \Yii::$app->request->get('pageSize');
        $order = \Yii::$app->request->get('order');

        try {
            $client = new MomentusClient();
            return $client->searchNotes($search, $page, $pageSize, $order);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    public function actionGetNote($type, $code, $sequenceNumber, $orgCode = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $client = new MomentusClient();
            return $client->getNote($type, $code, $sequenceNumber, $orgCode);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    public function actionCreateNote()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = \Yii::$app->request->getBodyParams();

        if (!is_array($payload) || $payload === []) {
            \Yii::$app->response->statusCode = 400;
            return ['error' => 'Request body must be a non-empty JSON object.'];
        }

        try {
            $client = new MomentusClient();
            return $client->createNote($payload);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    public function actionUpdateNote($type, $code, $sequenceNumber, $orgCode = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = \Yii::$app->request->getBodyParams();

        if (!is_array($payload) || $payload === []) {
            \Yii::$app->response->statusCode = 400;
            return ['error' => 'Request body must be a non-empty JSON object.'];
        }

        try {
            $client = new MomentusClient();
            return $client->updateNote($type, $code, $sequenceNumber, $payload, $orgCode);
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    public function actionDeleteNote($type, $code, $sequenceNumber, $orgCode = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $client = new MomentusClient();
            $client->deleteNote($type, $code, $sequenceNumber, $orgCode);
            \Yii::$app->response->statusCode = 204;
            return null;
        } catch (\Exception $exception) {
            \Yii::error($exception->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['error' => $exception->getMessage()];
        }
    }

    /**
     * Public API: shapes in eventdraw format with Momentus link info.
     * Same structure as https://3d.eventdraw.com.au/eventdraw_api/public/api/shapes
     * plus linkedToMomentus, momentus_resource_code, momentus_resource_description, momentus_sequence.
     *
     * GET params:
     *   - org_code (optional): if provided, Momentus info reflects the primary mapping for this org only.
     */
    public function actionShapesApi()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $orgCode = $this->getOrgCode();

        $joinCondition = 's.id = m.shape_id AND m.momentus_resource_code IS NOT NULL AND m.momentus_resource_code <> \'\'';
        $params = [];
        if ($orgCode !== '') {
            $joinCondition .= ' AND m.org_code = :org_code';
            $params[':org_code'] = $orgCode;
        }

        $query = (new \yii\db\Query())
            ->select([
                's.source_id',
                's.shapeType',
                's.category',
                's.elevate',
                's.height',
                's.description',
                's.model',
                's.shapetypes',
                'm.momentus_resource_code',
                'm.momentus_resource_description',
                'm.sequence',
            ])
            ->from('{{%shapes}} s')
            ->leftJoin('{{%shape_momentus_mapping}} m', $joinCondition, $params)
            ->orderBy(['s.source_id' => SORT_ASC, 'm.sequence' => SORT_ASC]);

        $rows = $query->all(\Yii::$app->db);

        $result = [];
        $seen = [];
        foreach ($rows as $row) {
            $sid = (int) $row['source_id'];
            if (isset($seen[$sid])) {
                continue;
            }
            $seen[$sid] = true;

            $linked = $row['momentus_resource_code'] !== null && $row['momentus_resource_code'] !== '';
            $result[] = [
                'id' => $sid,
                'shapeType' => $row['shapeType'],
                'category' => (int) $row['category'],
                'elevate' => (int) $row['elevate'],
                'height' => (int) $row['height'],
                'description' => $row['description'],
                'model' => $row['model'],
                'shapetypes' => $row['shapetypes'],
                'linkedToMomentus' => $linked,
                'momentus_resource_code' => $linked ? $row['momentus_resource_code'] : null,
                'momentus_resource_description' => $linked ? $row['momentus_resource_description'] : null,
                'momentus_sequence' => $linked ? (int) $row['sequence'] : null,
            ];
        }

        return $result;
    }

    public function actionShapeManager()
    {
        return $this->render('shapes');
    }

    private function formatSpaces(array $payload)
    {
        $items = [];
        foreach ($this->extractItems($payload) as $space) {
            $description = isset($space['SpaceDescription']) ? (string) $space['SpaceDescription'] : '';
            $code = isset($space['Code']) ? (string) $space['Code'] : (isset($space['SpaceCode']) ? (string) $space['SpaceCode'] : '');
            $id = isset($space['SpaceID']) ? (string) $space['SpaceID'] : '';

            $items[] = [
                'id' => $id,
                'text' => trim($description . ($code !== '' ? ' (' . $code . ')' : '')),
                'description' => $description,
                'code' => $code,
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
     * GET /momentus/event-fields-for-floorplan?momentus_event_id=9427&org_code=10
     * Returns normalized strings for EventDraw floorplan XXXX placeholders (Saved Layout load).
     */
    public function actionEventFieldsForFloorplan()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $fw = (array) (\Yii::$app->params['momentus']['floorplanWorkflow'] ?? []);
        if (array_key_exists('enabled', $fw) && !$fw['enabled']) {
            return ['ok' => false, 'error' => 'disabled', 'fields' => []];
        }

        $reqEvent = \Yii::$app->request->get('momentus_event_id');
        $reqOrg = \Yii::$app->request->get('org_code');

        $eventId = ($reqEvent !== null && $reqEvent !== '') ? (int) $reqEvent : (int) ($fw['defaultMomentusEventId'] ?? 9427);
        $orgCode = trim((string) ($reqOrg ?? ''));
        if ($orgCode === '') {
            $orgCode = trim((string) ($fw['defaultOrgCode'] ?? ''));
        }
        if ($orgCode === '') {
            $orgCode = trim($this->getOrgCode());
        }
        if ($orgCode === '') {
            $orgCode = '10';
        }

        try {
            $client = new MomentusClient();
            $row = $client->getEventRowForFloorplan($eventId, $orgCode);
            if ($row === null) {
                return [
                    'ok' => false,
                    'error' => 'event_not_found',
                    'momentus_event_id' => $eventId,
                    'org_code' => $orgCode,
                    'fields' => [],
                ];
            }

            return [
                'ok' => true,
                'momentus_event_id' => $eventId,
                'org_code' => $orgCode,
                'fields' => self::mapMomentusEventRowToFloorplanFields($row, $eventId),
            ];
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), __METHOD__);
            \Yii::$app->response->statusCode = 500;
            return ['ok' => false, 'error' => $e->getMessage(), 'fields' => []];
        }
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,string>
     */
    private static function mapMomentusEventRowToFloorplanFields(array $row, $eventIdFallback)
    {
        $pick = function (array $keys) use ($row) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                    return $row[$k];
                }
            }
            return null;
        };

        $name = $pick(['Description', 'EventName', 'Name', 'LongDescription']);
        $startRaw = $pick(['StartDate', 'EventStartDate', 'EventDate']);
        $startTimeRaw = $pick(['StartTime', 'EventStartTime', 'TimeStart']);
        $pax = $pick(['Attendance', 'EstimatedAttendance', 'ExpectedAttendance', 'PlannedAttendance', 'UsrsEstimate']);
        $contact = $pick(['Contact', 'ContactDescription', 'ContactName', 'PrimaryContactName', 'BillToContact', 'Coordinator']);
        $parking = $pick(['ParkingNotes', 'Parking_Notes', 'ParkingNote']);
        $security = $pick(['SafetySecurityNotes', 'SafetyNotes', 'SecurityNotes', 'SafetyAndSecurityNotes']);

        $eventNum = $pick(['Event', 'EventID']);
        $eventIdStr = $eventNum !== null ? (string) $eventNum : (string) (int) $eventIdFallback;

        return [
            'eventDate' => self::formatMomentusDisplayDate($startRaw),
            'eventTime' => self::formatMomentusDisplayTime($startTimeRaw, $startRaw),
            'pax' => $pax !== null ? (string) $pax : '',
            'eventName' => $name !== null ? (string) $name : '',
            'eventId' => $eventIdStr,
            'contact' => $contact !== null ? (string) $contact : '',
            'parkingNotes' => $parking !== null ? (string) $parking : '',
            'securityNotes' => $security !== null ? (string) $security : '',
        ];
    }

    /**
     * @param mixed $v
     */
    private static function formatMomentusDisplayDate($v)
    {
        if ($v === null || $v === '') {
            return '';
        }
        if (is_string($v) && preg_match('/^\s*\d{1,2}\/\d{1,2}\/\d{2,4}/', $v)) {
            return trim($v);
        }
        $s = (string) $v;
        $ts = strtotime($s);
        if ($ts === false && is_numeric($v)) {
            $n = (int) $v;
            if ($n > 200000000000) {
                $ts = (int) round($n / 1000);
            } elseif ($n > 1000000000) {
                $ts = $n;
            }
        }
        if ($ts === false || $ts <= 0) {
            return $s;
        }

        return date('m/d/Y', $ts);
    }

    /**
     * @param mixed $timePart
     * @param mixed $datePart
     */
    private static function formatMomentusDisplayTime($timePart, $datePart)
    {
        if ($timePart !== null && $timePart !== '') {
            $tStr = trim((string) $timePart);
            if (preg_match('/\d{1,2}:\d{2}/', $tStr) && preg_match('/[AP]M/i', $tStr)) {
                return $tStr;
            }
            $t = strtotime($tStr);
            if ($t !== false) {
                return date('g:i A', $t);
            }

            return $tStr;
        }
        if ($datePart !== null && $datePart !== '') {
            $ts = strtotime((string) $datePart);
            if ($ts !== false) {
                return date('g:i A', $ts);
            }
        }

        return '';
    }
}
