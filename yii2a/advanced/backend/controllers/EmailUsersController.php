<?php

namespace backend\controllers;

use backend\components\EmailHelper;
use backend\components\FileUploader;
use common\models\Client;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use common\models\EmailTrackData;
use common\models\EmailTemplate;
/**
 * Class EmailUsersController
 * @package backend\controllers
 */
class EmailUsersController extends Controller
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
                            'update-session',
                            'shift-select',
                            'export',
                            'update-mail-tracking',
                            'read-mail'
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

    public function actionReadMail(){
        $modelTemplates = EmailTrackData::find()->orderBy(['created_at'=>SORT_DESC])->all();
        //print_r($modelTemplates);die;
        return $this->render('read-mail',['modelTemplates'=>$modelTemplates]);
    }
    public function actionUpdateMailTracking($tracking_code=''){
        if(!empty($tracking_code)):
        $mail_tracking_data = EmailTrackData::find()->where(['email_tracking_code' => $tracking_code])->one();
        //print_r($mail_tracking_data);die;

        $mail_tracking_data->email_status = 'yes';
        $mail_tracking_data->email_open_datetime = date('Y-m-d H:i:s');
        $mail_tracking_data->save();
    endif;
        return '<img src="https://developer.eventdrawus.com/open.gif">';
    }

    /**
     * @return string
     */
    public function actionBulkMailOut($selected = '', $from = '', $page = 1)
    {
        Yii::$app->session->set('selectedUsers', []);
        Yii::$app->session->set('previouslySelectedUsers', []);
        $selectedUsers = [];
        $totalPages = 0;

        if (!empty($selected)) {
            /*$pageSize = 2;*/

            if ($from == 'client') {
                $selectedClients = json_decode($selected);

                foreach ($selectedClients as $id) {
                    $client = Client::findOne($id);

                    foreach ($client->users as $user) {
                        $selectedUsers[] = $user->email;
                    }
                }
            } else {
                $userIds = json_decode($selected);

                foreach ($userIds as $id) {
                    $user = User::findOne($id);

                    $selectedUsers[] = $user->email;
                }
            }

            /*Yii::$app->session->set('previouslySelectedUsers', $selectedUsers);

            $totalPages = ceil(count($selectedUsers)) / $pageSize;
            $selectedUsers = array_slice($selectedUsers, 0, $pageSize);*/

            $email_template             =   new EmailTemplate();
            //$dataProvider = EmailTemplate::find()->where('template_type','email-template')->indexBy('id')->all();
            $dataProvider               =   EmailTemplate::find()->where(['template_type' =>'email-template'])->indexBy('id')->all();
        }

        return $this->render('bulk-mail-out', compact('selectedUsers', 'page', 'totalPages','dataProvider'));
    }

    /**
     * @return Response
     */
    public function actionSendBulkMailOut()
    {
        $email = Yii::$app->request->post();

        $send_from  =   $_POST['send_from'];
        $filename = '';

        if (!empty($_FILES["attachment"]["name"])) {
            $filename = FileUploader::uploadFile();
            if (!$filename) {
                die('There was a problem while uploading the attachment.');
            }
        }

        EmailHelper::sendEmailToBulkUser($email, $filename,$send_from);

        return $this->redirect(['/site/']);
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

        return json_encode(EmailHelper::eligibleEmailUsers($data));
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
