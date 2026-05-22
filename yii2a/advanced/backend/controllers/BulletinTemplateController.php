<?php
namespace backend\controllers;
use Yii;
use common\models\FrontTemplate;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\User;
use common\models\TemplateChangeView;
/**
 * TemplateController implements the CRUD actions for Template model.
 */
class BulletinTemplateController extends Controller
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
                        'actions' => ['index', 'view', 'update','send-mail-user','updates'],
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
        $FrontTemplate = new FrontTemplate();
        $dataProvider = FrontTemplate::find()->indexBy('id')->orderBy(['id' => SORT_DESC])->all();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionSendMailUser()
    {
        $FrontTemplate = new FrontTemplate();
        $dataProvider = FrontTemplate::find()->indexBy('id')->all();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionUpdate($id=''){
        $FrontTemplate = new FrontTemplate();
        $email_data = FrontTemplate::findOne($id);
        return $this->render('update',['getData'=>$email_data]);
    }
    public function actionCreate()
    {
        $EmailTemplate = new EmailTemplate();
        return $this->render('create');
    }
    public function actionUpdates($id=''){
        $FrontTemplate = new FrontTemplate();
        $frontemplate = FrontTemplate::find()->where(['id' => $_POST['t_id']])->one();  
        // $id not found in database
        // update record   

        
        if($_POST['t_id']){
            $user_ids           =   $_POST['user_id'];
            $no_of_entry        =   count($user_ids);
            $userIds            =   array();
            for($i=0;$i<$no_of_entry;$i++){
                if(!empty($user_ids[$i])){
                    array_push($userIds,$user_ids[$i]);
                }
            }
            if (!empty($frontemplate->show_to_user)) {
                $oldShow = explode(',', $frontemplate->show_to_user); // Convert the string to an array
                $freshArray = $_POST['user_id']; // Assuming this is already an array

                $newArray = array_diff($oldShow, $freshArray); // Find the difference
                $templateChangeView     =   new TemplateChangeView();
                if (!empty($newArray)) {
                    foreach ($newArray as $ids) {
                        $model = templateChangeView::findOne(['user_id' => $ids, 'client_id' => $frontemplate->client_id]);
                        if (!empty($model)) { // Check if the model exists
                            $model->delete();
                        }
                    }
                }
                for($i=0;$i<$no_of_entry;$i++){
                    $newModel = new TemplateChangeView();
                    $newModel->template_id = $frontemplate->template_id;
                    $newModel->is_seen = 0;
                    $newModel->user_id = $user_ids[$i];
                    $newModel->client_id = $frontemplate->client_id;
                    $checkRecord = templateChangeView::findOne(['user_id' => $user_ids[$i], 'client_id' => $frontemplate->client_id]);
                    if(empty($checkRecord)){
                        $newModel->save();
                    }
                }

            }
            if(!empty($userIds)){
                $show_to_user       =   implode(',',$userIds);
                $frontemplate->show_to_user = $show_to_user;
                $frontemplate->save();
            }
            //echo 'enter';die;   
            return $this->redirect(['bulletin-template/update?id='.$id]);   
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