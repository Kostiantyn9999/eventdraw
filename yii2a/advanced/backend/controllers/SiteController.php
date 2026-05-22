<?php



namespace backend\controllers;



use common\models\User;

use Yii;

use yii\web\Controller;

use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use common\models\LoginFormAdmin;



/**

 * Site controller

 */

class SiteController extends Controller

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
                        'actions' => ['login','error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout','update','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function actions()

    {

        return [

            'error' => [

                'class' => 'yii\web\ErrorAction',

            ],

        ];

    }



    /**

     * Displays homepage.

     *

     * @return string

     */

    public function actionIndex()

    {
        $user_id = Yii::$app->user->identity->id;
        $customer = User::find()->where(['id' => $user_id])->one();
        //print_r($customer);
        return $this->render('index', [
            'userData' => $customer,
        ]);

    }






    public function actionTemplate()

    {

        return $this->render('template');

    }



    public function actionStencil()

    {

        return $this->render('stencil');

    }



    public function actionUser()

    {

        return $this->render('user');

    }



    public function actionGoogle()

    {

        return $this->render('google');

        //return $this->render('userGoogle');

    }



    /**

     * Login action.

     *

     * @return string

     */

    public function actionLogin()

    {

        if (!Yii::$app->user->isGuest) {

            return $this->goHome();

        }



        $model = new LoginFormAdmin();

        $modelGoogle = new LoginFormAdmin();



        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();

        } else {

            $model->password = '';



            return $this->render('login', [

                'model' => $model,

            ]);

        }

    }



    /**

     * Logout action.

     *

     * @return string

     */

    public function actionLogout()

    {

        Yii::$app->user->logout();



        return $this->goHome();

    }

    public function actionUpdate()
    {
        $user_id = Yii::$app->user->identity->id;

        $request = Yii::$app->request;
        $userType = $request->post('user_type');
        $evnt = User::find()->where(['id' => $user_id])->one();
        //$evnt   = User::findByID($user_id);
        //print_r($evnt);
        if ($evnt) {
            $evnt->id = $id;
            $evnt->userType = $userType;

            $save = $evnt->save(false);
            if($save){
                $arr    =   array('status'=>'true','message'=>'success');
                echo json_encode($arr);
            }else{
                $arr    =   array('status'=>'true','message'=>'failed','evnt'=>$evnt);
                echo json_encode($arr);

            }
        }
    }

}

