<?php



namespace backend\components;



use common\models\EmailTemplate;

use common\models\Client;

use common\models\NewUserBroadcastEmailTemplates;

use common\models\User;

use common\models\EmailsSent;

use http\Exception;

use Yii;

use yii\web\ServerErrorHttpException;



/**

 * Class ClientEmailHelper

 */

class ClientEmailHelper

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

        $template = NewUserBroadcastEmailTemplates::find()->where(['order' => 1])->one();

        if(empty($model->clientName)){

            $message = Yii::$app

                ->mailer

                ->compose(

                    'new-email-template-body', [

                        'name' => $model->firstname,

                        'body' => $template->template_image,

                    ]

                )

                ->setFrom(Yii::$app->params['supportEmail'])

                ->setTo($model->email)

                ->setCc('sales@eventdraw.com')

                ->setSubject($template->heading);



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

        }else{

            $message = Yii::$app

                ->mailer

                ->compose(

                    'new-email-template-body', [

                        'name' => $model->clientName,

                        'body' => $template->template_image,

                    ]

                )

                ->setFrom(Yii::$app->params['supportEmail'])

                ->setTo($model->email)

                ->setCc('sales@eventdraw.com')

                ->setSubject($template->heading);



            try {

                Yii::$app->mailer->send($message);



                // $model->last_email_date = date('Y-m-d H:i:s');

                // $model->email_count++;



                // $model->save();



                return 'Welcome Email Sent Successfully';

            }

            catch (Exception $exception) {

                return $exception->getMessage();

            }

        }

    }





    public static function mail_template_variables_data($content,$data){



        $content = str_replace('{{full_name}}',$data['first_name'], $content);

        $content = str_replace('{{email_address}}',$data['email_address'], $content);



        //print_r($data['first_name']);die;

        return $content;

    }

    public static function sendEmailToBulkClients(array $email, string $filename = null, string $send_from = null,$email_template_id=null): bool

    {

        //print_r($email_template_id);die;



        $emailTemplate = new EmailTemplate();

        $emailTemplate = emailTemplate::find()->where(['id' => $email_template_id])->one(); 

        $messages = [];

         

        EmailsSent::deleteAll();

        if (isset($email['users'])) {

            $mail_data                  =   array();

            foreach ($email['users'] as $user) {

                $model = Client::find()->where(['clientEmail' => $user])->one();



                $emailSent = new EmailsSent();

                $emailSent->email = $model->clientEmail;

                $new_client_data        =   array(

                    'first_name'=>$model->clientName,

                    'email_address'=>$model->clientEmail,

                );

                array_push($mail_data,$new_client_data);

                if ($emailSent->save()) {

                    $message = Yii::$app->mailer->compose(

                        'bulk-email-template',

                        [

                            'name' => $model->clientName,

                            'email' => $email,

                            'user' => $model->clientEmail,

                        ]

                    )

                        ->setFrom($send_from)

                        ->setTo($model->clientEmail)

                    
                        ->setSubject($email['subject']);



                    if (!empty($filename)) {

                        $message = $message->attach("uploads/email-attachments/{$filename}");

                    }

                    $mail_template_variables_data   =   self::mail_template_variables_data($email["body"],$new_client_data);



                    //print_r($mail_template_variables_data);die;

                    $email_body = self::getEmailBodyClient($model->clientName,$email,$model->clientEmail,$isSubscribed,$mail_template_variables_data);

                    try{

                        $message = $message->setHtmlBody( $email_body);

                        Yii::$app->mailer->send($message);



                        // $model->email_count++;

                        // $model->last_email_date = date('Y-m-d H:i:s');

                        // $model->save();



                        $emailSentModel = EmailsSent::find()->where(['email' => $model->clientEmail])->one();

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

            Yii::$app->session->setFlash('success', 'You email has been successfully sent to ' . count($email['users']) . ' user(s)');

        } else {

            Yii::$app->session->setFlash('error', 'There was an error sending your message.');

        }



        return true;

    }





    public static function getEmailBodyClient($name,$email,$user,$isSubscribed='',$mail_template_variables_data)

    {

        //print_r($mail_template_variables_data);die;

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

                                        

                                        <p>' .$mail_template_variables_data.'</p>

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



        //print_r($body);die;

        return $body;

    }





    

    public static function eligibleEmailClients(array $data)

    {

        $session = Yii::$app->session;

        $pageSize = 1000;

        $page = $data['page'];

        $limit = $pageSize;

        $offset =  ($page * $pageSize) - $pageSize;

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

        foreach($clients as $obj){

            $new_user       =   $obj->clientName;

            array_push($array_username,$new_user);

        }

        if ($data['type'] === 'username') {

            $array          =   explode(',',$data['filter']);

            $new = array();

            foreach($array  as $u){

                array_push($new,$u);

            }

        }





        $array_clientEmail     =   array();

        $new = array();

        foreach($clients as $obj){

            $new_email       =   $obj->clientEmail;

            array_push($array_clientEmail,$new_email);

        }

        if ($data['type'] === 'clientEmail') {

            $array_email          =   explode(',',$data['filter']);

            $new_client = array();

            foreach($array_email  as $u_c){

                array_push($new_client,$u_c);

            }

        }



        $response['com_array']   =   $com_array;

        foreach ($clients as $model) {

            $userType   =   $model->clientType;

            $status     =   $model->status;

            if ($data['type'] === 'username') {

                if (!empty($data['filter']) && !in_array($model->clientName,$array)) {

                    continue;

                }

            }

            if ($data['type'] === 'clientEmail') {

                if (!empty($data['filter']) && !in_array($model->clientEmail,$array_email)) {

                    continue;

                }

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

                $selectedUsers[$i]['email'] = $model->clientEmail;

                $selectedUsers[$i]['isChecked'] = false;

            } else {

                if (($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 1) || array_search($model->clientEmail, array_column($selectedUsers, 'email')) === false) {

                    $selectedUsers[$i]['email'] = $model->clientEmail;

                    $selectedUsers[$i]['isChecked'] = true;

                }



                if($selectedUsers[$i]['isChecked']===true){

                    $totalSelectedUsers++;

                }

            }



            $eligibleUsers[$i] = $model->clientEmail;

            $i++;

        }



        //$allUsers = $eligibleUsers;

        //$allUsersCount = count($eligibleUsers);

        //$eligibleUsers = array_slice($eligibleUsers, $offset, $limit);

        //print_r($allUsers);

        //print_r($eligibleUsers);die;

        $array_clients_list         =   '<table class="table table-bordered" id="clientData_dataTable">

                    <thead>

                        <tr>

                            <th><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th>

                            <th>ID</th>

                            <th>Client Name</th>

                            <th>Client Email</th>

                            <th></th>

                            <th>Created</th>

                            <th>Updated</th>

                            <th>Client Payment</th>

                            <th>Show Max Capacity Plans</th>

                            <th>Allow EventDraw Cloud</th>

                            <th>Allow Favourite Stencils</th>

                            <th>Status</th>

                            <th>Expiry Date</th>

                            <th></th>

                        </tr>

                    </thead>

                    <tbody id="clientData">';

        if(!empty($eligibleUsers)){foreach($eligibleUsers as $e_users){

            $Client = new Client();

            $client_info = Client::find()->where(['clientEmail' => $e_users])->one();

            if($client_info->clientPayment==1){

                $clientPayment  =   'Yes';

            }else{

                $clientPayment  =   'No';

            }

            if($client_info->ShowMaxCapPlans=='1'){

                $ShowMaxCapPlans    =   'Yes';

            }else{

                $ShowMaxCapPlans    =   'No';

            }

            if($client_info->AllowSaveCloud=='1'){

                $AllowSaveCloud     =   'Yes';

            }else{

                $AllowSaveCloud     =   'No';

            }

            if($client_info->AllowFavouriteStencils=='1'){

                $AllowFavouriteStencils         =   'Yes';

            }else{

                $AllowFavouriteStencils         =   'No';

            }

            if($client_info->status=='10'){

                $userStatus                     =   'Full Version';

            }elseif($client_info->status==9){

                $userStatus                     =   'Trail';

            }else if($client_info->status==11){

                $userStatus                     =   'Custom Trail';

            }else{

                $userStatus                     =   '';

            }

            if(!empty($client_info->expiry_date)){

                $expiry_date                    =   date('d/m/Y',$client_info->expiry_date);

            }else{

                $expiry_date                    =   '<span style="color:red">(not set)</span>';

            }

            $array_clients_list.='<tr data-key="'.$client_info->id.'">

                <td>

                    <input type="checkbox"  value="'.$client_info->clientEmail.'" name="users[]" class="user-checkbox"/>

                    

                </td>

                <td><a href="/backend/web/client/update?id='.$client_info->id.'" title="Edit">'.$client_info->id.'</a></td>

                <td>'.$client_info->clientName.'</td>

                <td>'.$client_info->clientEmail.'</td>

                <td>

                    <a href="/backend/web/client/template?id='.$client_info->id.'"><span class="glyphicon glyphicon-list-alt"></span></a>

                    <a href="/backend/web/client/settings?id='.$client_info->id.'"><span class="glyphicon glyphicon-cog"></span></a>

                </td>

                <td>'.date('d/m/Y H:i:s',$client_info->created_at).'</td>

                <td>'.date('d/m/Y H:i:s',$client_info->updated_at).'</td>

                <td>'.$clientPayment.'</td>

                <td>'.$ShowMaxCapPlans.'</td>

                <td>'.$AllowSaveCloud.'</td>

                <td>'.$AllowFavouriteStencils.'</td>

                <td>'.$userStatus.'</td>

                <td>'.$expiry_date.'</td>

                <td>

                    <a href="/backend/web/client/view?id='.$client_info->id.'" title="View" aria-label="View" data-pjax="0">

                    <span class="glyphicon glyphicon-eye-open"></span></a> 

                    <a href="/backend/web/client/update?id='.$client_info->id.'" title="Update" aria-label="Update" data-pjax="0">

                    <span class="glyphicon glyphicon-pencil"></span></a> 

                    <a href="/backend/web/client/delete?id='.$client_info->id.'" title="Delete" aria-label="Delete" data-pjax="0" data-confirm="Are you sure you want to delete this item?" data-method="post"><span class="glyphicon glyphicon-trash"></span>

                    </a>

                </td>

            </tr>';

        } }

        $array_clients_list.='</tbody>

                </table>';

        $response['users']         =   $array_clients_list;

        return $response;

        // $response['selectedUsers'] = $selectedUsers;

        // $response['totalSelectedUsers'] = $totalSelectedUsers;

        // $response['allusers']=$allUsers;

        // $response['users'] = $eligibleUsers;

        // $response['totalUsers'] = count($eligibleUsers);

        // $response['totalPages'] = !empty($allUsersCount) ? ceil($allUsersCount / $pageSize) : 0;

        // $response['data'] = $data;

        // $session->set('selectedUsers', json_encode($selectedUsers));

        // $session->set('exportUser', json_encode($eligibleUsers));

        // //return $response['data'];

        // $response['clients']    =   $clients;

        // return $response;

    }


    public static function eligibleEmailClientsCheckbox(array $data)

    {

        $session = Yii::$app->session;

        $pageSize = 1000;

        $page = $data['page'];

        $limit = $pageSize;

        $offset =  ($page * $pageSize) - $pageSize;

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

        foreach($clients as $obj){

            $new_user       =   $obj->clientName;

            array_push($array_username,$new_user);

        }

        if ($data['type'] === 'username') {

            $array          =   explode(',',$data['filter']);

            $new = array();

            foreach($array  as $u){

                array_push($new,$u);

            }

        }





        $array_clientEmail     =   array();

        $new = array();

        foreach($clients as $obj){

            $new_email       =   $obj->clientEmail;

            array_push($array_clientEmail,$new_email);

        }

        if ($data['type'] === 'clientEmail') {

            $array_email          =   explode(',',$data['filter']);

            $new_client = array();

            foreach($array_email  as $u_c){

                array_push($new_client,$u_c);

            }

        }



        $response['com_array']   =   $com_array;

        foreach ($clients as $model) {

            $userType   =   $model->clientType;

            $status     =   $model->status;

            if ($data['type'] === 'username') {

                if (!empty($data['filter']) && !in_array($model->clientName,$array)) {

                    continue;

                }

            }

            if ($data['type'] === 'clientEmail') {

                if (!empty($data['filter']) && !in_array($model->clientEmail,$array_email)) {

                    continue;

                }

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

                $selectedUsers[$i]['email'] = $model->clientEmail;

                $selectedUsers[$i]['isChecked'] = false;

            } else {

                if (($data['isSelectAllClicked'] == 1 && $data['selectAll'] == 1) || array_search($model->clientEmail, array_column($selectedUsers, 'email')) === false) {

                    $selectedUsers[$i]['email'] = $model->clientEmail;

                    $selectedUsers[$i]['isChecked'] = true;

                }



                if($selectedUsers[$i]['isChecked']===true){

                    $totalSelectedUsers++;

                }

            }



            $eligibleUsers[$i] = $model->clientEmail;

            $i++;

        }



        //$allUsers = $eligibleUsers;

        //$allUsersCount = count($eligibleUsers);

        //$eligibleUsers = array_slice($eligibleUsers, $offset, $limit);

        //print_r($allUsers);

        //print_r($eligibleUsers);die;

        
        $array_clients_list     =   '';
        if(!empty($eligibleUsers)){foreach($eligibleUsers as $e_users){

            $Client = new Client();

            $client_info = Client::find()->where(['clientEmail' => $e_users])->one();

            if($client_info->clientPayment==1){

                $clientPayment  =   'Yes';

            }else{

                $clientPayment  =   'No';

            }

            if($client_info->ShowMaxCapPlans=='1'){

                $ShowMaxCapPlans    =   'Yes';

            }else{

                $ShowMaxCapPlans    =   'No';

            }

            if($client_info->AllowSaveCloud=='1'){

                $AllowSaveCloud     =   'Yes';

            }else{

                $AllowSaveCloud     =   'No';

            }

            if($client_info->AllowFavouriteStencils=='1'){

                $AllowFavouriteStencils         =   'Yes';

            }else{

                $AllowFavouriteStencils         =   'No';

            }

            if($client_info->status=='10'){

                $userStatus                     =   'Full Version';

            }elseif($client_info->status==9){

                $userStatus                     =   'Trail';

            }else if($client_info->status==11){

                $userStatus                     =   'Custom Trail';

            }else{

                $userStatus                     =   '';

            }

            if(!empty($client_info->expiry_date)){

                $expiry_date                    =   date('d/m/Y',$client_info->expiry_date);

            }else{

                $expiry_date                    =   '<span style="color:red">(not set)</span>';

            }

            $array_clients_list.='<label><input type="checkbox"  value="'.$client_info->clientEmail.'" name="user_id[]" class="user-checkbox"/> '.$client_info->clientEmail.' </label><br/>';

        } }
        $response['users']         =   $array_clients_list;

        return $response;

        // $response['selectedUsers'] = $selectedUsers;

        // $response['totalSelectedUsers'] = $totalSelectedUsers;

        // $response['allusers']=$allUsers;

        // $response['users'] = $eligibleUsers;

        // $response['totalUsers'] = count($eligibleUsers);

        // $response['totalPages'] = !empty($allUsersCount) ? ceil($allUsersCount / $pageSize) : 0;

        // $response['data'] = $data;

        // $session->set('selectedUsers', json_encode($selectedUsers));

        // $session->set('exportUser', json_encode($eligibleUsers));

        // //return $response['data'];

        // $response['clients']    =   $clients;

        // return $response;

    }


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
                    $users = User::find()->where(['clientid' => $client->id])->all();
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

