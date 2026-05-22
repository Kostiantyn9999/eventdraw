<?php

namespace backend\controllers;

use backend\components\ArrayValues;
use backend\components\EmailHelper;
use common\models\Usersettings;
use DateTime;
use Yii;
use common\models\User;
use common\models\UserSearch;
use common\models\AdminUserSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class AdminUserController extends Controller
{
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
                        'actions' => [
                            'export',
                            'logout',
                            'index',
                            'view',
                            'update',
                            'create',
                            'delete',
                            'search',
                            'psw',
                            'template',
                            'stencil',
                            'floorplans',
                            'emaillist',
                            'settings',
                            'ajax-save',
                            'ajax-delete',
                            'ajax-send-credentials',
                            'sendlogin'
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
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 1000;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        //$UserSettings = \common\models\Usersettings::findOne(['id' => $this->id]);
        
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelTemplate' => $this->findUserTemplates($id),
            'modelStencil' => $this->findUserStencils($id),
            'modelSettings' => $this->findUserSettings($id),
        ]);
    }

    public function actionExport()
    {
        $this->layout = false;
        return $this->render('export');
    }

    public function actionEmaillist()
    {
        //$this->layout = false;
        //return $this->render('emaillist');
        return $this->render('_search', ['model' => \common\models\UserSearch]);

    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $modelTemplates = $this->findUserTemplates(-1);
        $modelStencils = $this->findUserStencils(-1);
        $modelSettings = $this->findUserSettings(-1);
        if ($model->load(Yii::$app->request->post())) {
            $model->template_number = 2;

            if($model->save())
            {
                EmailHelper::sendWelcomeEmail($model);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelTemplate' => $modelTemplates,
            'modelStencil' => $modelStencils,
            'modelSettings' => $modelSettings,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        $modelTemplates = $this->findUserTemplates($id);
        $modelStencils = $this->findUserStencils($id);
        $modelSettings = $this->findUserSettings($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->id]);
        }


        return $this->render('update', [
            'model' => $model,
            'modelTemplate' => $modelTemplates,
            'modelStencil' => $modelStencils,
            'modelSettings' => $modelSettings,
        ]);
    }

    public function actionPsw($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('psw', [
            'model' => $model,
        ]);
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

    public function actionFloorplans($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('floorplans', [
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

    /**
     * Deletes an existing User model.
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

    protected function findUserSettings($id)
    {
        $userSetting = Usersettings::findOne(['userid' => $id]);
        if ($id != -1) {
            if (!$userSetting) {
                $userSetting = new Usersettings();
                $userSetting->userid = $id;
                $userSetting->meas_unit = 'M';
                $userSetting->copy_dist = 1;
                $userSetting->save(false);
            }
        }
        return $userSetting;

    }

    protected function findUserTemplates($id)
    {
        $modelTemplates = \common\models\UserTemplates::find()
            ->where(['userid' => $id])
            ->all();

        return $modelTemplates;
    }

    protected function findUserStencils($id)
    {
        $modelStencils = \common\models\UserStencils::find()
            ->where(['userid' => $id])
            ->all();

        return $modelStencils;
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return array
     */
    public function actionAjaxSave(): array
    {
        $model = User::findOne(Yii::$app->request->post('User')['id']);

        if (empty($model)) {
            $model = new User();
            $model->scenario = User::SCENARIO_CREATE;
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (!empty($model->password_hash)) {
                $oldPasswordHash = password_hash(time() . rand(), PASSWORD_DEFAULT);
            } else {
                $oldPasswordHash = $model->password_hash;
            }
            if ($model->load(Yii::$app->request->post())) {
                if ($model->isNewRecord) {
                    $model->template_number = 2;
                }
                $expiry_date = $model->expiry_date;
                $validate = ActiveForm::validate($model);

                if (!empty($validate)) {
                    return $validate;
                }

                if (empty(Yii::$app->request->post()['ajax'])) {
                    if ($model->isNewRecord) {
                        $model->password_hash = password_hash(time() . rand(), PASSWORD_DEFAULT);
                    } else {
                        if (!empty($model->password_hash)) {
                            $model->password_hash = password_hash($model->password_hash, PASSWORD_DEFAULT);
                        } else {
                            $model->password_hash = $oldPasswordHash;
                        }
                    }

                    $model->status = ArrayValues::userStatusKeyByValue($model->status);
                    $model->expiry_date = DateTime::createFromFormat('M d, Y', $expiry_date)->getTimestamp();

                    if ($model->save()) {
                        EmailHelper::sendWelcomeEmail($model);

                        return [
                            'data' => [
                                'success' => true,
                                'model' => $model,
                                'message' => 'User has been saved.',
                            ],
                            'code' => 0,
                        ];
                    }
                }
            }
        }

        return [
            'data' => [
                'success' => false,
                'model' => $model,
                'message' => 'There were some problems while saving the user.',
            ],
            'code' => 1,
        ];
    }

    /**
     * @return array
     */
    public function actionAjaxDelete(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($id = Yii::$app->request->post('id')) {
                $model = User::findOne($id)->delete();

                return [
                    'data' => [
                        'success' => true,
                        'model' => $model,
                        'message' => 'User has been delete successfully.',
                    ],
                    'code' => 0,
                ];
            }
        }

        return [
            'data' => [
                'success' => false,
                'model' => null,
                'message' => 'An error occurred.',
            ],
            'code' => 1, // Some semantic codes that you know them for yourself
        ];
    }

    /**
     * @return string
     */
    public function actionAjaxSendCredentials(): string
    {
        if (!empty($userIds = Yii::$app->request->post('userIds'))) {
            return EmailHelper::sendCredentialsToEmail($userIds);
        }

        return "No User(s) Selected!";
    }
    
    public function actionSendlogin($id)
    {
        
        $userIds = array();
        array_push($userIds, $id);
        EmailHelper::sendCredentialsToEmail($userIds);
       
       
       return $this->redirect(['view', 'id' => $id]);


    }

}
