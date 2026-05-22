<?php

namespace backend\controllers;

use Yii;
use common\models\Template;
use common\models\TemplateSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
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
                        'actions' => ['logout', 'index', 'view', 'update', 'create','delete','search','psw','template','image','versions'],
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
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVersions($id)
    {
        $model = $this->findModel($id);

         if ($model->load(Yii::$app->request->post()) && $model->saveNewVersion()) {
             return $this->redirect(['update', 'id' => $model->id]);
         }

        return $this->render('versions', [
            'model' => $model,
        ]);
    }

    public function actionImage($id)
    {
        $model = $this->findModel($id);
        set_time_limit(180);

        if ($model->load(Yii::$app->request->post()) ) {
            $file = UploadedFile::getInstance($model,'imageFile');


            $fileToSave = Yii::getAlias("@backend/web/templates/images/") . $model->id . '.' . $file->extension;
//            var_dump($file);
            //var_dump($fileToSave);
            //die();
            $file->saveAs($fileToSave);
            $model->image = $model->id . '.' . $file->extension;
            $model->save(false);

                return $this->redirect(['view', 'id' => $model->id]);


        }

        return $this->render('image', [
            'model' => $model,
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
            'modelSubTemplate' => $this->findSubTemplates($id),
            'modelRealistic' => $this->findRealistic($id),

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

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $model->subtemplate = $this->findSubTemplates($id);
        $model->realisticid =   $this->getRealisticID($id);

        //fill exist subtemplates to $model

       
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            
         
             $RealisticTemplate=\common\models\RealisticTemplates::findOne(['template_id' => $model->id ]);
            
            if ($RealisticTemplate){

            
                if (is_null($model->realisticid) || $model->realisticid == '')
                {
                    //realistic is null. If there is record, delete it
                    
                    $RealisticTemplate->delete();
                }
                else
                {
                    $RealisticTemplate->realistic_id = $model->realisticid;
                    $RealisticTemplate->save();
                }
               
            }
            else{

               
                //add new recors if realistic is not null

                if (is_null($model->realisticid) || $model->realisticid == '')
                {

                }
                else
                {
           
                    $RealisticTemplate= new \common\models\RealisticTemplates();
                    $RealisticTemplate->template_id = $model->id;
                    $RealisticTemplate->realistic_id = $model->realisticid;
                    $RealisticTemplate->save();
                }

              

            }


            return $this->redirect(['view', 'id' => $model->id]);
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


    protected function findSubTemplates($id)
    {
        $modelSubTemplates= \common\models\Subtemplates::find()
            ->where(['templateid' => $id])
            ->all();
        return $modelSubTemplates;
    }


    protected function getRealisticID($id)
    {
        $RealisticTemplate=\common\models\RealisticTemplates::findOne(['template_id' => $id ]);
        if ($RealisticTemplate){
            return $RealisticTemplate->realistic_id;
        }
        else{
            return null;
        }
    }

     protected function findRealistic($id)
    {
        
        $modelRealistic= \common\models\RealisticTemplates::find()
            ->where(['template_id' => $id])
            ->all();
        return $modelRealistic;
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
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
