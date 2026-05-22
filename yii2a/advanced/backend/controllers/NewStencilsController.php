<?php



namespace backend\controllers;



use Yii;

use common\models\NewStencils;

use common\models\NewStencilsSearch;

use yii\filters\AccessControl;

use yii\web\Controller;

use yii\web\NotFoundHttpException;

use yii\filters\VerbFilter;

use yii\web\UploadedFile;



/**

 * StencilController implements the CRUD actions for Stencil model.

 */

class NewStencilsController extends Controller

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

                        'actions' => ['logout', 'index', 'view', 'update', 'create','delete','search','psw','template','versions'],

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

     * Lists all Stencil models.

     * @return mixed

     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

   public function actionVersions($id)
    {
        $model = $this->findModel($id);

         if ($model->load(Yii::$app->request->post()) && $model->saveNewVersion()) {
             return $this->redirect(['update', 'id' => $model->ID]);
         }

        return $this->render('versions', [
            'model' => $model,
        ]);
    }
    public function actionIndex()

    {

        $searchModel = new NewStencilsSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);



        return $this->render('index', [

            'searchModel' => $searchModel,

            'dataProvider' => $dataProvider,

        ]);

    }



    /**

     * Displays a single Stencil model.

     * @param integer $id

     * @return mixed

     * @throws NotFoundHttpException if the model cannot be found

     */


     /**

     * Creates a new Stencil model.

     * If creation is successful, the browser will be redirected to the 'view' page.

     * @return mixed

     */

    public function actionCreate()

    {

        $model = new NewStencils();



        if ($model->load(Yii::$app->request->post()) ) {



            $xmlFileInfo = UploadedFile::getInstance($model,'xmlFile');



            $model->save(false) ;



            return $this->redirect(['view', 'id' => $model->ID]);

        }



        return $this->render('create', [

            'model' => $model,

        ]);

    }



    /**

     * Updates an existing Stencil model.

     * If update is successful, the browser will be redirected to the 'view' page.

     * @param integer $id

     * @return mixed

     * @throws NotFoundHttpException if the model cannot be found

     */

    public function actionUpdate($id)

    {

        $model = $this->findModel($id);



        if ($model->load(Yii::$app->request->post())) {

     

            $model->save(false) ;



            return $this->redirect(['view', 'id' => $model->ID]);

        }



        return $this->render('update', [

            'model' => $model,

        ]);

    }



    


    /**

     * Deletes an existing Stencil model.

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

     * Finds the Stencil model based on its primary key value.

     * If the model is not found, a 404 HTTP exception will be thrown.

     * @param integer $id

     * @return Stencil the loaded model

     * @throws NotFoundHttpException if the model cannot be found

     */

    protected function findModel($id)

    {

        if (($model = NewStencils::findOne($id)) !== null) {

            return $model;

        }



        throw new NotFoundHttpException('The requested page does not exist.');

    }

}

