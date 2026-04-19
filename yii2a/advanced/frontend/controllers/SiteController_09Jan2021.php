<?php
namespace frontend\controllers;

use common\models\GuestAllocation;
use common\models\GuestAllocationItems;
use common\models\Template;
use common\models\Userlogon;
use common\models\Usersettings;
use common\models\Event;
use common\models\ShapeModels;
use common\models\XmlShapes;
use common\models\XmlShapesBackup;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Security;
use yii\debug\models\search\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use  yii\web\Session;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    public static function allowedDomains() {
        return [
             '*',                        // star allows all domains
            'http://allocation.eventdraw.com.au',
            'http://test2.example.com',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
//                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => [
                            'login',
                            'signup',
                            'signout',
                            'index',
                            'error',
                            'about',
                            'contact',
                            'generate-link',
                            'get-link-info',
                            'get-link-json',
                            'save-guests',
                            'get-template-json',
                            'get-user-guest-links',
                            'request-password-reset',
                            'set-user-release',
                            'get-user-templates',
                            'login-new',
                            'reset-password',
                            'save-template-json',
                            'set-settings',
                            'get-settings',
                            'save-library',
                            'get-guid',
                            'check-guid',
                            'captcha',
                            'get-library',
                            'save-event-json',
                            'get-event-json',
                            'get-user-events',
                            'delete-user-event',
                            'update-user-event',
                            'get-models-json',
                            'export-pdf',
                            'save-template-preview',
                            'show-pdf',
                            'toggle-user-subscription',
                        ],
                        'allow' => true,
//                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','eventdraw'],
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
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin'                           => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ['POST'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    private function generateSecret($length = 20)
    {
        $security = new Security();
        $full = $security->generateRandomString($length);
        return substr($full, 0, $length);
    }


    public function actionToggleUserSubscription($email, $subscribed)
    {
        $this->layout = 'empty';
        $model = User::find()->where(['email' => $email])->one();

        if (!empty($model)) {
            $model->is_subscribed = !$subscribed;

            if ($model->save()) {
                return $this->render('email-subscription-message', ['isSubscribed' => $model->is_subscribed]);
            }
        }

        return $this->redirect(['/']);
    }

    public function actionSaveTemplatePreview()
    {
        $request = Yii::$app->request;

        $templateid = $request->post('templateid');

        $imageXML = $request->post('image');
        //replace all '_' to '+'
        $imageXML = str_replace('_', '+', $imageXML);

        //remove data:image/png;base64,
        $imageXML = str_replace('data:image/png;base64,', '', $imageXML);

        $templ = Template::findByID($templateid);
        if ($templ)
        {
            $fileNameWithPath = "site/media_srv/tmpl_images/" . $templ->id . '.png';
            if(file_put_contents($fileNameWithPath, base64_decode($imageXML) )) {
            }
            return $this->asJson('Done' );
        }
        else
        {
            return $this->asJson('Not found' );
        }


    }


    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

       public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->post('filename');

        $imageXML = $request->post('image');

        $pageWidth = $request->post('pageWidth');
        $pageHeight = $request->post('pageHeight');

//        if ($pageWidth >= $pageHeight)
//        {
//            $pageOrientation = 'L';
//        }
//        else
//        {
//            $pageOrientation = 'P';
//        }

        $pageOrientation = 'P';


        $imgLeft = $request->post('imgLeft');
        $imgTop = $request->post('imgTop');

        $mpdf = new \Mpdf\Mpdf( [
            'mode' => 'utf-8',
            'format' => [$pageWidth, $pageHeight],
            //'format' => 'A4',
            'orientation' => $pageOrientation,
            'margin_left' => $imgLeft,
            'margin_right' => 0,
            'margin_top' => $imgTop,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        //replace all '_' to '+'
        $imageXML = str_replace('_', '+', $imageXML);

        $fileNameWithPath = "site/tmp/".uniqid(rand(), true) . '.svg';
        $fileNameLocalPDF = "site/tmp/".uniqid(rand(), true) . '.pdf';

        $mpdf->SetCreator('EventDraw');

        if(file_put_contents($fileNameWithPath, base64_decode($imageXML) )) {
            $html = " <div> <img src=$fileNameWithPath> </div> ";

            $mpdf->WriteHTML($html);

        }

        $mpdf->Output($fileNameLocalPDF,  'F');

        $pdfResult = file_get_contents($fileNameLocalPDF);

        //delete temp files
        if (!unlink($fileNameWithPath)) {
        }

        if (!unlink($fileNameLocalPDF)) {
        }

        return base64_encode ($pdfResult);
   }


public function actionSaveEventJson()
    {
        //Get data from post request
           $request = Yii::$app->request;

            $eventname = $request->post('eventname');
            $userid = $request->post('userid');
            //$eventdate = $request->post('eventdate');

            if ($eventname) {
                $templateXML = $request->post('diagram');
                //replace all '_' to '+'
                $templateXML = str_replace('_', '+', $templateXML);


                $imageXML = $request->post('image');
                //replace all '_' to '+'
                $imageXML = str_replace('_', '+', $imageXML);

                $evnt = Event::findByName($eventname,$userid);
                    if (!$evnt)
                    {
                        $evnt = new Event();
                        $evnt->eventName= $eventname;
                        $evnt->userid = $userid;
                        $evnt->eventActive = 1;
                        //$evnt->eventdate = $eventdate;
                    }

                $evnt->xmlCode  = $templateXML;
                $evnt->imageCode  = $imageXML;

                $evnt->save(false);

                return $this->render('genlink');
            }
            else
            {
                //event id or name is empty
                return $this->render('generr');
            }

    }

    public function actionGetEventJson()
    {
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');

        $item =  Event::findOne(['id' => $eventid]);

        return $this->asJson($item);

    }

    public function actionGetUserEvents()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $evntNames = Event::getUserEvents($userid);

        return $this->asJson($evntNames);

    }

    public function actionDeleteUserEvent()
    {
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');

        $evnt = Event::findByID($eventid);
        if ($evnt)
        {
            $evnt->eventActive = 0;
            $evnt->save(false);
        }
        return $this->asJson(null);

    }

    public function actionUpdateUserEvent()
    {
        //Get data from post request
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');
        $eventname = $request->post('eventname');
        $eventdate = $request->post('eventdate');

            $evnt = Event::findByID($eventid);
            if ($evnt)
            {
                $evnt->eventName= $eventname;
                if ($eventdate == 'null')
                {
                    $evnt->eventdate= null;
                }
                else
                {
                    $evnt->eventdate= $eventdate;
                }

                $evnt->save(false);
            }


            return $this->render('genlink');
    }

    public function actionGetModelsJson()
    {
        $request = Yii::$app->request;
        //$userid = $request->post('userid');

        $item =  ShapeModels::find()->all();
        return $this->asJson($item);

    }
public function actionCheckGuid()
    {
        //get guid
        $guid = Yii::$app->getRequest()->getQueryParam('guid');

        //check user name with this guid
        $guidInfo = \common\models\Userlogon::findByGUID($guid);

        if ($guidInfo) {
            //remove used GUID record


            //authenticate user with this guid
            $userID = \common\models\User::findIdentity($guidInfo->userid);
            $guidInfo->delete();

            if ($userID) {
                Yii::$app->user->login($userID);

                //return $this->render('evdr/index');
                //open eventdraw site
                $this->layout = 'empty';
                Yii::$app->response->redirect('eventdraw');

            }
            else
            {
               return $this->asJson('Incorrect User' );
            }

        } else
        {
            return $this->asJson('Incorrect GUID' );
        }



    }
    public function actionGetGuid()
    {
        $request = Yii::$app->request;
        $username = $request->post('login');
        $userpsw =  $request->post('psw');

        $guidinfo = "error";
        //search user by username
        $userID = \common\models\User::findByUsername($username);
        if ($userID)
        {
            if ($userID->validatePassword($userpsw)) {
                $guidinfo = bin2hex(openssl_random_pseudo_bytes(16));
                //add record to userlogon table
                $userlogon = new \common\models\Userlogon();
                $userlogon->userid = $userID->id;
                $userlogon->guid = strval($guidinfo);
                $userlogon->save(false);
            }
        }

        return $this->asJson($guidinfo );
    }



    public function actionGetLinkInfo()
    {
        return $this->render('linkinfo');
    }
    public function actionGetTemplateJson()
    {
        $request = Yii::$app->request;
        $templid = $request->post('templateid');

        $item =  Template::findOne(['id' => $templid, 'templateActive' => 1]);

        return $this->asJson($item);

    }

    public function actionSetUserRelease()
    {
        $request = Yii::$app->request;
        $nrelease = $request->post('nrelease');

        //for this user - set nrelase
        if (Yii::$app->user->isGuest)
        {

        }
        else
        {
            Yii::$app->user->identity->setReleaseN($nrelease);

        }

    }

    public function actionGetSettings()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $item =  Usersettings::find()
            ->where(['userid' => $userid])
             ->all();

        return $this->asJson($item);
    }

public function actionGetLibrary()
    {
//        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
            $lib_name = $request->post('libname');

            $shape = XmlShapes::findByShapeName($lib_name);
            if ($shape)
            {
                return $shape->xmlCode;
            }
            else
            {
                return '';
            }
//        }
//        else
//            {
//                //user is not logged in
//                return $this->render('generr');
//            }
    }
    
public function actionSaveLibrary()
    {
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

           $xml_source = $request->post('xml');
           $lib_name = $request->post('libname');
           $serv_name = $request->post('servname');
           $userid = $request->post('userid');

           //update in database
            $shape = XmlShapes::findByShapeName($lib_name, $serv_name);

            if ($shape)
            {
                //create backup copy of exist shape
                $xmlBackupShape = new XmlShapesBackup();
                $xmlBackupShape->shapesid =$shape->id;
                $xmlBackupShape->shapesActive = $shape->shapesActive;
                $xmlBackupShape->shapesName = $shape->shapesName;
                $xmlBackupShape->xmlCode =$shape->xmlCode;
                $xmlBackupShape->serverName =$shape->serverName;
                $xmlBackupShape->update_user = $userid;
                $xmlBackupShape->save(false);

                $shape->xmlCode = $xml_source;
                $shape->save(false);

            }
            else
            {
                $xmlShape = new XmlShapes();
                $xmlShape->shapesActive = 1;
                $xmlShape->shapesName = $lib_name;
                $xmlShape->xmlCode = $xml_source;
                $xmlShape->serverName = $serv_name;
                $xmlShape->save(false);
            }

            //replace all '_' to '+'
            $xml_source = str_replace('_', '+', $xml_source);
            $xml_source = str_replace('~', '&', $xml_source);

            //for favourites saving to another folder
            if( $lib_name =='Favourites') {



                $clientID = 0;
                $userFoundedID = \common\models\User::findIdentity($userid) ;

                if ($userFoundedID)
                {
                    if ($userFoundedID->clientid) {
                        $clientID = $userFoundedID->clientid;
                    }
                }

                $fileName =  strval($clientID) . '_' . $lib_name ;
                $fileName = 'stencils_favourite/' . pathinfo($fileName, PATHINFO_FILENAME). '.xml';
            }
            else
            {
                $fileName = $lib_name;
                $fileName = 'stencils/' .pathinfo($fileName, PATHINFO_FILENAME) . '.xml';
            }

            $fileNameWithPath = "site/media_srv/".$fileName;
            $contents = $xml_source;
            $appended = date('_Y_m_d_H_i_s');
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);


            //copy source file if exist
            if (file_exists($fileNameWithPath)) {
                copy($fileNameWithPath, "site/media_srv/" . $fileName . $appended . '.' . $extension);
            }

            if(file_put_contents($fileNameWithPath,$contents )){

            }

            return $this->render('genlink');
        }
        else
        {
            //template id or name is empty
            return $this->render('generr');
        }
    }      
        
    public function actionSetSettings()
    {
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

            //check is settings for this user already exist or not
            $userSett =  Usersettings::findOne(['userid' => Yii::$app->user->identity->id]);

            if ($userSett)
            {

            }
            else
            {
                $userSett = new Usersettings();
                $userSett->userid = Yii::$app->user->identity->id;
            }

            $meas_unit = $request->post('meas_unit');
            if ($meas_unit) {
                $userSett->meas_unit = $meas_unit;
            }

            $userSett->save(false);

            return $this->render('genlink');
        }
        else
        {
            //template id or name is empty
            return $this->render('generr');
        }
    }
    
    public function actionGetUserTemplates()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $tmplNames = Template::getUserTemplates($userid);

        return $this->asJson($tmplNames);

    }

    public function actionGetUserGuestLinks()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $item =  GuestAllocation::find()
            ->where(['userid' => $userid])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->asJson($item);

    }


        public function actionGetLinkJson()
    {
        $request = Yii::$app->request;
        $linkID = $request->post('linkid');
        $linkInfo = \common\models\GuestAllocation::findLink($linkID);

        //need decode password value
        if ($linkInfo)
        {
            //encode base64
            $pwd = base64_encode($linkInfo->password);
            //reverse string
            $pwd = strrev($pwd);
            $linkInfo->password = $pwd ;

            $items =  GuestAllocationItems::findAll(['guestallocationid' => $linkInfo->id]);

            //add guest saved items if exist
            $totalinfo = [$linkInfo,$items];

        }
        else
        {
            $totalinfo = null;
        }
        return $this->asJson($totalinfo);
    }

    public function actionSaveGuests()
    {
        $request = Yii::$app->request;
        $linkID = $request->post('linkid');
        $guestJSON = json_decode($request->post('guests'));

        //get id of guest allocation by its link
        $linkInfo = \common\models\GuestAllocation::findLink($linkID);

        //delete all exist records for this id
        GuestAllocationItems::deleteAll(['guestallocationid' => $linkInfo->id]);

        if ($linkInfo)
        {
            //add records
            foreach ($guestJSON as $v) {
                $newrec = new GuestAllocationItems();
                $newrec->guestallocationid = $linkInfo->id;
                $newrec->guestname = $v->guestName;
                if($v->tableNumber){
                    $newrec->tablenumber = $v->tableNumber;
                }
                if($v->chairNumber){
                    $newrec->chairnumber = $v->chairNumber;
                }
                if($v->diet){
                    $newrec->diet = $v->diet;
                }
                if($v->seating){
                    $newrec->seating = $v->seating;
                }
                if($v->color){
                    $newrec->dietcolor = $v->color;
                }

                if($v->gift){
                    $newrec->gift = 1;
                }
                else
                {
                    $newrec->gift = 0;
                }
                $newrec->save();
            }
        }
        return $this->asJson($linkID);

    }
    public function actionGenerateLink()
    {
        //Get data from post request
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

            $allocName = $request->post('allocname');

            if ($allocName) {

                $allocExpiry = $request->post('expiry');
                $allocPsw = $request->post('password');
                $allocEmail = $request->post('email');
                $allocComments = $request->post('comments');
                $allocXML = $request->post('diagram');

                $allocLink = $this->generateSecret(20);
                $allocFullLink = 'https://allocation.eventdraw.com.au?id=' . $allocLink;

                $msgHTML = '<p>Follow the link below to edit Guest Allocation: ' . $allocName . '</p>'
                    . '<p>' . Html::a(Html::encode($allocFullLink), $allocFullLink) . '</p>';

                if ($allocExpiry) {
                    $msgHTML = $msgHTML . '<p> Expire date:' . $allocExpiry . '</p>';
                }
                if ($allocPsw) {
                    $msgHTML = $msgHTML . '<p> Password:' . $allocPsw . '</p>';
                }
                if ($allocComments) {
                    $msgHTML = $msgHTML . '<p> Comments:' . $allocComments . '</p>';
                }

                //if set email, try to send message with Guest Allocaiton link and info
                $message = Yii::$app->mailer->compose();

                $message->setFrom(Yii::$app->params['supportEmail']);
                $message->setTo(Yii::$app->user->identity->email);
                if ($allocEmail) {
                    $message->setCc($allocEmail);
                }
                $message->setSubject('Guest Allocation Link: ' . $allocName)
                    ->setHtmlBody($msgHTML)
                    ->send();


                $guestAlloc = new GuestAllocation();
                $guestAlloc->userid = Yii::$app->user->identity->id;
                $guestAlloc->allocationname = $allocName;
                $guestAlloc->expirydate = $allocExpiry;
                $guestAlloc->password = $allocPsw;
                $guestAlloc->comments = $allocComments;
                $guestAlloc->xmlcode = $allocXML;
                $guestAlloc->email = $allocEmail;
                $guestAlloc->allocationlink = $allocLink;

                $guestAlloc->save(false);

                return $this->render('genlink');
            }
            else
            {
                //allocation name is empty
                return $this->render('generr');
            }
        }
        else{
            return $this->goHome();
        }
    }

    public function actionSaveTemplateJson()
    {
        //Get data from post request
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

            $templateid = $request->post('templateid');

            if ($templateid) {
                $templateXML = $request->post('diagram');
                //replace all '_' to '+'
                $templateXML = str_replace('_', '+', $templateXML);

                $imageXML = $request->post('image');
                //replace all '_' to '+'
                $imageXML = str_replace('_', '+', $imageXML);

                //remove data:image/png;base64,
                $imageXML = str_replace('data:image/png;base64,', '', $imageXML);

                //if first symbol is ! - need create new template, else find by id
                if ($templateid[0] == '!')
                {
                    $templ = new Template();
                    $templ->templateName = substr($templateid,1);
                    $templ->templateActive = true;
                    $templ->templateDefault = false;
                }
                else
                {
                    $templ = Template::findByID($templateid);
                    if (!$templ)
                    {
                        $templ = new Template();
                        $templ->templateName = substr($templateid,1);
                        $templ->templateActive = true;
                        $templ->templateDefault = false;
                    }
                }

                $templ->xmlCode  = $templateXML;



                $templ->save(false);

                //save to tmpl_images
                $fileNameWithPath = "site/media_srv/tmpl_images/" . $templ->id . '.png';
                if(file_put_contents($fileNameWithPath, base64_decode($imageXML) )) {
                }

                return $this->render('genlink');
            }
            else
            {
                //template id or name is empty
                return $this->render('generr');
            }
        }
        else{
            return $this->goHome();
        }
    }

    public function actionSignout()
    {
        $model = new LoginForm();
        $model->password = '';
        return Yii::$app->getResponse()->redirect('http://www.eventdraw.com');
    }


    public function actionIndex()
    {

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->getResponse()->redirect('https://login.eventdraw.com.au/frontend/web/site/eventdraw');
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->getResponse()->redirect('https://www.eventdraw.com.au/login/advanced/frontend/web/site/eventdraw');
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionEventdraw()
    {
        //$this->layout = false;
        $this->layout = 'empty';
        return $this->render('evdr/index');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            //Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            //return $this->goHome();
            //return Yii::$app->getResponse()->redirect(Yii::$app->getHomeUrl());
            return Yii::$app->response->redirect(['site/login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
