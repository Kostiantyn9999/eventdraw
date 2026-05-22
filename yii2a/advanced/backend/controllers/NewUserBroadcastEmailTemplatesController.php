<?php

namespace backend\controllers;

use Yii;
use common\models\NewUserBroadcastEmailTemplates;
use common\models\NewUserBroadcastEmailTemplatesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NewUserBroadcastEmailTemplatesController implements the CRUD actions for NewUserBroadcastEmailTemplates model.
 */
class NewUserBroadcastEmailTemplatesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NewUserBroadcastEmailTemplates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewUserBroadcastEmailTemplatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataOrganizer = $searchModel->search_event_organizer(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataOrganizer'=>$dataOrganizer
        ]);
    }

    /**
     * Displays a single NewUserBroadcastEmailTemplates model.
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
     * Creates a new NewUserBroadcastEmailTemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NewUserBroadcastEmailTemplates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionBulletin()
    {
        $model = new NewUserBroadcastEmailTemplates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('form_bulletin', [
            'model' => $model,
        ]);
    }
    public function actionBupdate()
    {
        $model = new NewUserBroadcastEmailTemplates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update_bulletin', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing NewUserBroadcastEmailTemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        //print_r(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //print_r(Yii::$app->request->post());die;
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NewUserBroadcastEmailTemplates model.
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
     * Finds the NewUserBroadcastEmailTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NewUserBroadcastEmailTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewUserBroadcastEmailTemplates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
