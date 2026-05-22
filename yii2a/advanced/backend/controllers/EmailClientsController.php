<?php

namespace backend\controllers;

use backend\components\ClientEmailHelper;
use backend\components\FileUploader;
use common\models\Client;
use common\models\User;
use common\models\ClientSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use common\models\EmailTemplate;
/**
 * Class EmailUsersController
 * @package backend\controllers
 */
class EmailClientsController extends Controller
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
                        'actions' => [
                            'login',
                            'error',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'bulk-mail-out',
                            'send-bulk-mail-out',
                            'eligible-email-users',
                            'eligible-email-clients',
                            'update-session',
                            'shift-select',
                            'export',
                            'get-template-data'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'send-bulk-mail-out' => ['POST'],
                ],
            ],
        ];
    }


    public function actionGetTemplateData(){
        $email_template_id  =   $_POST['email_template_id'];
        $EmailTemplate = new EmailTemplate();
        $email_data = EmailTemplate::findOne($email_template_id);

        if(!empty($email_data)){
            return json_encode(array('status'=>'true','template_name'=>$email_data->template_name,'content'=>$email_data->content,'bulletin_heading'=>$email_data->bulletin_heading));
        }else{
            return json_encode(array('status'=>'false','message'=>'no record found'));
        }
    }
    /**
     * @return string
     */
    public function actionBulkMailOut($selected = '', $from = '', $page = 1)
    {   
        Yii::$app->session->set('selectedUsers', []);
        Yii::$app->session->set('previouslySelectedUsers', []);
        $selectedUsers = array();
        $totalPages = 0;

        if (!empty($selected)) {
            /*$pageSize = 2;*/

            // if ($from == 'client') {
            //     $selectedClients = json_decode($selected);
            //     foreach ($selectedClients as $id) {
            //         //$client = Client::findOne($id);
            //         $clients = Client::find()->where(['id' => $id])->one();
            //         $new_row    =   array(
            //             'email'=>$clients->clientEmail,
            //             'id'=>$id
            //         );
            //         array_push($selectedUsers,$new_row);
            //     }
            // }
            if ($from == 'client') {
                $selectedClients = json_decode($selected);
                foreach ($selectedClients as $id) {
                    //$client = Client::findOne($id);
                    $users = User::find()->where(['clientid' => $id])->all();
                    foreach($users as $user){
                        $new_row    =   array(
                            'email'=>$user['email'],
                            'id'=>$user['id']
                        );
                        array_push($selectedUsers,$new_row);
                    }
                    
                }
            }
        }

        $email_template             =   new EmailTemplate();
        //$dataProvider = EmailTemplate::find()->where('template_type','email-template')->indexBy('id')->all();
        $dataProvider               =   EmailTemplate::find()->where(['template_type' =>'email-template'])->indexBy('id')->all();
        return $this->render('bulk-mail-out', compact('selectedUsers', 'page', 'totalPages','dataProvider'));
    }
    function get_client_info($id){
        $query = new Query;
        // compose the query
        $data   =   $query->select('id, name')
            ->from('user')
            ->where('id=:id', array(':id'=>$id))
            ->queryRow();

            return $data;
    }
    /**
     * @return Response
     */
    public function actionSendBulkMailOut()
    {
        $email = Yii::$app->request->post();
        //print_r($email);die;
        $send_from  =   $_POST['send_from'];
        $email_template_id  =   $_POST['email_template_id'];
        $filename = '';

        if (!empty($_FILES["attachment"]["name"])) {
            $filename = self::uploadFile();
            if (!$filename) {
                die('There was a problem while uploading the attachment.');
            }
        }

        ClientEmailHelper::sendEmailToBulkClients($email, $filename,$send_from,$email_template_id);

        return $this->redirect(['/site/']);
    }
    public static function uploadFile()
    {
        $target_dir = "uploads/email-attachments/";
        $filename = rand() . $_FILES["attachment"]["name"];
        $target_file = $target_dir . basename($filename);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["attachment"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }

        if (file_exists($target_file)) {
            $uploadOk = 0;
        }

        if ($_FILES["attachment"]["size"] > 500000) {
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            return false;
        } else {
            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                return $filename;
            }
        }

        return false;
    }
    public function actionShiftSelect()
    {
        $data = Yii::$app->request->post();
        $session = Yii::$app->session;
        $selectedUsers = [];
        $totalSelectedUsers = 0;
        if ($session->has('selectedUsers')) {
            $selectedUsers = json_decode($session->get('selectedUsers'), true);
        }

        $emails = $data['emails'];
        $isChecked = $data['isChecked'];

        foreach ($emails as $email) {
            $key = array_search($email, array_column($selectedUsers, 'email'));
            $selectedUsers[$key]['isChecked'] = $isChecked === 'true';
        }

        foreach ($selectedUsers as $user) {
            if ($user['isChecked'] === true) {
                $totalSelectedUsers++;
            }
        }

        $response['totalSelectedUsers'] = $totalSelectedUsers;
        $session->set('selectedUsers', json_encode($selectedUsers));

        return json_encode($response);
    }

    /**
     * @return string
     */
    public function actionEligibleEmailUsers()
    {
        $data = Yii::$app->request->post();

        return json_encode(ClientEmailHelper::eligibleEmailClientsCheckbox($data));
    }

    public function actionEligibleEmailClients(){
        

        $data = Yii::$app->request->post();

        return json_encode(ClientEmailHelper::eligibleEmailUsers($data));
    }

    public function actionUpdateSession()
    {
        $data = Yii::$app->request->post();
        $session = Yii::$app->session;
        $selectedUsers = [];
        $totalSelectedUsers = 0;

        if ($session->has('selectedUsers')) {
            $selectedUsers = json_decode($session->get('selectedUsers'), true);
        }

        $email = $data['email'];
        $isChecked = $data['isChecked'];

        $key = array_search($email, array_column($selectedUsers, 'email'));
        $selectedUsers[$key]['isChecked'] = $isChecked === 'true';

        foreach ($selectedUsers as $user) {
            if ($user['isChecked'] === true) {
                $totalSelectedUsers++;
            }
        }

        $response['totalSelectedUsers'] = $totalSelectedUsers;
        $session->set('selectedUsers', json_encode($selectedUsers));

        return json_encode($response);
    }

    public function actionExport()
    {
        $data = Yii::$app->request->post();
        $session = Yii::$app->session;
        $exportUser = [];
        //print_r(Yii::$app->session);die;
        if ($session->has('exportUser')) {
            $exportUser = json_decode($session->get('exportUser'), true);
            //echo sizeof($exportUser);
            //print_r($exportUser);die;
        }
        //print_r($data);die;
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=SelectedUsers.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr><th>Emails</th></tr>';
        
        foreach ($exportUser as $user) {
            echo '<tr><td>';
            echo $user;
            echo '</td></tr>';
        }
        echo '</table>';

        exit();
    }
}
