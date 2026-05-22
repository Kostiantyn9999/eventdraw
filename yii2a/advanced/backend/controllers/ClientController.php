<?php

namespace backend\controllers;

use common\models\User;
use backend\services\users\UserSettingsService;
use backend\services\users\UserStencilsService;
use backend\services\users\UserTemplatesService;
use Yii;
use common\models\Client;
use common\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use function GuzzleHttp\Psr7\str;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
{
    private $userSettingsService;
    private $userTemplatesService;
    private $userStencilsService;

    public function __construct(
        $id,
        $module,
        UserSettingsService $userSettingsService,
        UserTemplatesService $userTemplatesService,
        UserStencilsService $userStencilsService,
        $config = []) {
        $this->userSettingsService = $userSettingsService;
        $this->userTemplatesService = $userTemplatesService;
        $this->userStencilsService = $userStencilsService;

        parent::__construct($id, $module, $config);
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
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'view', 'update', 'create', 'delete', 'search', 'template', 'settings', 'download','stencil'],
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();

        // var_dump($searchModel);
        // die();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 500;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDownload($id)
    {
        $path = '../../frontend/web/site/stencils_favourite/' . $id . '_Favourites.xml';
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            return Yii::$app->response->content = 'File not found!';
        }
    }

    public function actionTemplate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->saveTemplates()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('template', [
            'model' => $model,
        ]);
    }

    public function actionSettings($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }


    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelUsers' => $this->findClientUsers($id),
            'modelStencil' => $this->findClientStencils($id),
            'modelTemplate' => $this->findClientTemplates($id),
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Client();
        $modelUsers = $this->findClientUsers(-1);
        $modelStencils = $this->findClientStencils(-1);
        $modelTemplates = $this->findClientTemplates(-1);
        $postParams = Yii::$app->request->post();

        if ($model->load($postParams)) {
            $model->expiry_date = strtotime($postParams['expiry_date']);

            if ($model->save()) {
                return $this->redirect(['/client/update', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelUsers' => $modelUsers,
            'modelStencil' => $modelStencils,
            'modelTemplate' => $modelTemplates
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelUsers = $this->findClientUsers($id);
        $modelStencils = $this->findClientStencils($id);
        $modelTemplates = $this->findClientTemplates($id);

        $user = new User();
        $userSettings = $this->userSettingsService->findUserSettings(-1);
        $userTemplates = $this->userTemplatesService->findUserTemplates(-1);
        $userStencils = $this->userStencilsService->findUserStencils(-1);

        $postParams = Yii::$app->request->post();

        if ($model->load($postParams)) {
            $model->expiry_date = strtotime($postParams['expiry_date']);

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelUsers' => $modelUsers,
            'modelStencil' => $modelStencils,
            'modelTemplate' => $modelTemplates,
            'user' => $user,
            'userTemplate' => $userTemplates,
            'userStencil' => $userStencils,
            'userSettings' => $userSettings,
        ]);
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionStencil($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->saveStencils()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('stencil', [
            'model' => $model,
        ]);
    }
     
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findClientUsers($id)
    {
        return \common\models\User::find()
            ->where(['clientid' => $id])
            ->orderBy(['userfullname' => SORT_ASC])
            ->all();
    }

    protected function findClientStencils($id)
    {
        $modelStencils = \common\models\ClientStencils::find()
            ->where(['clientid' => $id])
            ->all();

        return $modelStencils;
    }

    protected function findClientTemplates($id)
    {
        $modelTemplates = \common\models\ClientTemplates::find()
            ->where(['clientid' => $id])
            ->all();

//        foreach ($modelTemplates as $modelTemplate) {
//            $modelTemplate->templateName = \common\models\ClientTemplates::;
//        }


        return $modelTemplates;
    }
}
