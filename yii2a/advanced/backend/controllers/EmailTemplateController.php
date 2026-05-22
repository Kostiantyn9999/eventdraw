<?php
namespace backend\controllers;
use Yii;
use common\models\EmailTemplate;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use backend\components\EmailHelper;
use common\models\EmailTemplateSearch;
/**
 * TemplateController implements the CRUD actions for Template model.
 */
class EmailTemplateController extends Controller
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
                        'actions' => ['index', 'view', 'update', 'create','copy-template','delete','save-email-template','insert','updates'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['GET'],
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
        $EmailTemplate = new EmailTemplate();
        $dataProvider = EmailTemplate::find()->indexBy('id')->all();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionUpdate($id=''){
        $EmailTemplate = new EmailTemplate();
        $email_data = EmailTemplate::findOne($id);
        return $this->render('update',['getData'=>$email_data]);
    }
    public function actionCopyTemplate($id=''){
        $EmailTemplate = new EmailTemplate();
        $email_data = EmailTemplate::findOne($id);
        return $this->render('copy',['getData'=>$email_data]);
    }
    /**
     * Creates a new Template model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $EmailTemplate = new EmailTemplate();
        return $this->render('create');
    }
    public function actionInsert(){
        $emailTemplate = new EmailTemplate();
        $emailTemplate->template_name =     $_POST['template_name'];
        $emailTemplate->email_subject =     $_POST['email_subject'];
        $emailTemplate->content       =     $_POST['content'];
        $emailTemplate->template_type =     $_POST['template_type'];
         $emailTemplate->bulletin_heading    =   $_POST['bulletin_heading'];
        $insert                         =   $emailTemplate->save();
        if ($insert) {
            return $this->redirect(['index']);
        }
    }

    public function actionUpdates($id=''){
        $emailTemplate = new EmailTemplate();
        $emailTemplate = emailTemplate::find()->where(['id' => $_POST['t_id']])->one();  
        // $id not found in database
        // update record   
        if($_POST['t_id']){
            $emailTemplate->id = $_POST['t_id'];
            $emailTemplate->content = $_POST['content'];
            $emailTemplate->template_name = $_POST['template_name'];
            $emailTemplate->email_subject = $_POST['email_subject'];
            $emailTemplate->template_type =     $_POST['template_type'];
            $emailTemplate->bulletin_heading    =   $_POST['bulletin_heading'];
            $emailTemplate->save();
            //echo 'enter';die;   
            return $this->redirect(['index']);   
        } 
    }
     /**  
    * Delete  
     * @param integer $id  
     */   
    public function actionDelete($id)   
    {  
        $EmailTemplate  =   new EmailTemplate();
        $model = EmailTemplate::findOne($id);         
        // $id not found in database   
        if($model === null)   
            throw new NotFoundHttpException('The requested page does not exist.');              
        // delete record   
        $model->delete();   
        return $this->redirect(['index']);   
    }
}