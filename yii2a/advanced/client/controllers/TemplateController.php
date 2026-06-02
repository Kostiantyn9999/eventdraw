<?php

namespace client\controllers;

use Yii;
use common\models\Client;
use common\models\Template;
use common\models\TemplateSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\Response;
use yii\web\BadRequestHttpException;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
{
    /**
     * Resolve client id: request param, then logged-in user session.
     */
    private function resolveClientIdForApi()
    {
        $fromRequest = (int) (\Yii::$app->request->get('client_id') ?: \Yii::$app->request->post('client_id', 0));
        if ($fromRequest > 0) {
            return $fromRequest;
        }

        if (!\Yii::$app->user->isGuest) {
            $clientId = (int) \Yii::$app->user->identity->clientid;
            return $clientId > 0 ? $clientId : null;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $csrfExempt = ['ajax-set-momentus-space', 'ajax-set-momentus-diagram-id', 'index-json'];
        if (in_array($action->id, $csrfExempt)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // Publicly accessible — called from Draw.io iframe without a session
                        'actions' => ['login', 'error', 'ajax-set-momentus-space', 'ajax-set-momentus-diagram-id', 'index-json'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'view', 'update', 'create', 'delete', 'search', 'psw', 'template'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Template models.
     * @return mixed
     */




public function actionAjaxSetMomentusSpace()
{
    \Yii::$app->response->format = Response::FORMAT_JSON;

    $templateId = (int)\Yii::$app->request->post('id');
    $code = trim((string)\Yii::$app->request->post('code'));
    $desc = trim((string)\Yii::$app->request->post('desc')); 
    if (!$templateId) {
        throw new BadRequestHttpException('No template id');
    }

    $m = Template::findOne($templateId);
    if (!$m) {
        throw new BadRequestHttpException('Template not found');
    }

    if (Client::usesClientResourceScoping()) {
        $clientId = $this->resolveClientIdForApi();
        if ($clientId !== null && (int) $m->clientid !== (int) $clientId) {
            throw new BadRequestHttpException('Template not found');
        }
    }

    if ($code === '') {
        $m->momentusSpaceCode = null;
        $m->momentusSpaceDescr = null;
    } else {
        $otherQuery = Template::find()
            ->where(['momentusSpaceCode' => $code])
            ->andWhere(['<>', 'id', $templateId]);
        if (Client::usesClientResourceScoping()) {
            $clientId = $this->resolveClientIdForApi();
            if ($clientId !== null) {
                $otherQuery->andWhere(['clientid' => $clientId]);
            }
        }
        $other = $otherQuery->one();
        if ($other !== null) {
            return [
                'ok' => false,
                'message' => 'This Momentus space is already assigned to template: ' . $other->templateName,
            ];
        }
        $m->momentusSpaceCode = $code;
        $m->momentusSpaceDescr = $desc ?: null;
    }

    if ($m->save(false, ['momentusSpaceCode', 'momentusSpaceDescr'])) {
        return ['ok' => true];
    }
    $err = $m->getFirstError('momentusSpaceCode');
    if ($err !== null) {
        return ['ok' => false, 'message' => $err, 'errors' => $m->errors];
    }
    return ['ok' => false, 'errors' => $m->errors];
}


    public function actionAjaxSetMomentusDiagramId()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $templateId = (int)\Yii::$app->request->post('id');
        $diagramId  = (int)\Yii::$app->request->post('diagram_id');

        if (!$templateId) {
            throw new BadRequestHttpException('No template id');
        }

        $m = Template::findOne($templateId);
        if (!$m) {
            throw new BadRequestHttpException('Template not found');
        }

        $m->momentusEventSpaceDiagramId = $diagramId > 0 ? $diagramId : null;

        if ($m->save(false, ['momentusEventSpaceDiagramId'])) {
            return ['ok' => true];
        }
        return ['ok' => false, 'errors' => $m->errors];
    }

    /**
     * JSON endpoint for the Space Mapping dialog in Draw.io.
     * Returns all templates as [{id, templateName, momentusSpaceCode, momentusSpaceDescr}].
     */
    public function actionIndexJson()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = Template::find()->orderBy('templateName');
        if (Client::usesClientResourceScoping()) {
            $clientId = $this->resolveClientIdForApi();
            if ($clientId !== null) {
                $query->andWhere(['clientid' => $clientId]);
            }
        }

        $templates = $query->all();
        $out = [];
        foreach ($templates as $t) {
            $out[] = [
                'id'               => $t->id,
                'templateName'     => $t->templateName,
                'momentusSpaceCode'  => $t->momentusSpaceCode,
                'momentusSpaceDescr' => $t->momentusSpaceDescr,
            ];
        }
        return $out;
    }

    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        if (Client::usesClientResourceScoping()) {
            $dataProvider = $searchModel->searchClient(Yii::$app->request->queryParams);
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }
        $dataProvider->pagination->pageSize=50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Template model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Template model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Template();

        if (Client::usesClientResourceScoping()) {
            $model->clientid = Yii::$app->user->identity->clientid;
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Client::usesClientResourceScoping()) {
                $model->clientid = Yii::$app->user->identity->clientid;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Template model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Client::usesClientResourceScoping()) {
                $model->clientid = Yii::$app->user->identity->clientid;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Template model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (Client::usesClientResourceScoping()) {
            if (($model = Template::findOne(['id' => $id, 'clientid' => Yii::$app->user->identity->clientid])) !== null) {
                return $model;
            }
        } elseif (($model = Template::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
