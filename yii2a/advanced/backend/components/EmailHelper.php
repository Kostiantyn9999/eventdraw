<?php

namespace backend\components;

use common\models\Client;
use common\models\NewUserBroadcastEmailTemplates;
use common\models\User;
use common\models\EmailsSent;
use http\Exception;
use Yii;
use yii\web\ServerErrorHttpException;
use common\models\EmailTemplate;

/**
 * Class EmailHelper
 */
class EmailHelper
{
    /**
     * @param array $userIds
     * @return string
     */
    public static function sendCredentialsToEmail(array $userIds): string
    {
        $messages = [];

        foreach ($userIds as $id) {
            $model = User::findOne($id);
            $model->password_reset_token = substr(base64_encode(sha1(mt_rand())), 0, 64);

            if ($model->save(false)) {
                if (!User::isPasswordResetTokenValid($model->password_reset_token)) {
                    $model->generatePasswordResetToken();

                    if (!$model->save()) {
                        continue;
                    }
                }
 
                $messages[] = Yii::$app
                    ->mailer
                    ->compose('user-credentials', [
                        'name' => $model->firstname,
                        'login' => $model->username,
                        'setPasswordLink' => Yii::$app->urlManagerFrontend->createUrl([
                            '/site/reset-password',
                            'token' => $model->password_reset_token
                        ]),
                        'isSubscribed' => $model->is_subscribed
                    ])
                    ->setFrom(Yii::$app->params['supportEmail'])
                    ->setTo($model->email)
                    ->setSubject('Login Credentials - EventDraw');
            }
        }

        try {
            Yii::$app->mailer->sendMultiple($messages);

            return 'Email(s) Sent Successfully';
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param $model
     * @return string
     */
    public static function sendWelcomeEmail($model): string
    {
        $template = EmailTemplate::find()->where(['template_type' => 'welcome-template'])->one();
        // echo $template['email_subject'];
        // print_r($template['email_subject']);die;

        // $message = Yii::$app
        //     ->mailer
        //     ->compose(
        //         'new-email-template-body', [
        //             'name' => $model->firstname,
        //             'body' => $template->template_image,
        //         ]
        //     )
        //     ->setFrom(Yii::$app->params['supportEmail'])
        //     ->setTo($model->email)
        //     ->setCc('sales@eventdraw.com')
        //     ->setSubject($template->heading);

        $message = Yii::$app
            ->mailer
            ->compose(
                'new-email-template-body', [
                    'name' => $model->firstname,
                    'body' => $template['content'],
                ]
            )
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($model->email)
            ->setCc('sales@eventdraw.com')
            ->setSubject($template['email_subject']);

        try {
            Yii::$app->mailer->send($message);

            $model->last_email_date = date('Y-m-d H:i:s');
            $model->email_count++;

            $model->save();

            return 'Welcome Email Sent Successfully';
        }
        catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param array $email
     * @param string $filename
     * @return bool
     */

    public static function sendTemplateChangeMiail($email,$mailBody,$username): string
    {
        

        $message = Yii::$app
            ->mailer
            ->compose(
                'new-email-template-body', [
                    'name' => $username,
                    'body' => $mailBody,
                ]
            )
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($model->email)
            ->setCc('sales@eventdraw.com')
            ->setSubject('Template Change');

        try {
            Yii::$app->mailer->send($message);            
            return 'Email Sent Successfully';
        }
        catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public static function sendEmailToBulkUser(array $email, string $filename = null, string $send_from = null): bool
    {

        $messages = [];

        EmailsSent::deleteAll();

        if (isset($email['users'])) {

            foreach ($email['users'] as $user) {
                $model = User::find()->where(['email' => $user])->one();

                $emailSent = new EmailsSent();
                $emailSent->email = $model->email;

                if ($emailSent->save()) {
                    $message = Yii::$app->mailer->compose(
                        'bulk-email-template',
                        [
                            'name' => $model->firstname,
                            'email' => $email,
                            'user' => $model->email,
                            'isSubscribed' => $model->is_subscribed,
                        ]
                    )
                        //->setFrom(ucfirst($send_from))
                        ->setFrom(array(ucfirst($send_from) => substr(ucfirst($send_from), 0, strpos(ucfirst($send_from), '@')) .'@EventDraw'))
                        ->setTo($model->email)
                        ->setSubject($email['subject']);

                    if (!empty($filename)) {
                        $message = $message->attach("uploads/email-attachments/{$filename}");
                    }
                    $email_body = self::getEmailBody($model->firstname,$email,$model->email,$model->is_subscribed);
                    try{
                        Yii::$app->mailer->send($message);

                        $model->email_count++;
                        $model->last_email_date = date('Y-m-d H:i:s');
                        $model->save();

                        $emailSentModel = EmailsSent::find()->where(['email' => $model->email])->one();
                        $emailSentModel->date_time = date('Y-m-d H:i:s');
                        $emailSentModel->is_sent = 1;
                        $emailSentModel->subject = $email['subject'];
                        $emailSentModel->email_body = $email_body;
                        $emailSentModel->save();

                        $messages[] = $message;
                    }
                    catch(\Exception $ex){
                        throw new ServerErrorHttpException($ex->getMessage(), '500');
                    }
                }
            }
        }

        if ($messages) {
            Yii::$app->session->setFlash('success', 'You email has been successfully sent to ' . count($messages) . ' user(s)');
        } else {
            Yii::$app->session->setFlash('error', 'There was an error sending your message.');
        }

        return true;
    }

    public static function getEmailBody($name,$email,$user,$isSubscribed)
    {
         $body = '<!doctype html>
<html>
<head>
    <title>Email</title>
    <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */
        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */
        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            Margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            Margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #fff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .footer {
            clear: both;
            padding-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            Margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            Margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        .powered-by a {
            text-decoration: none;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            Margin: 20px 0;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important;
            }

            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important;
            }

            table[class=body] .content {
                padding: 0 !important;
            }

            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table[class=body] .btn table {
                width: 100% !important;
            }

            table[class=body] .btn a {
                width: 100% !important;
            }

            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }
    </style>
</head>
<body class="">
<table border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <table class="main">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>';
         if(!empty($email["heading"]))
             {
             $body.= "<h3>". $email["heading"] ."</h3>";
             }
         $body.= '
                                        <p>Hi '.$name.',</p>
                                        <p>' .$email["body"].'</p>
                                        <p>
                                            EventDraw - Hospitality Event Diagramming Software
                                            <a href="http://www.eventdraw.com" target="_blank">www.EventDraw.com</a>
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                </table>

                <!-- START FOOTER -->
                <div class="footer">
                    <table border="0" cellpadding="0" cellspacing="0">';
                     if ($isSubscribed){
                 $body.= '<tr>
                                <td class="content-block">
                                    <br>
                                    <a href="'.Yii::$app->urlManagerFrontend->createUrl([
                                        "/site/toggle-user-subscription",
                                        "email" => $user,
                                        "subscribed" => !$isSubscribed,
                                    ]) .'">
                                        Unsubscribe
                                    </a>.
                                </td>
                          </tr>';
                     }
                     else
                         {
                              $body.= '<tr>
                                <td class="content-block">
                                    <br>
                                    <a href="'.Yii::$app->urlManagerFrontend->createUrl([
                                        "/site/toggle-user-subscription",
                                        "email" => $user,
                                        "subscribed" => $isSubscribed,
                                    ]) .'">
                                        Subscribe
                                    </a>.
                                </td>
                          </tr>';
                         }
                     $body.= '<td class="content-block powered-by">
                                Email Preferences <a href="http://www.eventdraw.com">EventDraw</a>.
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- END FOOTER -->

                <!-- END CENTERED WHITE CONTAINER -->
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>
';
         return $body;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function eligibleEmailUsers(array $data)
    {
        $session = Yii::$app->session;
        $pageSize = 1000;
        $page = $data['page'];
        $limit = $pageSize;
        $offset =  ($page * $pageSize) - $pageSize;
        $users = User::find()->all();
       

        $clients = Client::find()->all();
        $eligibleUsers = [];
        $selectedUsers = [];
        $totalSelectedUsers=0;

        if ($session->has('selectedUsers') && $session->get('selectedUsers')) {
            $selectedUsers = json_decode($session->get('selectedUsers'), true);
        }

        $i = 0;
        //print_r($users);die;
        
        $array_username     =   array();
        $new = array();
        foreach($users as $obj){
            $new_user       =   $obj->username;
            array_push($array_username,$new_user);
        }
        if ($data['type'] === 'username') {
            $array          =   explode(',',$data['filter']);
            $new = array();
            foreach($array  as $u){
                array_push($new,$u);
            }
        }

        if ($data['type'] === 'company') {
            $com_array          =   $data['filter'];
            $company_array = array();
            foreach($com_array  as $c){
                array_push($company_array,$c);
            }
        }
        $response['com_array']   =   $com_array;
        foreach ($users as $model) {
            $lastLogin = date('Y-m-d', $model->last_login);
            $lastEmail  =   date("Y-m-d",$model->last_email_date);
            $totSession =   $model->totSession;
            $userType   =   $model->userType;
            $status     =   $model->status;
            $siDate     =   $model->siDate;

            //AXF 27Apr 2023 
            //do not include user if it has status UserDoNotEmail = 1
            if ($model->UserDoNotEmail === 1 )
            {
               continue; 
            }
            
            if ($data['type'] === 'siDate' && ($data['filter'] != $siDate)) {
                continue;
            }
            
            if ($data['type'] === 'last-login-date' && ($data['filter'] >= $lastLogin)) {
                continue;
            }
            if ($data['type'] === 'last_email_date') {
                if ($data['filter'] === '') {
                    continue;
                }

                if ($data['filter'] === '0' && (date('Y-m-d', strtotime('-3 days')) > $lastEmail)) {
                    continue;
                }

                if ($data['filter'] === '1' && date('Y-m-d', strtotime('-5 days')) <= $lastEmail) {
                    continue;
                }
            }
            if($data['type']==='totSession'){
                if($data['filter']===''){
                    continue;
                }
                if ($data['filter'] === '0'
                    && !(
                        $totSession >= '0'
                        && $totSession <= '2'
                    )) {
                    continue;
                }
                if ($data['filter'] === '1'
                    && !(
                        $totSession >= '3'
                        && $totSession <= '5'
                    )) {
                    continue;
                }
                if ($data['filter'] === '2'
                    && !(
                        $totSession >= '5'
                        && $totSession <= '10'
                    )) {
                    continue;
                }
                if ($data['filter'] === '3'
                    && !(
                        $totSession >= '10'
                    )) {
                    continue;
                }
            }
            if ($data['type'] === 'last_login') {
                if ($data['filter'] === '') {
                    continue;
                }

                if ($data['filter'] === '0' && (date('Y-m-d', strtotime('-3 months')) > $lastLogin)) {
                    continue;
                }

                if ($data['filter'] === '1'
                    && !(
                        date('Y-m-d', strtotime('-3 months')) >= $lastLogin
                        && date('Y-m-d', strtotime('-6 months')) <= $lastLogin
                    )) {
                    continue;
                }

                if ($data['filter'] === '2' && date('Y-m-d', strtotime('-6 months')) <= $lastLogin) {
                    continue;
                }
            }

            if ($data['type'] === 'is-subscribed') {
                if ($data['filter'] === 'true' && !$model->is_subscribed) {
                    continue;
                } else if ($data['filter'] === 'false') {
                    continue;
                }
            }
            
            if ($data['type'] === 'username') {
                /*if (!empty($data['filter'])
                    in_array($model->username,$array)) {
                    continue;
                }*/
                if (!empty($data['filter']) && !in_array($model->username,$array)) {
                    continue;
                }
            }

            if ($data['type'] === 'company') {
                continue;
            }
            if($data['type']==='status'){
                if($data['filter']===''){
                    continue;
                }
                if ($data['filter'] === '9' && !($status == '9')) {
                    continue;
                }
                if ($data['filter'] === '10' && !($status == '10')) {
                    continue;
                }
                if ($data['filter'] === '11' && !($status == '11')) {
                    continue;
                }
            }

            if($data['type']==='userType'){
                if($data['filter']===''){
                    continue;
                }
                if ($data['filter'] === '0' && !($userType == '0')) {
                    continue;
                }
                if ($data['filter'] === '1' && !($userType == '1')) {
                    continue;
                }
                if ($data['filter'] === '2' && !($userType == '2')) {
                    continue;
                }
            }

            if ($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 0) {
                $selectedUsers[$i]['email'] = $model->email;
                $selectedUsers[$i]['isChecked'] = false;
            } else {
                if (($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 1) || array_search($model->email, array_column($selectedUsers, 'email')) === false) {
                    $selectedUsers[$i]['email'] = $model->email;
                    $selectedUsers[$i]['isChecked'] = true;
                }

                if($selectedUsers[$i]['isChecked']===true){
                    $totalSelectedUsers++;
                }
            }

            $eligibleUsers[$i] = $model->email;

            $i++;
        }

        $allUsers = $eligibleUsers;
        $allUsersCount = count($eligibleUsers);
        $eligibleUsers = array_slice($eligibleUsers, $offset, $limit);

        if ($data['type'] === 'company') {
            if (!empty($data['filter'])) {
                $eligibleUsers = [];
                $selectedUsers = [];
                $totalSelectedUsers=0;
                $i = 0;

                if ($session->has('selectedUsers') && $session->get('selectedUsers')) {
                    $selectedUsers = json_decode($session->get('selectedUsers'), true);
                }

                $client = Client::find()->where(['clientName' => $data['filter']])->one();

                if ($client->users) {
                    //$users = User::find()->where(['clientid' => $client->id])->all();
                    $users = User::find()->where(['clientid' => $client->id, 'UserDoNotEmail' => 0])->all();
                    
                     //AXF 27Apr 2023 
                    //do not include user if it has status UserDoNotEmail = 1
          

                    foreach ($users as $model) {
                        if ($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 0) {
                            $selectedUsers[$i]['email'] = $model->email;
                            $selectedUsers[$i]['isChecked'] = false;
                        } else {
                            if (($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 1) || array_search($model->email, array_column($selectedUsers, 'email')) === false) {
                                $selectedUsers[$i]['email'] = $model->email;
                                $selectedUsers[$i]['isChecked'] = true;
                            }

                            if($selectedUsers[$i]['isChecked']===true){
                                $totalSelectedUsers++;
                            }
                        }

                        $i++;
                        $eligibleUsers[] = $model->email;
                    }

                    $allUsers = $eligibleUsers;
                    $allUsersCount = count($eligibleUsers);
                    $eligibleUsers = array_slice($eligibleUsers, $offset, $limit);
                }
            }
        }



        $response['selectedUsers'] = $selectedUsers;
        $response['totalSelectedUsers'] = $totalSelectedUsers;
        $response['allusers']=$allUsers;
        $response['users'] = $eligibleUsers;
        $response['totalUsers'] = count($eligibleUsers);
        $response['totalPages'] = !empty($allUsersCount) ? ceil($allUsersCount / $pageSize) : 0;
        $response['data'] = $data;
        $session->set('selectedUsers', json_encode($selectedUsers));
        $session->set('exportUser', json_encode($eligibleUsers));
        //return $response['data'];
        $response['clients']    =   $clients;
        return $response;
    }
}
