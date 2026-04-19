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

use \common\models\Userbulletin;
use yii\db\Query;

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
                            'get-user-events-short',
                            'convert-pdf',
                            'get-bulletin-message',
                            'export-pdf-area',
                            'set-usertype',
                            'get-pdf',
                            'share-event',
                            'save-event-json-new',
                            'get-matterport-link',
                            'get-template-matterport',
                            'get-user-templates-json',
'create-template-folder','rename-template-folder', 'delete-template-folder','set-template-folder',
'create-event-folder','rename-event-folder','delete-event-folder','set-event-folder'
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

public function actionGetTemplateMatterport()
    {
        $request = Yii::$app->request;
        $template_id = $request->post('templateid');
        $token = $request->post('token');
        
        $template=\common\models\Template::findOne(['id' => $template_id]);
        if ($template)
        {
            $Matterport=\common\models\Matterport::findOne(['id' => $template->matterportid]);

            if ($Matterport)
            {

           return 'https://matterport.eventdraw.com.au/?token=' . $token . '&building=' .$Matterport->mat;
            }
            else 
            {
                return '';
            }
        }

        else 
            {
                return '';
            }

    }
    
public function actionGetMatterportLink()
    {
        $request = Yii::$app->request;
        $event_id = $request->post('eventid');

        $ThreedEvent=\common\models\ThreedEvents::findOne(['eventid' => $event_id]);
  
        $Matterport_assign=\common\models\MatterportAssign::findOne(['layout_id' =>$event_id ]);
       
                
        if ($Matterport_assign ){

            $Matterport=\common\models\Matterport::findOne(['id' => $Matterport_assign->matterport_id]);

            if ($Matterport)
            {
            return 'https://matterport.eventdraw.com.au/?token=' . $ThreedEvent->token . '&building=' .$Matterport->mat;
            }
            else 
            {
                return '';
            }
        }
        else{
            return '';
        }

        
    }

    public function actionGetBulletinMessage()
{
     $request = Yii::$app->request;
     $userid = $request->post('userid');

     $retValue = \common\models\BulletBoards::getUserBulletMessage($userid);
     

     return base64_encode ($retValue);
}


 public function setCountSavedFloorplans($userid)
    {

         $Events =  \common\models\Event::findAll([
          'userid' => $userid, 'eventActive' => 1,
          ]);
         if ($Events) {
             $recCount = count($Events);
          }
          else {
            $recCount = 0;
          }

            $user_update=\common\models\User::findOne(['id' => $userid]);
            if ($user_update) {
                $user_update->userSavedFloorplansCount =  $recCount;
                $user_update->save(false);
            }
            

    }

    public function saveStencil_S3($fileName, $dataXML)
    {
            $s3 = Yii::$app->get('s3');
            $result = $s3->put('eventdraw_data/' . $fileName , $dataXML);
       
    }
    
public function saveEvent_S3($eventID)
    {
        $evnt = \common\models\Event::findByIDAll($eventID);
        if ($evnt) 
        {
            $s3 = Yii::$app->get('s3');
            $result = $s3->put('eventdraw_data/events/' . strval($evnt->id) . '.xml', $evnt->xmlCode);
        }
        return 0;
    }
    public function loadEvent_S3($eventID)
    {
        $strXML = "";
        $fileNameLocal = "site/tmp/".uniqid(rand(), true) . '.xml';
        
        $s3 = Yii::$app->get('s3');
      
        $filename_looking = 'eventdraw_data/events/'. strval($eventID) . '.xml';
        $exist = $s3->exist($filename_looking);

        if ($exist) 
        {
            $result = $s3->commands()->get($filename_looking)->saveAs($fileNameLocal)->execute();
            $strXML = file_get_contents($fileNameLocal);

             //delete temp files
            if (!unlink($fileNameLocal)) {
            }


        }
        else 
        {
          $strXML = "";
          $evnt = Event::findByID($eventID);
            if ($evnt)
            {
                $strXML = $evnt->xmlCode;
            }
        }
        

       

        
        return $strXML;
    }

    public function saveTemplate_S3($templateID)
    {
        $tmpl = \common\models\Template::findByID($templateID);
        if ($tmpl) 
        {
            $s3 = Yii::$app->get('s3');
            $result = $s3->put('eventdraw_data/templates/' . strval($tmpl->id) . '.xml', $tmpl->xmlCode);
        }
        return 0;
    }

public function loadTemplate_S3($tempID)
    {
        $strXML = "";
        $fileNameLocal = "site/tmp/".uniqid(rand(), true) . '.xml';
        
        $s3 = Yii::$app->get('s3');
        
        $filename_looking = 'eventdraw_data/templates/'. strval($tempID) . '.xml';
        $exist = $s3->exist($filename_looking);

        if ($exist) 
        {
            $result = $s3->commands()->get($filename_looking)->saveAs($fileNameLocal)->execute();
            $strXML = file_get_contents($fileNameLocal);

             //delete temp files
            if (!unlink($fileNameLocal)) {
            }


        }
        else 
        {
          $strXML = "";
          $templ = Template::findByID($tempID);
            if ($templ)
            {
                $strXML = $templ->xmlCode;
            }
        }
        

       
        
        return $strXML;
    }


public function actionShareEvent()
    {
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');
        $shared = $request->post('shared');

        if (strlen($eventid) > 20)
        {
            $item =  Event::findOne(['event_uuid' => $eventid]);
        }
        else
        {
            $item =  Event::findOne(['id' => $eventid]);
        }

        if ( $item)
        {
            $item->event_shared = $shared ;
            $item->save(false);
        }


    }




 public function actionSetUsertype()
    {
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

            //check is settings for this user already exist or not
            $userInfo =  \common\models\User::findOne(['id' => Yii::$app->user->identity->id]);

            if ($userInfo)
            {
                $userType = $request->post('userType');
                if ($userType) {
                    $userInfo->userType = $userType;

                    if ($userType == 2) {
                        $userInfo->AllowSaveCloud =1;
                    }
                    $userInfo->save(false);
                }
            }
            
          

            return $this->render('genlink');
        }
        else
        {
                return $this->render('generr');
        }
    }

public function actionGetPdf()
    {
             $request = Yii::$app->request;

        $imageXML = $request->post('imageXML');

       
        $format = 'pdf';
        $bg = 'none';
        $base64 = '0';
        $embedXml = '0';
        $crop = $request->post('crop');
        $filename = uniqid(rand(), true) . '.pdf';

        $url = "https://eventdraw.site:3000";
        // $url = "http://eventdraw.site:8000";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "format=" . $format . "&bg=" . $bg . "&crop=" . $crop . "&base64=" . $base64 . "&embedXml=" . $embedXml . "&xml=" . base64_decode($imageXML) . "&filename=" .  $filename);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        #return  "format=" . $format . "&bg=" . $bg . "&crop=" . $crop . "&base64=" . $base64 . "&embedXml=" . $embedXml . "&xml=" . base64_decode($imageXML) . "&filename=" .  $filename;
        return base64_encode ($resp);


    }


public function actionConvertPdf()
    {
        $request = Yii::$app->request;

        $imageXML = $request->post('imageXML');

        $imageXML = substr($imageXML, 21);

        $imageXML = str_replace('_', '+', $imageXML);
        $imageXML = str_replace('~', '&', $imageXML);

        $fileNameLocalPDF = "site/tmp/".uniqid(rand(), true) . '.pdf';

        if(file_put_contents($fileNameLocalPDF, base64_decode($imageXML) )) {

        }


        $fileNameLocalImage = "site/tmp/".uniqid(rand(), true) . '.png';

        $im = new \Imagick();

        $im->setResolution(300, 300);
        $im->readimage($fileNameLocalPDF);
        $im->setImageFormat('png');
        $im->writeImage($fileNameLocalImage);
        $im->clear();
        $im->destroy();

        $jpgContents = file_get_contents($fileNameLocalImage);

        //delete temp files
        if (!unlink($fileNameLocalImage)) {
        }
        if (!unlink($fileNameLocalPDF)) {
        }

        return base64_encode ($jpgContents);
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

 public function actionExportPdfArea()
    {
        $request = Yii::$app->request;
        $imageXML = $request->post('image');

        $imageXML = str_replace('_', '+', $imageXML);
        $imageXML = str_replace('~', '&', $imageXML);

        $pageOrientation = $request->post('orientation');
        $pageFormat = $request->post('pageformat');

        //$imageXML = substr($imageXML, 22);

           $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => $pageFormat,
            'orientation' => $pageOrientation,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        $mpdf->SetCreator('EventDraw');

        $html = ' <table style="width:100%;height:100%;"> ';
        $html = $html  . '<tr align="center" >';
        $html = $html  . '<td align="center" >';
        $html = $html  . '<img  style="display:block;"  src="' . $imageXML . '" alt="Selected area" >';            
        $html = $html  . '</td>';                
        $html = $html  . '</tr>';
        $html = $html  . '</table>';

        //var_dump($html); die();

        $mpdf->WriteHTML($html);

        $fileNameLocalPDF = "site/tmp/" . uniqid(rand(), true) . '.pdf';


        $mpdf->Output($fileNameLocalPDF, 'F');


        $pdfResult = file_get_contents($fileNameLocalPDF);

        //delete temp files
        if (!unlink($fileNameLocalPDF)) {
        }

        return base64_encode ($pdfResult);
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

            //save to S3    
            $this::saveEvent_S3($evnt->id);

            //update count of saved floor plans for this user
            $this::setCountSavedFloorplans($userid);


            //save image to folder
            $fileNameWithPath = "site/media_srv/event_images/" . $evnt->id . '.png';

            if ($evnt->imageCode) {
                $file_data = base64_decode(substr($evnt->imageCode,22));

                if (file_put_contents($fileNameWithPath, $file_data)) {
                }
            }

                return $evnt->id;
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

       if (strlen($eventid) > 20)
        {
            $item =  Event::findOne(['event_uuid' => $eventid]);
        }
        else
        {
            $item =  Event::findOne(['id' => $eventid]);
        }

        return $this->asJson($item);

    }

public function actionSaveEventJsonNew()
    {
        //Get data from post request
        $request = Yii::$app->request;

        $eventname = $request->post('eventname');
        $eventid = $request->post('eventID');
        $userid = $request->post('userid');
        $eventdate = $request->post('eventdate');
        $eventinfo = $request->post('eventinfo');

        $eventinfo = str_replace('|', '+', $eventinfo);
        $eventinfo = str_replace('~', '&', $eventinfo);

        if (1==1) {
            $templateXML = $request->post('diagram');
            //replace all '_' to '+'
            $templateXML = str_replace('_', '+', $templateXML);


            $imageXML = $request->post('image');
            //replace all '_' to '+'
            $imageXML = str_replace('_', '+', $imageXML);

           
           if ($eventid)
            {
                $evnt =Event::findOne(['id' => $eventid]);
            }
            else
            {
                $evnt = Event::findByName($eventname,$userid);
            }
            
            if (!$evnt)
            {
                $evnt = new Event();
                $evnt->eventName= $eventname;
                $evnt->userid = $userid;
                $evnt->eventActive = 1;

            }

            $evnt->eventInfo  = $eventinfo;
            $evnt->xmlCode  = $templateXML;
            $evnt->imageCode  = $imageXML;
            if ($eventdate && $eventdate != 'null') {
                $evnt->eventdate = $eventdate;
            }
            else {
                $evnt->eventdate = NULL;
            }


            $evnt->save(false);
            
            //save to S3    
            $this::saveEvent_S3($evnt->id);


            //update count of saved floor plans for this user
            $this::setCountSavedFloorplans($userid);

            //save image to folder
            $fileNameWithPath = "site/media_srv/event_images/" . $evnt->id . '.png';


            if ($evnt->imageCode) {
                $file_data = base64_decode(substr($evnt->imageCode,22));

                if (file_put_contents($fileNameWithPath, $file_data)) {
                }
            }

            return $this->asJson($evnt->id );
        }
        else
        {
            //event id or name is empty
            return $this->render('generr');
        }

    }


public function actionGetUserEvents()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $evntNames = Event::getUserEvents($userid, true);

        return $this->asJson($evntNames);

    }

    public function actionGetUserEventsShort()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $evntNames = Event::getUserEvents($userid, false);

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

        //update count of saved floor plans for this user
        $this::setCountSavedFloorplans($userid);

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
            
            //save to S3    
            $this::saveEvent_S3($evnt->id);

            //update count of saved floor plans for this user
            $this::setCountSavedFloorplans($userid);

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


            //replace all '_' to '+'
            $lib_name = str_replace('_', '+', $lib_name);
            $lib_name = str_replace('~', '&', $lib_name);

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

            $this::saveStencil_S3($fileName, $contents);

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

            $copy_dist = $request->post('copy_dist');
            if ($copy_dist) {
                $userSett->copy_dist = $copy_dist;
            }

            $showSaveInfo = $request->post('showSaveInfo');

           
            if ($showSaveInfo == '0' || $showSaveInfo == '1') {
                $userSett->showSaveInfo = $showSaveInfo;

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
    
    public function actionGetUserTemplatesJson()
    {
        $request = Yii::$app->request;
        $userid = $request->post('userid');

        $tmplNames = Template::getUserTemplatesJson($userid);

        return $this->asJson($tmplNames);
    }

    public function actionCreateTemplateFolder()
    {
        
        $request = Yii::$app->request;
        $parentid = intval($request->post('parentid'));
        $folder_name = $request->post('folder_name');
        $user_id = Yii::$app->user->identity->id;
        
        //get client id from current user
        $clientID = 0;
        $userFoundedID = \common\models\User::findIdentity($user_id) ;

        if ($userFoundedID)
        {
            if ($userFoundedID->clientid) {
                $clientID = $userFoundedID->clientid;
            }
        }


        if ($clientID != 0)
        {
            //create new item in table 
            $TemplateFolder = new \common\models\ClientTemplateFolders();
            $TemplateFolder->clientid = $clientID;
            $TemplateFolder->templateid = -1;
            $TemplateFolder->folderName = $folder_name;
            $TemplateFolder->parentid = $parentid;


            $TemplateFolder->save(false);
            
            return $this->asJson($TemplateFolder->id);
        }
        else
        {
            Yii::$app->response->statusCode = 400;
            return $this->asJson('User is not assigned to Client');
        }
    }

    public function actionRenameTemplateFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = intval($request->post('folderid'));
            $folder_name = $request->post('folder_name');
            $user_id = Yii::$app->user->identity->id;
            
            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and folderid 

                $TemplateFolder =  \common\models\ClientTemplateFolders::findOne(['id' => $folderid, 'clientid' => $clientID]);

                if ($TemplateFolder)
                {
                    $TemplateFolder->folderName = $folder_name;
                    $TemplateFolder->save(false);
                    return $this->asJson('OK');
                }
                else
                {
                    Yii::$app->response->statusCode = 400;
                    return $this->asJson('Folder not found');
                }
            
                
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
            }
        }

     public function actionDeleteTemplateFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = intval($request->post('folderid'));
            $folder_name = $request->post('folder_name');
            $user_id = Yii::$app->user->identity->id;
            
            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and folderid 

                $TemplateFolder =  \common\models\ClientTemplateFolders::findOne(['id' => $folderid, 'clientid' => $clientID]);

                if ($TemplateFolder)
                {
                    //check any items in this folders
                    $FolderItems =  \common\models\ClientTemplateFolders::findOne(['parentid' => $folderid]);

                    if ($FolderItems)
                    {
                        Yii::$app->response->statusCode = 400;
                        return $this->asJson('Folder is not empty');
                    }
                    else
                    {
                        $TemplateFolder->delete();
                        return $this->asJson('OK');
                    }

                    
                }
                else
                {
                    Yii::$app->response->statusCode = 400;
                    return $this->asJson('Folder not found');
                }
            
                
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
            }
        }



    public function actionSetTemplateFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = intval($request->post('folderid'));
            $itemids = $request->post('itemids');
            $myArray = explode(',', $itemids);

            $user_id = Yii::$app->user->identity->id;
            
            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and id

                $arrlength = count($myArray);
                for($x = 0; $x < $arrlength; $x++) { 

                 $templateid =   $myArray[$x];
                 if (substr($templateid, 0,  3) == 'tpl')
                 {
                     $templateid  = substr($templateid, 3);
                 }
                 
                $TemplateFolder =  \common\models\ClientTemplateFolders::findOne(['templateid' => intval($templateid), 'clientid' => $clientID]);

                if ($TemplateFolder)
                {
                    $TemplateFolder->parentid = $folderid;
                    $TemplateFolder->save(false);
                }
                else
                {
                     $TemplateFolder = new \common\models\ClientTemplateFolders();
                     $TemplateFolder->clientid = $clientID;
                     $TemplateFolder->templateid = intval($templateid);
                     $TemplateFolder->folderName = "";
                     $TemplateFolder->parentid = $folderid;
                     $TemplateFolder->save(false);
                }
            
                }
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
            }
        }


    public function actionCreateEventFolder()
    {
        
        $request = Yii::$app->request;
        $parentid = $request->post('parentid');
        $folder_name = $request->post('folder_name');
        $user_id = Yii::$app->user->identity->id;
        
        if (substr($parentid, 0,  3) == 'evt')
            {
                $parentid  = intval(substr($parentid, 3));
            }

        //get client id from current user
        $clientID = 0;
        $userFoundedID = \common\models\User::findIdentity($user_id) ;

        if ($userFoundedID)
        {
            if ($userFoundedID->clientid) {
                $clientID = $userFoundedID->clientid;
            }
        }


        if ($clientID != 0)
        {
            //create new item in table 
            $EventFolder = new \common\models\ClientEventFolders();
            $EventFolder->clientid = $clientID;
            $EventFolder->eventid = -1;
            $EventFolder->folderName = $folder_name;
            $EventFolder->parentid = $parentid;


            $EventFolder->save(false);
            
            return $this->asJson($EventFolder->id);
        }
        else
        {
            Yii::$app->response->statusCode = 400;
            return $this->asJson('User is not assigned to Client');
        }
    }

    public function actionRenameEventFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = $request->post('folderid');
            $folder_name = $request->post('folder_name');
            $user_id = Yii::$app->user->identity->id;
            
             if (substr($folderid, 0,  3) == 'evt')
            {
                $folderid  = intval(substr($folderid, 3));
            }

            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and folderid 

                $EventFolder =  \common\models\ClientEventFolders::findOne(['id' => $folderid, 'clientid' => $clientID]);

                if ($EventFolder)
                {
                    $EventFolder->folderName = $folder_name;
                    $EventFolder->save(false);
                    return $this->asJson('OK');
                }
                else
                {
                    Yii::$app->response->statusCode = 400;
                    return $this->asJson('Folder not found');
                }
            
                
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
            }
        }


    public function actionDeleteEventFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = $request->post('folderid');
            $folder_name = $request->post('folder_name');
            $user_id = Yii::$app->user->identity->id;
            
            if (substr($folderid, 0,  3) == 'evt')
            {
                $folderid  = intval(substr($folderid, 3));
            }

            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and folderid 

                $EventFolder =  \common\models\ClientEventFolders::findOne(['id' => $folderid, 'clientid' => $clientID]);

                if ($EventFolder)
                {
                    //check any items in this folders
                    $FolderItems =  \common\models\ClientEventFolders::findOne(['parentid' => $folderid]);

                    if ($FolderItems)
                    {
                        Yii::$app->response->statusCode = 400;
                        return $this->asJson('Folder is not empty');
                    }
                    else
                    {
                        $EventFolder->delete();
                        return $this->asJson('OK');
                    }

                    
                }
                else
                {
                    Yii::$app->response->statusCode = 400;
                    return $this->asJson('Folder not found');
                }
            
                
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
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

    public function actionSetEventFolder()
        {
            
            $request = Yii::$app->request;
            $folderid = $request->post('folderid');
            $itemids = $request->post('itemids');
            $myArray = explode(',', $itemids);

            if (substr($folderid, 0,  3) == 'evt')
            {
                $folderid  = intval(substr($folderid, 3));
            }

            $user_id = Yii::$app->user->identity->id;
            
            //get client id from current user
            $clientID = 0;
            $userFoundedID = \common\models\User::findIdentity($user_id) ;

            if ($userFoundedID)
            {
                if ($userFoundedID->clientid) {
                    $clientID = $userFoundedID->clientid;
                }
            }


            if ($clientID != 0)
            {
                //find item for this client and id

                $arrlength = count($myArray);
                for($x = 0; $x < $arrlength; $x++) { 

                 $eventid =   $myArray[$x];
                 if (substr($eventid, 0,  3) == 'evt')
                 {
                     $eventid  = substr($eventid, 3);
                 }
                 
                $EventFolder =  \common\models\ClientEventFolders::findOne(['eventid' => intval($eventid), 'clientid' => $clientID]);

                if ($EventFolder)
                {
                    $EventFolder->parentid = $folderid;
                    $EventFolder->save(false);
                }
                else
                {
                     $EventFolder = new \common\models\ClientEventFolders();
                     $EventFolder->clientid = $clientID;
                     $EventFolder->eventid = intval($eventid);
                     $EventFolder->folderName = "";
                     $EventFolder->parentid = $folderid;
                     $EventFolder->save(false);
                }
            
                }
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
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

                 //save to S3    
                $this::saveTemplate_S3($templ->id);
                
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
    // public function actionEventdraw()
    // {
    //     //$this->layout = false;
    //     $this->layout = 'empty';
    //     return $this->render('evdr/index');
    // }
public function actionEventdraw()
    {
        $model = new Userbulletin();
        //$this->layout = false;
        $this->layout = 'empty';
        $user_id = Yii::$app->user->identity->id;
        $userType   =   Yii::$app->user->identity->userType;

        if ($userType > 0)
        {
        $userLog        = new Query();

        $broadCast      =   new Query();
        $userLog->select(['*'])->from('user_bulletin_log as U')
                ->where('U.user_id=:id',['id'=>$user_id])
                ->orderBy(['U.id' => SORT_DESC]);;
                //->limit(1);
        $userLogCommand   = $userLog->createCommand();
        $userLogResult    = $userLogCommand->queryAll(); 

        //print_r($userLogResult);
        // fetch data from new user broadcast email templates

        $broadCast      =   new Query();
        $broadCast->select(['*'])->from('new_user_broadcast_email_templates as T')
                          ->where('T.userType=:userType',['userType'=>$userType])
                          ->andWhere('T.display_as=:id',['id'=>'Yes'])
                          ->orderBy(['T.order' => SORT_ASC]);

        $broadData   = $broadCast->createCommand();
        $broadDataResult1    = $broadData->queryAll(); 
        
        if($userLogResult[0]['bulletin_id']==''){
            $broadDataResult            =   $broadDataResult1;
            $broad_id           =   $broadDataResult1[0]['id'];

            $model->user_id     =   $user_id;
            $model->bulletin_id =   $broad_id;
            // write update query to save data in userlogon table
            $model->save();
        }else{
            $logArray                =   array();
            foreach($userLogResult as $log){
                $new_log        =   $log['bulletin_id'];
                array_push($logArray,$new_log);
            }
            $log_array          =   implode(',',$logArray);

            $broad_id_array     =   array();
            foreach($broadDataResult1 as $broad){
                $new_id         =   $broad['id'];
                array_push($broad_id_array,$new_id);
            }
            $broadData              =   implode(',',$broad_id_array);

            $want_to_see            =   explode(",", $log_array);
            $current_user_can_see   =   explode(",", $broadData);

            
            // $result variable contain which bulletin not shown to user
            $result                 =   array_diff($current_user_can_see,$want_to_see);
            
            //echo reset($result);die;
            $array_diff_id          =   reset($result);
            if($array_diff_id ==''){
                $seen_bulletin_id   =   $userLogResult[0]['bulletin_id'];

                //echo $seen_bulletin_id;die;
                $logAfterSeen       =   new Query();

                $logAfterSeen->select(['*'])->from('user_bulletin_log as U')
                    ->where('U.user_id=:id',['id'=>$user_id])
                    ->andWhere('U.bulletin_id=:bulletin_id',['bulletin_id'=>$seen_bulletin_id])
                    ->orderBy(['U.id' => SORT_DESC]);
                $logAfterSeen   = $logAfterSeen->createCommand();
                $logAfterSeen    = $logAfterSeen->queryAll(); 
                $seenLength         =   sizeof($logAfterSeen);

                $bulletinAfterSeen      =   new Query();
                $bulletinAfterSeen->select(['*'])->from('new_user_broadcast_email_templates as T')
                                  ->where('T.userType=:userType',['userType'=>$userType])
                                  ->andWhere('T.display_as=:display_as',['display_as'=>'Yes'])
                                  ->andWhere('T.id=:id',['id'=>$seen_bulletin_id])
                                  ->orderBy(['T.id' => SORT_ASC]);

                $bulletinAfterSeen   = $bulletinAfterSeen->createCommand();
                $broadDataResult1    = $bulletinAfterSeen->queryAll(); 
                $no_of_time_seen = $broadDataResult1[0]['no_of_time_bulletin_show'];


                if($seenLength<$no_of_time_seen){
                    $model->user_id     =   $user_id;
                    $model->bulletin_id =   $seen_bulletin_id;
                    // write update query to save data in userlogon table
                    $model->save();
                    $broadDataResult    =   $broadDataResult1;
                }
            }else{
                $broadCast      =   new Query();
                $broadCast->select(['*'])->from('new_user_broadcast_email_templates as T')
                                  ->where('T.id=:id',['id'=>reset($result)]);

                $broadData   = $broadCast->createCommand();
                $broadDataResult    = $broadData->queryAll(); 

                $model->user_id     =   $user_id;
                $model->bulletin_id =   reset($result);


                $newBulletinCount   =   new Query();
                $newBulletinCount->select(['*'])->from('user_bulletin_log as U')
                                    ->where('U.user_id=:user_id',['user_id'=>$user_id])
                                    ->andWhere('U.bulletin_id=:bulletin_id',['bulletin_id'=>reset($result)]);

                $newBulletinCount   = $newBulletinCount->createCommand();
                $bulletinCount    = $newBulletinCount->queryAll();

                $model->user_id     =   $user_id;
                $model->bulletin_id =   reset($result);
                // write update query to save data in userlogon table
                $model->save();
                $broadDataResult    =   $broadDataResult;
            }
        }
        }
        return $this->render('evdr/index',
            [
                'broadDataResult'=>$broadDataResult
            ]);
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

            //var_dump($model);die();
             $userID = \common\models\User::findByUsername($model->email);
         
             if ($userID) {
                 Yii::$app->user->login($userID);

                 //return $this->render('evdr/index');
                 //open eventdraw site
                 $this->layout = 'empty';
                 Yii::$app->response->redirect('eventdraw');

             }
             else
             {
                return Yii::$app->response->redirect(['site/login']);
             }

            
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
