<?php
namespace frontend\controllers;

	
use common\models\BulletBoards;
use common\models\BulletBoardUsers;

use common\models\GuestAllocation;
use common\models\GuestAllocationItems;
use common\models\Template;
use common\models\Userlogon;
use common\models\Usersettings;
use common\models\Event;
use common\models\ShapeModels;
use common\models\XmlShapes;
use common\models\XmlShapesBackup;
use common\models\UserOpenEvents;
use common\models\ExtraLayouts;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Security;
// use yii\debug\models\search\User;
use common\models\User;
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
use \common\models\userBulletinModel;
use yii\db\Query;
use yii\db\Expression;
use common\models\FrontTemplate;
use common\models\EmailTemplate;
use common\models\TemplateChangeView;
use backend\components\EmailHelper;
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
                            'load-file-guid',
                            'save-guests',
                            'get-template-json',
                            'rename-template-item',
                            'rename-event-item',
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
                            'get-guid-desktop',
                            'check-guid',
                            'check-guid-desktop',
                            'get-api-token',
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
                            'get-user-events-short2',
                            'convert-pdf',
                            'get-bulletin-message',
                            'export-pdf-area',
                            'set-usertype',
                            'get-pdf',
                            'get-pdf3',
                            'share-event',
                            'momentus-copy',
                            'momentus-credentials',
                            'momentus-refresh',
                            'save-event-json-new',
                            'get-matterport-link',
                            'set-matterport-link',
                            'update-momentus-link',
                            'get-momentus-state',
                            'get-momentus-data',
                            'get-template-matterport',
                            'get-user-templates-json',
'create-template-folder','rename-template-folder', 'delete-template-folder','set-template-folder','delete-template','delete-event',
'create-event-folder','rename-event-folder','delete-event-folder','set-event-folder', 'get-all-clients','get-client-templates',
'set-event-update-status','get-event-edit-status','get-event-extra-layouts','svg2dwg','svg2dxf','get-dwg3','get-dxf3','clear-old-events',
'temp-ed-upload','get-temp-ed-file','add-new-stencil','get-new-stencils','save-new-stencil','delete-new-stencil',
'get-comments', 'add-comment','edit-comment','delete-comment', 'get-user-list','board-data-popup','users-video-data','get-user-template-changes','sso-login',
                        ],
                        'allow' => true,
//                        'roles' => ['?'],
                            'denyCallback' => function ($rule, $action) {
                                // Set return URL and redirect to login page
                                Yii::$app->user->setReturnUrl(Yii::$app->request->url);
                                return Yii::$app->response->redirect(['site/login']);
                            },

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
   
    private function getUserClient($userid)
    {
        $user_info=\common\models\User::findOne(['id' => $userid]);
        if ($user_info) {
            //found user with this ID
            return  $user_info->clientid;
        }
        else
        {
            //not found user
            return -1;
        }

       
    }

function secondsToTimeAgo($seconds) {
    if ($seconds < 1) {
        return "just now";
    }
    
    $intervals = [
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,  
        'second' => 1
    ];
    
    foreach ($intervals as $unit => $secsPerUnit) {
        $div = $seconds / $secsPerUnit;
        if ($div >= 1) {
            $count = floor($div);
            $result = $count . " " . $unit;
            if ($count > 1) {
                $result .= "s"; // add 's' if more 1
            }
            return $result . " ago";
        }
    }
    
    return "just now";
}

  public function actionDeleteNewStencil()
    {
           $request = Yii::$app->request;

           $ID = $request->post('ID');


           $newStencil =  \common\models\NewStencils::findOne(['ID' => $ID]);
            if ($newStencil)
            {
                $newStencil->stencilActive = 0;
                $newStencil->save(false);
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return 'Not found Stencil with this ID';
            }

    }

   
function actionGetComments()
{
    $request = Yii::$app->request;
    $eventid = $request->get('eventid', $request->post('eventid'));

    $items = (new Query())
        ->select('T.*, USER.userfullname, ST.status_name')
        ->from('comments as T')
        ->leftJoin('user as USER', 'T.userid = USER.id')
        ->leftJoin('comm_status as ST', 'T.status = ST.id')
        ->where(['eventid' => $eventid])
        ->orderBy(['id' => SORT_DESC])
        ->all();

    $nodeMap = [];
    $tree = [];

    foreach ($items as $item) {
        if (isset($item['parentid']) && $item['parentid'] == $item['id']) {
            continue;
        }
        
        $now = time();
        $item['created_at'] = $this->secondsToTimeAgo($now - $item['created_at']);
        $item['updated_at'] = $this->secondsToTimeAgo($now - $item['updated_at']);

        $item['parent_comment'] = $item['parentid'];
        unset($item['parentid']);

        $item['status'] = $item['status_name'];
        unset($item['status_name']);

        $item['comment'] = $item['message'];
        unset($item['message']);

        $item['user_name'] = $item['userfullname'];
        unset($item['userfullname']);
        
        $item['child'] = [];
        $nodeMap[$item['id']] = $item;
    }

    foreach ($nodeMap as $id => &$node) {
        $parentId = $node['parent_comment'] ?? null;
        
        if ($parentId === null) {
            $tree[] = &$node;
        } 
        elseif (isset($nodeMap[$parentId]) && $parentId != $id) {
            $nodeMap[$parentId]['child'][] = &$node;
        }
    }
    unset($node);

    return $this->asJson($tree);
}

function actionAddComment()
{
    $request = Yii::$app->request;
    $eventid = $request->get('eventid', $request->post('eventid'));
    $userid = $request->get('userid', $request->post('userid'));
    $message = $request->get('message', $request->post('message'));
    $parentid = $request->get('parentid', $request->post('parentid'));

    $NewComment = new \common\models\Comments();
    $NewComment->eventid = $eventid;
    $NewComment->userid = $userid;
    $NewComment->message = $message;
    $NewComment->parentid = $parentid;
    $NewComment->save(false);
    $id = $NewComment->id; 

    $item = \common\models\Comments::find()
        ->select([
            'comments.*',
            'USER.userfullname',
            'ST.status_name'
        ])
        ->from('comments')
        ->leftJoin('user USER', 'comments.userid = USER.id')
        ->leftJoin('comm_status ST', 'comments.status = ST.id')
        ->where(['comments.id' => $id])
        ->asArray()
        ->one();

    $now = time();
    
    if ($item) {
        $item['created_at'] = $this->secondsToTimeAgo($now - $item['created_at']);
        $item['updated_at'] = $this->secondsToTimeAgo($now - $item['updated_at']);
        
        $item['parent_comment'] = $item['parentid'];
        $item['status'] = $item['status_name'];
        $item['comment'] = $item['message'];
        $item['user_name'] = $item['userfullname'];
        
        unset(
            $item['parentid'],
            $item['status_name'],
            $item['message'],
            $item['userfullname']
        );
    }

    return $this->asJson($item);
}

    function actionEditComment()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', $request->post('id'));
        $status = $request->get('status', $request->post('status'));
        $message = $request->get('message', $request->post('message'));


        $child = \common\models\Comments::findOne(['id' => $id]);

        if ($child)
        {
            if ($status)
            {
                $child->status = $status;
            }
            if ($message)
            {
                $child->message = $message;
            }
            $child->save(false);

            $item = \common\models\Comments::find()
                ->select([
                    'comments.*',
                    'USER.userfullname',
                    'ST.status_name'
                ])
                ->from('comments')
                ->leftJoin('user USER', 'comments.userid = USER.id')
                ->leftJoin('comm_status ST', 'comments.status = ST.id')
                ->where(['comments.id' => $id])
                ->asArray()
                ->one();

            $now = time();
    
            if ($item) {
                $item['created_at'] = $this->secondsToTimeAgo($now - $item['created_at']);
                $item['updated_at'] = $this->secondsToTimeAgo($now - $item['updated_at']);
                
                $item['parent_comment'] = $item['parentid'];
                $item['status'] = $item['status_name'];
                $item['comment'] = $item['message'];
                $item['user_name'] = $item['userfullname'];
                
                unset(
                    $item['parentid'],
                    $item['status_name'],
                    $item['message'],
                    $item['userfullname']
                );
        }
            return $this->asJson($item);
        }
        else
        {
            Yii::$app->response->statusCode = 400;
            return $this->asJson('Comment not found');
        }
 
    }

    public function actionDeleteComment()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', $request->post('id'));

        $commentIds = $this->collectCommentIds($id);

        if (!empty($commentIds)) {
            \common\models\Comments::deleteAll(['id' => $commentIds]);
            return $this->asJson($id);
        } else {
            Yii::$app->response->statusCode = 400;
            return $this->asJson('Comment not found');
        }
    }

    private function collectCommentIds($parentId)
    {
        $ids = [$parentId];
        $children = \common\models\Comments::find()
            ->select('id')
            ->where(['parentid' => $parentId])
            ->column();

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->collectCommentIds($childId));
        }

        return $ids;
    }  
public function actionAddNewStencil()
    {
           $request = Yii::$app->request;

           $xml_source = $request->post('xml');
           $lib_name = $request->post('libname') ;
           $userid = $request->post('userid');

           $max_stencils = 3;

            //replace all '_' to '+'
            $lib_name = str_replace('_', '+', $lib_name);
            $lib_name = str_replace('~', '&', $lib_name);
            
            //replace all '_' to '+'
            $xml_source = str_replace('_', '+', $xml_source);
            $xml_source = str_replace('~', '&', $xml_source);

            $userStencils = \common\models\NewStencils::find()
                    ->where(['userid' => $userid,'stencilActive' =>1])
                    ->all();

            $arrlength = count($userStencils);

            
            if ($arrlength >= $max_stencils)
            {
                Yii::$app->response->statusCode = 400;
                return 'You have reach the maximum number of user stencils';
            }

            $newStencil =  \common\models\NewStencils::findOne(['userid' => $userid, 'stencilName' => $lib_name]);
            if ($newStencil)
            {
                Yii::$app->response->statusCode = 400;
                return 'Stencil with this name already exist';
            }
            else
            {
                $newStencil = new \common\models\NewStencils();
                $newStencil->stencilName = $lib_name;
                $newStencil->stencilXML = $xml_source;
                $newStencil->userid = $userid;
                $newStencil->save(false);

                return $newStencil->ID;

            }

    }      

    public function actionGetNewStencils()
    {
           $request = Yii::$app->request;
           $userid = $request->post('userid');

           $NewStencils =  \common\models\NewStencils::find()
                                    ->where(['userid' => $userid, 'stencilActive' => 1])
                                    ->all();

        return $this->asJson($NewStencils);
    }
   
    public function actionSaveNewStencil()
    {
           $request = Yii::$app->request;

           $ID = $request->post('ID');
           $xml_source = $request->post('xml');
           $lib_name = $request->post('libname') ;
           $userid = $request->post('userid');

            //replace all '_' to '+'
            $xml_source = str_replace('_', '+', $xml_source);
            $xml_source = str_replace('~', '&', $xml_source);

            //replace all '_' to '+'
            $lib_name = str_replace('_', '+', $lib_name);
            $lib_name = str_replace('~', '&', $lib_name);

           $newStencil =  \common\models\NewStencils::findOne(['ID' => $ID]);
            if ($newStencil)
            {
                $newStencil->stencilXML = $xml_source;
                $newStencil->stencilName = $lib_name;
                $newStencil->save(false);

                $s3 = Yii::$app->get('s3');
                // $result = $s3->put(templates/ . $fileName , $dataXML);
                $result = $s3->put($this->getS3RootFolder() . 'new_stencils/' . $ID . '.xml' , $xml_source);

            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return 'Not found Stencil with this ID';
            }

    }
    
    function actionGetUserList()
{
    $request = Yii::$app->request;
    $userid = $request->get('userid', $request->post('userid'));
    $user_info=\common\models\User::findOne(['id' => $userid]);
    if ($user_info) 
    {
        $client_info=\common\models\Client::findOne(['id' => $user_info->clientid]);
        if ($client_info)
        {
             $items = (new Query())
             ->select(' id, userfullname')
            ->from('user ')
            ->where(['clientid' => $user_info->clientid])
            ->andWhere(['!=', 'status', 0])
             ->orderBy(['userfullname' => SORT_ASC])
             ->all();
        } 
        else
        {
              $items = (new Query())
             ->select(' id, userfullname')
            ->from('user ')
            ->where(['id' => $userid])
             ->orderBy(['userfullname'=> SORT_ASC ])
             ->all();
        }
        return $this->asJson($items);
    }
    else
    {
         return $this->asJson([]);
    }
    }



    private function getClientMomentusApiURL($clientid)
    {
        $client_info=\common\models\Client::findOne(['id' => $clientid]);
        if ($client_info) {

            $retValue = Yii::$app->params['api_url'];
            if (strlen($client_info->MomentusAPIUrl) > 1 )
             {
                  $retValue =  $client_info->MomentusAPIUrl ;
             }
            return $retValue;
        }

         else
        {
            //not found client
            return "";
        }

    }
    private function getClientToken($clientid)
    {
        $client_info=\common\models\Client::findOne(['id' => $clientid]);
        if ($client_info) {
            //found client with this ID
             $url = Yii::$app->params['api_urlAuth'] . "/token";

            //special test code for client The Empire Theatre
            // if ($client_info->id == 92)
            // {
            //      $url =  "https://auth-api.ap-venueops.com" . "/token";
                 
            // }
            if (strlen($client_info->MomentusAPIUrlAuth) > 1 )
             {
                  $url =  $client_info->MomentusAPIUrlAuth . "/token";
             } 

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = '{"clientId":"' . $client_info->MomentusAPIKey . '","clientSecret":"' . $client_info->MomentusSecretKey . '"}';


            $headers = array(
                "Content-Type: text/plain",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            $obj = json_decode($resp);
            //var_dump($resp);
            return ($obj->{'accessToken'});
        }
        else
        {
            //not found client
            return "";
        }

       
    }

public function actionTempEdUpload()
    {   
        $tmpName = $_FILES['file']['tmp_name'];
        $fileContent = file_get_contents($tmpName);
        
        $fileName = uniqid(rand(), true) . '.evdr';
        $filePath =  "site/tmp/".$fileName;

        file_put_contents($filePath, $fileContent);

        return $this->asJson([
            "success" => true,
            "fileName" => $fileName,
            "fileSize" => strlen($fileContent)
        ]);
    }

    public function actionGetTempEdFile()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName');
        $filePath =  "site/tmp/".$fileName;
        $fileContent = file_get_contents($filePath);

        @unlink($filePath);

        return $fileContent;
    }


function actionClearOldEvents()
{
$oldEvents =  \common\models\Event::find()
            ->select(['id', 'updated_at'])
            ->where(['<','updated_at',1735689601])
            ->andwhere(['>','id',93529])
            ->andwhere(['!=','xmlCode',""])
            ->orderBy(['id' => SORT_ASC])
            ->limit(200)
             ->all();


    if ($oldEvents)
    {
        $arrlength = count($oldEvents);
        $strSQL = "";

        for($x = 0; $x < $arrlength; $x++) 
        {
           $eventid = $oldEvents[$x]->id;

              $s3 = Yii::$app->get('s3');
               

              $filename_looking = $this->getS3RootFolder() . 'events/'. strval($eventid) . '.xml';
              $exist = $s3->exist($filename_looking);

                if ($exist) 
                {

                    if ($strSQL != "")
                    {
                        $strSQL  = $strSQL  . "," . $eventid;
                    }
                    else
                    {
                        $strSQL  = $strSQL   . $eventid;
                    }
                    

                // $evnt = \common\models\Event::find()
                //      ->where(['id' => $eventid])->one();
                //             if ($evnt)
                //             {

                               

                //               $evnt->xmlCode = "";
                //               $evnt->save(false);
                            
                              
                //             }


                 }
        }

        return $strSQL;   
    }
}
function actionMomentusCredentials()
    {
        
        $request = Yii::$app->request;
        $user_id = $request->post('userid');
        $client_id = $request->post('clientid');
        $client_secret = $request->post('clientsecret');

		 $user_info=\common\models\User::findOne(['id' => $user_id]);
            if ($user_info) {
                $clients = \common\models\Client::findOne(['id' => $user_info->clientid]);
                if ($clients) {
                     $clients->MomentusAPIKey = $client_id;
                     $clients->MomentusSecretKey = $client_secret;
                     $clients->save(false);

                     $token = $this::getClientToken($clients->id);

                     if ($token != '')
                     {
                        return $this->asJson('Momentus Credendials was updated succesfully' );
                     }
                     else
                     {
                        Yii::$app->response->statusCode = 400;
                         return $this->asJson('Client ID or Client Secret key not corrected' );  
                     }
                     
                }
                else
                {
                     
                }
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User not found' );   
            }

      
        
        return $this->asJson('OK' );  
    }

       public function actionGetApiToken()
    {
        $url = Yii::$app->params['api_urlAuth'] . "/token";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $data = '{"clientId":"' . Yii::$app->params['api_clientID'] . '","clientSecret":"' . Yii::$app->params['api_clientSecret'] . '"}';


        $headers = array(
            "Content-Type: text/plain",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($resp);
        //var_dump($resp);
        return ($obj->{'accessToken'});
    }
   public function actionMomentusCopy()
    {
        $request = Yii::$app->request;
        $event_id = $request->post('eventid');
		$userid = $request->post('userid');

        $userlogon  = \common\models\Userlogon::findOne(['userid' => $userid, 'xml' => $event_id]);

		if ($userlogon)
		{

		}

		else
		{
			$guidinfo = bin2hex(openssl_random_pseudo_bytes(16));
			$userlogon = new \common\models\Userlogon();
			$userlogon->userid = $userid;
			$userlogon->guid = strval($guidinfo);
			$userlogon->xml = $event_id;
			$userlogon->save(false);
		}

       return 'https://momentusstaging.eventdrawus.com/frontend/web/site/check-guid?guid=' . $userlogon->guid; 

    }

function actionGetMomentusState()
    {
        
        $request = Yii::$app->request;
        $user_id = $request->post('userid');
        $data_arr = [];
		 $user_info=\common\models\User::findOne(['id' => $user_id]);
            if ($user_info) {
                $clients = \common\models\Client::findOne(['id' => $user_info->clientid]);
                if ($clients) {

                    $data_arr[0]["AllowMomentus"]=$clients->AllowMomentus;
                    $data_arr[0]["MomentusAPIKey"]=$clients->MomentusAPIKey;
                    $data_arr[0]["MomentusSecretKey"]=$clients->MomentusSecretKey;

                    $token = $this::getClientToken($clients->id);

                     if ($token != '')
                     {
                        $data_arr[0]["MomentusTokenCorrect"] = 1;
                     }
                     else
                     {
                        $data_arr[0]["MomentusTokenCorrect"] = 0;
                     }

                    return   $this->asJson($data_arr[0]);
                }
                else
                {
                    Yii::$app->response->statusCode = 400;
                    return $this->asJson('Client not found' );   
                }
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User not found' );   
            }

      
 
    }
public function actionGetMomentusData()
    {
        $request = Yii::$app->request;
        $event_id = $request->post('eventid');
        $userid = $request->post('userid');

        $EventMomentus=\common\models\Eventmomentus::findOne(['eventid' => $event_id]);
        if ($EventMomentus)
		{
           //send request to Momentus API

            $user_client = $this->getUserClient($userid);

            // return $user_client;
            //$token = $this::actionGetApiToken();
            $token = $this::getClientToken($user_client);

            $FunctionID = $EventMomentus->momentus_function;


            $url = $this::getClientMomentusApiURL($user_client) . "functions/" . $FunctionID ;

            //special test code for client The Empire Theatre
            // if ($user_client == 92)
            // {
            //      $url =  "https://api.ap-venueops.com/v1/" . "functions/" . $FunctionID ;
                 
            // }

            $curl = curl_init($url);
            $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            $obj = json_decode($resp);
            return $this->asJson($obj);

		}

		else
		{
          Yii::$app->response->statusCode = 400;
          return $this->asJson('Saved Floorplan is not linked with Momentus Elite' );  
        }

        
    }
function actionUpdateMomentusLink()
    {
        
        $request = Yii::$app->request;
        $event_id = $request->post('eventid');
		// $userid = 12821;
        $userid = $request->post('userid');
        $momentus_event = $request->post('MomentusEvent');
        $momentus_function = $request->post('MomentusFunction');

        // $EventMomentus=\common\models\Eventmomentus::findOne(['eventid' => $event_id, 
        //                                                       'momentus_event'=> $momentus_event,
        //                                                       'momentus_function'=> $momentus_function]);
        $EventMomentus=\common\models\Eventmomentus::findOne(['eventid' => $event_id]);
        if ($EventMomentus)
		{

			$EventMomentus->momentus_event = $momentus_event;
			$EventMomentus->momentus_function = $momentus_function;
			$EventMomentus->save(false);
		}

		else
		{
            $EventMomentus = new \common\models\Eventmomentus();
			$EventMomentus->eventid = $event_id;
			$EventMomentus->momentus_event = $momentus_event;
			$EventMomentus->momentus_function = $momentus_function;
			$EventMomentus->save(false);
        }

        $userlogon  = \common\models\Userlogon::findOne(['userid' => $userid, 'xml' => $event_id]);

		if ($userlogon)
		{

		}

		else
		{
			$guidinfo = bin2hex(openssl_random_pseudo_bytes(16));
			$userlogon = new \common\models\Userlogon();
			$userlogon->userid = $userid;
			$userlogon->guid = strval($guidinfo);
			$userlogon->xml = $event_id;
			$userlogon->save(false);

            
            
		}

    
         $user_client = $this->getUserClient($userid);

            // return $user_client;
            //$token = $this::actionGetApiToken();
            $token = $this::getClientToken($user_client);
        
    

            // $url = Yii::$app->params['api_url'] . "functions/add-document-link";
            $url = $this::getClientMomentusApiURL($user_client) . "functions/add-document-link" . $FunctionID ;

            //special test code for client The Empire Theatre
            // if ($user_client == 92)
            // {
            //      $url =  "https://api.ap-venueops.com/v1/" . "functions/add-document-link" ;
                 
            // }


            $curl = curl_init($url);
            $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            //  $payload = json_encode( array( "eventId"=> $event_id ) );

        $docName = "EventDraw floorplan edit URL";
        $EventInfo = \common\models\Event::findByIDAll($event_id);

        if ($EventInfo)
        {
            $docName =$EventInfo->eventName;  
        }
        $data = '{"name":"' .  $docName .  '", "functionId":"' . $momentus_function .'", "fileUrl":"' . 'https://login.eventdraw.com.au/frontend/web/site/check-guid?guid=' . $userlogon->guid . '"}';
            
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;

        
        return 'Your floor plan has been saved and the link stored in Momentus Elite for ' . $momentus_function;
    }

   public function actionLoadFileGuid()
{
        //get guid
        $request = Yii::$app->request;
        $guid = $request->post('guid');

        $guidInfo = \common\models\Userlogon::findByGUID($guid);

        if ($guidInfo) {
          
            $filename = $guidInfo->ip_addr;
            $filedata = $guidInfo->xml;

            //remove used GUID record
             $guidInfo->delete();

            return json_encode(array ('state'=>'ok','filename'=>$filename,'xml'=>$filedata));

       
        } else
        {
            return json_encode(array ('state'=>'error','message'=>'Incorrect GUID'));
        }



}



   public function getS3RootFolder()
   {
    //    return 'staging_data/';
    return 'momentus_data/';
   }

 public function actionGetGuidDesktop()
    {
        $request = Yii::$app->request;
        $username = $request->post('login');
        $userpsw =  $request->post('psw');

        $fileName = $request->post('filename');
        $fileData = $request->post('filedata');

        $fileData = str_replace('|', '+', $fileData);
        $fileData = str_replace('~', '&', $fileData);
         

        $guidinfo ="error";


        //search user by username
        $userID = \common\models\User::findByUsername($username);
        if ($userID)
        {
            if ($userID->validatePassword($userpsw)) {
                $guidinfo = bin2hex(openssl_random_pseudo_bytes(16));
                //add record to userlogon table
                $userlogon = new \common\models\Userlogon();
                $userlogon->userid = $userID->id;
                $userlogon->ip_addr = substr($fileName,0, 49);
                //$userlogon->xml = base64_decode($fileData);
                $userlogon->xml = $fileData;
                $userlogon->guid = strval($guidinfo);
                $userlogon->save(false);
            }
            else {
               $guidinfo = "incorrect password"; 
            }
        }

        return $this->asJson($guidinfo );

    }


public function actionCheckGuidDesktop()
    {
        //get guid
        $guid = Yii::$app->getRequest()->getQueryParam('guid');

        //check user name with this guid
        $guidInfo = \common\models\Userlogon::findByGUID($guid);

        if ($guidInfo) {
            //remove used GUID record


            //authenticate user with this guid
            $userID = \common\models\User::findIdentity($guidInfo->userid);
            //$guidInfo->delete();

            if ($userID) {
                Yii::$app->user->login($userID);

                //return $this->render('evdr/index');
                //open eventdraw site
                $this->layout = 'empty';
                Yii::$app->response->redirect('eventdraw?fileGuid=' . $guid);

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



   public function actionSvg2dwg()
{
    $request = Yii::$app->request;

    $imageSVG = urldecode($request->post('imageSVG'));

    $base_url = "http://94.46.135.126:3200/";

    $file_name_with_full_path =  "site/tmp/".uniqid(rand(), true) . '.svg';

    if (file_put_contents($file_name_with_full_path, $imageSVG)) 
    { 
        //  return "File downloaded successfully " . $file_name_with_full_path; 


         // svg to dwg
        $dwg_url = $base_url . "svg2dwg_ajax";
        $cFile = curl_file_create($file_name_with_full_path);
        $post = array('file'=> $cFile);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dwg_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec ($curl);
        curl_close ($curl);

        $data = json_decode($result, true);
        // return $base_url . $data['fileName'];
        $url =  $base_url . $data['fileName'];
        $file_name = "site/tmp/".uniqid(rand(), true) . '.dwg';  

        if (file_put_contents($file_name, file_get_contents($url))) 
        { 
            // return "File downloaded successfully"; 

            $dwgContents = file_get_contents($file_name);

            // delete temp files
            if (!unlink($file_name)) {
            }
            if (!unlink($file_name_with_full_path)) {
            }

            return base64_encode ($dwgContents);

        } 
        else
        { 
            // return "File downloading failed."; 
        } 
    } 

   


}

   public function actionSvg2dxf()
{
    $request = Yii::$app->request;

    $imageSVG = urldecode($request->post('imageSVG'));

    $base_url = "http://94.46.135.126:3200/";

    $file_name_with_full_path =  "site/tmp/".uniqid(rand(), true) . '.svg';

    if (file_put_contents($file_name_with_full_path, $imageSVG)) 
    { 
        
         // svg to dxf
        $dxf_url = $base_url . "svg2dxf_ajax";
        $cFile = curl_file_create($file_name_with_full_path);
        $post = array('file'=> $cFile);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dxf_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($result, true);

       
        // return $base_url . $data['fileName'];
        $url =  $base_url . $data['fileName'];
        $file_name = "site/tmp/".uniqid(rand(), true) . '.dxf';  

        if (file_put_contents($file_name, file_get_contents($url))) 
        { 
            // return "File downloaded successfully"; 

            $dxfContents = file_get_contents($file_name);

            // delete temp files
            if (!unlink($file_name)) {
            }
            if (!unlink($file_name_with_full_path)) {
            }

            return base64_encode ($dxfContents);

        } 
        else
        { 
            // return "File downloading failed."; 
        } 
    } 

   


}


public function actionGetDwg3()
    {
             $request = Yii::$app->request;

        $imageXML = $request->post('imageXML');

       
        $format = 'pdf';
        $bg = 'none';
        $base64 = '0';
        $crop = $request->post('crop');
        $crop = 0;
        $filename = uniqid(rand(), true) . '.pdf';

        // $url = "https://eventdraw.site:3000";
        $url = "https://eventdrawpdf.com:3000";

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

        $fileNameLocalPDF = "site/tmp/" . uniqid(rand(), true) . '.pdf';

        // return base64_encode ($resp);
        file_put_contents($fileNameLocalPDF, $resp);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetCreator('EventDraw');
        $pagecount = $mpdf->setSourceFile($fileNameLocalPDF);
        $tplId = $mpdf->importPage(1);
        $arrSize = $mpdf->getTemplateSize($tplId);

        
        $mpdf2 = new \Mpdf\Mpdf();
        $mpdf2->AddPageByArray(array(
            'mode' => 'utf-8',
            'orientation' =>  'P',
            'sheet-size' => array($arrSize['width'], $arrSize['height'])
        ));
        
        $pagecount = $mpdf2->setSourceFile($fileNameLocalPDF);
        $tplId2 = $mpdf2->importPage(1);
        $mpdf2->useTemplate($tplId2);

        
        
        $fileNameLocalPDF2 = "site/tmp/" . uniqid(rand(), true) . '.pdf';


        // Do not add page until doc template set, as it is inserted at the start of each page
     



        
        
        $mpdf2->Output($fileNameLocalPDF2, 'F');



        
        //$pdfResult = file_get_contents($fileNameLocalPDF2);

        // delete temp files
        // if (!unlink($fileNameLocalPDF)) {
        // }
        // if (!unlink($fileNameLocalPDF2)) {
        // }

        // return base64_encode ($pdfResult);



    $base_url = "http://zic.yir.mybluehost.me:3200/";
    $dwg_url = $base_url . "svg2dwg_ajax";


    if ($fileNameLocalPDF2 != '') 
    { 
        //  return "File downloaded successfully " . $file_name_with_full_path; 


         // svg to dwg
        
        $cFile = curl_file_create($fileNameLocalPDF2);
        $post = array('file'=> $cFile);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dwg_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec ($curl);
        curl_close ($curl);

        $data = json_decode($result, true);
        // return $base_url . $data['fileName'];
        $url =  $base_url . $data['fileName'];
        $file_name = "site/tmp/".uniqid(rand(), true) . '.dwg';  

        if (file_put_contents($file_name, file_get_contents($url))) 
        { 
            // return "File downloaded successfully"; 

            $dwgContents = file_get_contents($file_name);

            // delete temp files
            if (!unlink($file_name)) {
            }
            if (!unlink($fileNameLocalPDF2)) {
            }
            

            return base64_encode ($dwgContents);

        } 
        else
        { 
            // return "File downloading failed."; 
        } 
    } 



    }


public function actionGetDxf3()
    {
             $request = Yii::$app->request;

        $imageXML = $request->post('imageXML');

       
        $format = 'pdf';
        $bg = 'none';
        $base64 = '0';
        $crop = $request->post('crop');
        $crop = 0;
        $filename = uniqid(rand(), true) . '.pdf';

        // $url = "https://eventdraw.site:3000";
        $url = "https://eventdrawpdf.com:3000";

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

        $fileNameLocalPDF = "site/tmp/" . uniqid(rand(), true) . '.pdf';

        // return base64_encode ($resp);
        file_put_contents($fileNameLocalPDF, $resp);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetCreator('EventDraw');
        $pagecount = $mpdf->setSourceFile($fileNameLocalPDF);
        $tplId = $mpdf->importPage(1);
        $arrSize = $mpdf->getTemplateSize($tplId);

        
        $mpdf2 = new \Mpdf\Mpdf();
        $mpdf2->AddPageByArray(array(
            'mode' => 'utf-8',
            'orientation' =>  'P',
            'sheet-size' => array($arrSize['width'], $arrSize['height'])
        ));
        
        $pagecount = $mpdf2->setSourceFile($fileNameLocalPDF);
        $tplId2 = $mpdf2->importPage(1);
        $mpdf2->useTemplate($tplId2);

        
        
        $fileNameLocalPDF2 = "site/tmp/" . uniqid(rand(), true) . '.pdf';


        // Do not add page until doc template set, as it is inserted at the start of each page
     



        
        
        $mpdf2->Output($fileNameLocalPDF2, 'F');



        
        //$pdfResult = file_get_contents($fileNameLocalPDF2);

        // delete temp files
        // if (!unlink($fileNameLocalPDF)) {
        // }
        // if (!unlink($fileNameLocalPDF2)) {
        // }

        // return base64_encode ($pdfResult);



    $base_url = "http://zic.yir.mybluehost.me:3200/";
    $dwg_url = $base_url . "svg2dxf_ajax";


    if ($fileNameLocalPDF2 != '') 
    { 
        //  return "File downloaded successfully " . $file_name_with_full_path; 


         // svg to dwg
        
        $cFile = curl_file_create($fileNameLocalPDF2);
        $post = array('file'=> $cFile);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dwg_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec ($curl);
        curl_close ($curl);

        $data = json_decode($result, true);
        // return $base_url . $data['fileName'];
        $url =  $base_url . $data['fileName'];
        $file_name = "site/tmp/".uniqid(rand(), true) . '.dwg';  

        if (file_put_contents($file_name, file_get_contents($url))) 
        { 
            // return "File downloaded successfully"; 

            $dwgContents = file_get_contents($file_name);

            // delete temp files
            if (!unlink($file_name)) {
            }
            if (!unlink($fileNameLocalPDF2)) {
            }
            

            return base64_encode ($dwgContents);

        } 
        else
        { 
            // return "File downloading failed."; 
        } 
    } 



    }



public function actionGetEventExtraLayouts()
{
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');
        $userid = $request->post('userid');

        if (substr($eventid, 0,  3) == 'evt')
            {
                $eventid  = intval(substr($eventid, 3));
            }


        // $eventid = 1;
        // $userid = 69;

        $data_arr = [];
        $child_items = [];
        $evnt = \common\models\Event::findByIDAll($eventid);
                 if ($evnt) 
                 {
                    $data_arr[0]["plan_id"]='evt' . strval($evnt->id);
                    $data_arr[0]["plan_name"]=$evnt->eventName;
                    $data_arr[0]["plan_image"]='frontend/web/site/media_srv/event_images/' . strval($evnt->id) . '.png?t=' . time() ;
                    $data_arr[0]["plan_parent"]=0;

                    $child = \common\models\ExtraLayouts::find()
                    ->where(['userid' => $userid, 'parentid' => $evnt->id ])
                    ->orderBy(['id' => SORT_DESC])
                    ->all();

                     if ($child)
                     {
                         $arrlength = count($child);
                          for($x = 0; $x < $arrlength; $x++) 
                         {
                            $child_item_id = $child[$x]->eventid;
                            $child_evnt = \common\models\Event::findByIDAll($child_item_id);
                            if ($child_evnt)
                            {
                                $child_items[$x]["plan_id"]='evt' . strval($child_evnt->id);
                                $child_items[$x]["plan_name"]=$child_evnt->eventName;
                                $child_items[$x]["plan_image"]='frontend/web/site/media_srv/event_images/' . strval($child_evnt->id) . '.png?t=' . time() ;
                                $child_items[$x]["plan_parent"]='evt' . strval($evnt->id);
                            }
                          }
                         }
                $data_arr[0]["child_layouts"]=$child_items;        
                return   $this->asJson($data_arr[0]);   
                 }
           return null;      
        
}
public function actionSetEventUpdateStatus()
    {
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');
        $userid = $request->post('userid');
        $status = $request->post('status');

        $item =  UserOpenEvents::find()
            ->where(['userid' => $userid, 'eventid' => $eventid , 'status' => 0])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
             ->all();
        if ($item )
        {
            $item[0]->status =  $status;
            $item[0]->edittime =  new Expression('NOW()');
            $item[0]->save(false);
        }
        

    }
public function actionGetEventEditStatus()
    {
        $request = Yii::$app->request;
        $eventid = $request->post('eventid');
        $userid = $request->post('userid');

        $ret = '';
        $items =  UserOpenEvents::find()
            ->where(['eventid' => $eventid , 'status' => 0])
             ->andwhere(['>','edittime',new Expression('DATE_ADD(NOW(), INTERVAL -60 SECOND)')])
            ->orderBy(['userid' => SORT_ASC])
             ->all();
        
        if ($items)
        {
            $ret = '';
            $lastuserid = -1;
            $second = false;
            $arrlength = count($items);
            for($x = 0; $x < $arrlength; $x++) {
                if ($items[$x]->userid != $lastuserid)
                {
                    $lastuserid = $items[$x]->userid;

                    $user_info=\common\models\User::findOne(['id' => $lastuserid]);
                    if ($user_info) {
                         if ($second) 
                         {
                            $ret = $ret . ', ';   
                         }
                         $ret = $ret . '<b>' .$user_info->userfullname  .'</b>';
                         $second = true;
                    }

                }
            }

        }
             
        return $ret;
        

    }    
public function actionGetAllClients()
    {

        $request = Yii::$app->request;
        

        //$item = Event::find()->all();
         $clients = \common\models\Client::find()
            ->select(['id', 'clientName'])
            ->orderBy(['clientName' => SORT_ASC])->all();




        return $this->asJson($clients); 
    }



// public function actionGetTemplateMatterport()
//     {
//         $request = Yii::$app->request;
//         $template_id = $request->post('templateid');
//         $token = $request->post('token');
        
//         $template=\common\models\Template::findOne(['id' => $template_id]);
//         if ($template)
//         {
//             $Matterport=\common\models\Matterport::findOne(['id' => $template->matterportid]);

//             if ($Matterport)
//             {

//            return 'https://matterport.eventdraw.com.au/?token=' . $token . '&building=' .$Matterport->mat;
//             }
//             else 
//             {
//                 return '';
//             }
//         }

//         else 
//             {
//                 return '';
//             }

//     }

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

           return 'https://matterport.eventdraw.com.au/?token=' . $token . '&building=' .$Matterport->mat . '&id=' . $Matterport->id;
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
  
        // $Matterport_assign=\common\models\MatterportAssign::findOne(['layout_id' =>$event_id ]);
       
       $Matterport_assign=\common\models\MatterportAssign::find()
         ->where(['layout_id' =>$event_id ])
            ->orderBy(['matterport_id' => SORT_DESC])
            ->one();
                
        if ($Matterport_assign ){

            // $Matterport=\common\models\Matterport::findOne(['id' => $Matterport_assign->matterport_id]);

            // if ($Matterport)
            // {
            // return 'https://matterport.eventdraw.com.au/?token=' . $ThreedEvent->token . '&building=' .$Matterport->mat;
            // }
            // else 
            // {
            //     return '';
            // }
            return $Matterport_assign->matterport_id;
        }
        else{
            return '';
        }

        
    }

public function actionSetMatterportLink()
    {
        $request = Yii::$app->request;
        $event_id = $request->post('eventid');
        $template_id = $request->post('templateid');
 
        // $Template=\common\models\Template::findOne(['id' =>$template_id ]);
        // if ($Template)        
        // {
        //    $matterport_id = $Template->matterportid;
        //    if ($matterport_id) 
        //    {
               $Matterport_assign=\common\models\MatterportAssign::findOne(['layout_id' =>$event_id ]);
                if ($Matterport_assign )
                {
                   $Matterport_assign->matterport_id = $template_id;
                }
                else
                {
                    $Matterport_assign=new \common\models\MatterportAssign;
                    $Matterport_assign->layout_id = $event_id;
                    $Matterport_assign->matterport_id = $template_id;
                }
                $Matterport_assign->save(false);
        //    }
            
        // }

        Yii::$app->response->statusCode = 200;
        return $this->asJson('Updated matterport link' . $Matterport_assign->id );
        
                
        
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

        //  $Events =  \common\models\Event::findAll([
        //   'userid' => $userid, 'eventActive' => 1,
        //   ]);

         $Events = \common\models\Event::find()
            ->select(['id' ])
            ->where(['userid' => $userid,'eventActive' => 1])->all();

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
            // $result = $s3->put(templates/ . $fileName , $dataXML);
            $result = $s3->put($this->getS3RootFolder() . 'stencils/' . $fileName , $dataXML);
       
    }
    
public function saveEvent_S3($eventID)
    {
        $evnt = \common\models\Event::findByIDAll($eventID);
        if ($evnt) 
        {
           if($evnt->xmlCode != '')
            {
              $s3 = Yii::$app->get('s3');
              $result = $s3->put($this->getS3RootFolder() . 'events/' . strval($evnt->id) . '.xml', $evnt->xmlCode);
              
              $evnt->xmlCode = '';
              $evnt->save(false);

            }

        }
        return 0;
    }
    public function loadEvent_S3($eventID)
    {
        $strXML = "";
        $fileNameLocal = "site/tmp/".uniqid(rand(), true) . '.xml';
        
        $s3 = Yii::$app->get('s3');
      
        $filename_looking = $this->getS3RootFolder() . 'events/'. strval($eventID) . '.xml';

        

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

            if($tmpl->xmlCode != '')
            {
              $s3 = Yii::$app->get('s3');
              $result = $s3->put($this->getS3RootFolder() . 'templates/' . strval($tmpl->id) . '.xml', $tmpl->xmlCode);

              $tmpl->xmlCode = '';
              $tmpl->save(false);

            }

            }
        return 0;
    }

public function loadTemplate_S3($tempID)
    {
        $strXML = "";
        $fileNameLocal = "site/tmp/".uniqid(rand(), true) . '.xml';
        
        $s3 = Yii::$app->get('s3');
        
        $filename_looking = $this->getS3RootFolder() . 'templates/'. strval($tempID) . '.xml';
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
        $crop = $request->post('crop');
        $crop = 0;
        $filename = uniqid(rand(), true) . '.pdf';

        // $url = "https://eventdraw.site:3000";
        
        $url = "https://eventdrawpdf.com:3000";

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
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($curl, CURLOPT_TIMEOUT, 600);

        $resp = curl_exec($curl);
        curl_close($curl);

        $fileNameLocalPDF = "site/tmp/" . uniqid(rand(), true) . '.pdf';

        // return base64_encode ($resp);
        file_put_contents($fileNameLocalPDF, $resp);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetCreator('EventDraw');
        $pagecount = $mpdf->setSourceFile($fileNameLocalPDF);
        $tplId = $mpdf->importPage(1);
        $arrSize = $mpdf->getTemplateSize($tplId);

        
        $mpdf2 = new \Mpdf\Mpdf();
        $mpdf2->AddPageByArray(array(
            'mode' => 'utf-8',
            'orientation' =>  'P',
            'sheet-size' => array($arrSize['width'], $arrSize['height'])
        ));
        
        $pagecount = $mpdf2->setSourceFile($fileNameLocalPDF);
        $tplId2 = $mpdf2->importPage(1);
        $mpdf2->useTemplate($tplId2);

        
        
        $fileNameLocalPDF2 = "site/tmp/" . uniqid(rand(), true) . '.pdf';


        // Do not add page until doc template set, as it is inserted at the start of each page
     



        
        
        $mpdf2->Output($fileNameLocalPDF2, 'F');



        
        $pdfResult = file_get_contents($fileNameLocalPDF2);

        // delete temp files
         if (!unlink($fileNameLocalPDF)) {
         }
         if (!unlink($fileNameLocalPDF2)) {
         }

        return base64_encode ($pdfResult);

    }



public function actionGetPdf3()
    {
             $request = Yii::$app->request;

        $imageXML = $request->post('imageXML');

       
        $format = 'pdf';
        $bg = 'none';
        $base64 = '0';
        $crop = $request->post('crop');
        $crop = 0;
        $filename = uniqid(rand(), true) . '.pdf';

        $url = "https://eventdraw.site:3000";
        // $url = "https://eventdrawpdf.com:3000";

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

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($curl, CURLOPT_TIMEOUT, 600);

        $resp = curl_exec($curl);
        curl_close($curl);

        $fileNameLocalPDF = "site/tmp/" . uniqid(rand(), true) . '.pdf';

        // return base64_encode ($resp);
        file_put_contents($fileNameLocalPDF, $resp);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetCreator('EventDraw');
        $pagecount = $mpdf->setSourceFile($fileNameLocalPDF);
        $tplId = $mpdf->importPage(1);
        $arrSize = $mpdf->getTemplateSize($tplId);

        
        $mpdf2 = new \Mpdf\Mpdf();
        $mpdf2->AddPageByArray(array(
            'mode' => 'utf-8',
            'orientation' =>  'P',
            'sheet-size' => array($arrSize['width'], $arrSize['height'])
        ));
        
        $pagecount = $mpdf2->setSourceFile($fileNameLocalPDF);
        $tplId2 = $mpdf2->importPage(1);
        $mpdf2->useTemplate($tplId2);

        
        
        $fileNameLocalPDF2 = "site/tmp/" . uniqid(rand(), true) . '.pdf';


        // Do not add page until doc template set, as it is inserted at the start of each page
     



        
        
        $mpdf2->Output($fileNameLocalPDF2, 'F');



        
        $pdfResult = file_get_contents($fileNameLocalPDF2);

        // delete temp files
         if (!unlink($fileNameLocalPDF)) {
         }
         if (!unlink($fileNameLocalPDF2)) {
         }

        return base64_encode ($pdfResult);

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
                // $templateXML = str_replace('_', '+', $templateXML);


                $imageXML = $request->post('image');
                //replace all '_' to '+'
                // $imageXML = str_replace('_', '+', $imageXML);

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
        $userid = $request->post('userid');

       if (strlen($eventid) > 20)
        {
            $item =  Event::findOne(['event_uuid' => $eventid]);
        }
        else
        {
            $item =  Event::findOne(['id' => $eventid]);
        }

        //add record to user_open_events table
        if ($item)
        {
            $new_user_open_event =  new UserOpenEvents();
            $new_user_open_event->userid = $userid ;
            $new_user_open_event->eventid = $item->id;

            $new_user_open_event->save(false);
        }

        //19 Jan 2025
        //if event was cleared load it from S3
        if ($item->xmlCode == "")
        {
          $item->xmlCode =   $this::loadEvent_S3($item ->id);
        
        }

        // Return the Momentus EventSpaceDiagramID and OrgCode stored on the event
        // record so the frontend can call get-event-service-order without needing
        // the original Momentus deep-link URL parameters.
        $responseData = $item->toArray();
        $responseData['momentusEventSpaceDiagramId'] = (int) ($item->momentus_space_diagram_id ?? 0);
        $responseData['momentusOrgCode'] = (string) ($item->momentus_org_code ?? '');

        return $this->asJson($responseData);

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

        $folderid = $request->post('folderid');

        if (substr($folderid, 0,  3) == 'evt')
            {
                $folderid  = intval(substr($folderid, 3));
            }

        $eventinfo = str_replace('|', '+', $eventinfo);
        $eventinfo = str_replace('~', '&', $eventinfo);

        if (1==1) {
            $templateXML = $request->post('diagram');
            //replace all '_' to '+'
            // $templateXML = str_replace('_', '+', $templateXML);


            $imageXML = $request->post('image');
            //replace all '_' to '+'
            // $imageXML = str_replace('_', '+', $imageXML);

           
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

            // Persist the Momentus EventSpaceDiagramID and OrgCode so we can
            // resolve the service order when this layout is reopened later.
            $momentusSpaceDiagramId = (int) $request->post('momentus_space_diagram_id');
            $momentusOrgCode = trim((string) $request->post('momentus_org_code', ''));
            if ($momentusSpaceDiagramId > 0) {
                $evnt->momentus_space_diagram_id = $momentusSpaceDiagramId;
            }
            if ($momentusOrgCode !== '') {
                $evnt->momentus_org_code = $momentusOrgCode;
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

            // update folder id for this event
            //get client id from current user
        $clientID = 0;
        $userFoundedID = \common\models\User::findIdentity($userid) ;

        if ($userFoundedID)
        {
            if ($userFoundedID->clientid) {
                $clientID = $userFoundedID->clientid;
            }
        }


        if ($clientID != 0)
        {
            //check is this event exist in event folders table
            $EventFolder =  \common\models\ClientEventFolders::findOne(['eventid' => $evnt->id, 'clientid' => $clientID]);

                if ($EventFolder)
                {
                     $EventFolder->parentid = $folderid;
                     $EventFolder->save(false);
                    // return $this->asJson('OK');
                }
                else
                {
                     $EventFolder = new \common\models\ClientEventFolders();
                     $EventFolder->clientid = $clientID;
                     $EventFolder->eventid = $evnt->id;
                     $EventFolder->folderName = '';
                     $EventFolder->parentid = $folderid;


            $EventFolder->save(false);
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

        public function actionGetUserEventsShort2()
    {
        $request = Yii::$app->request;
        $userid = $request->get('userid', $request->post('userid'));


        $evntNames = Event::getUserEvents2($userid, false);

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

            $creator_client = $this->getUserClient($guidInfo->userid);

            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                // return Yii::$app->getResponse()->redirect('/frontend/web/site/eventdraw');

                $logged_client =  $this->getUserClient(Yii::$app->user->identity->id);
                //check is this logged user the same client as user who create this floorplan
                if ($creator_client == $logged_client)
                {
                    Yii::$app->response->redirect('eventdraw?EventID=' . $guidInfo->xml) ;
                }
                else
                {
                     return $this->render('another_client', [
            'model' => $model,]);
            // Yii::$app->response->redirect('eventdraw?EventID=' . $guidInfo->xml . '&client1=' . $creator_client . '&client2=' . $logged_client) ;
                }
                
                // return $this->goBack(); 
            } else {
                $model->password = '';

                return $this->render('login', [
                    'model' => $model,
                ]);
            }

            //authenticate user with this guid
            // $userID = \common\models\User::findIdentity($guidInfo->userid);

            // if ($userID) {
            //     Yii::$app->user->login($userID);

            //     $this->layout = 'empty';
            //     Yii::$app->response->redirect('eventdraw?EventID=' . $guidInfo->xml);

            // }
            // else
            // {
            //    return $this->asJson('Incorrect User' );
            // }

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


        //05 Apr 2026
        //if event was cleared load it from S3
        if ($item->xmlCode == "")
        {
          $item->xmlCode =   $this::loadTemplate_S3($item ->id);
        
        }

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

            $localDir = $request->post('localDir');

           
            if ($localDir == '0' || $localDir == '1') {
                $userSett->localDir = $localDir;

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

    public function actionGetClientTemplates()
    {
         $clients = \common\models\Client::find()
            ->select(['id' ])
            ->orderBy(['id' => SORT_ASC])->all();

        $retr_arr = [];

        $arrlength = count($clients);
        for($x = 0; $x < $arrlength; $x++) {
            $client_id = $clients[$x]->id;

            $clientTemplates = \common\models\ClientTemplates::findAll([
                    'clientid' => $client_id
                ]);
            $arrlengthTemplates = count($clientTemplates);
            $templ_arr = [];

            for($y = 0; $y < $arrlengthTemplates; $y++) {
                array_push($templ_arr, $clientTemplates[$y]->templateid); 
            }

            $Templates = \common\models\Template::find()
            ->select(['id' ])
            ->where(['clientid' => $client_id,'templateActive' => 1])->all();
             $arrlength2 = count($Templates);
             for($z = 0; $z < $arrlength2; $z++) {
                 $tmpl_id = $Templates[$z]->id;
                 $foundInList = false;

                 $arrlength3 = count($templ_arr);
                 for($k = 0; $k < $arrlength3; $k++) {
                     if ($tmpl_id == $templ_arr[$k])
                     {
                        $foundInList = true; 
                         break;
                     }
                 }
                 if ($foundInList != true)
                 {
                      array_push($templ_arr, $tmpl_id); 
                 }
             }


            $retr_arr [$x]["clientId"]= $client_id;
            $retr_arr [$x]["templateIds"]= $templ_arr;

        }

        

        return $this->asJson($retr_arr);


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

public function actionRenameTemplateItem()
        {
            
            $request = Yii::$app->request;
            $templateid = $request->post('template_id');
            $new_name = $request->post('template_name');
            
             if (substr($templateid, 0,  3) == 'tpl')
            {
                $templateid  = intval(substr($templateid, 3));
            }

            $TemplatInfo =  \common\models\Template::findOne(['id' => $templateid]);

            if ($TemplatInfo)
            {
                $TemplatInfo->templateName  = $new_name;
                $TemplatInfo->save(false);
                return $this->asJson('OK');
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('Template not found');
            }
   
        }

public function actionRenameEventItem()
        {
            
            $request = Yii::$app->request;
            $eventid = $request->post('event_id');
            $new_name = $request->post('event_name');
            
           
            if (substr($eventid, 0,  3) == 'evt')
            {
                $eventid  = intval(substr($eventid, 3));
            }
            
            $EventInfo =  \common\models\Event::findOne(['id' => $eventid]);

            if ($EventInfo)
            {
                $EventInfo->eventName  = $new_name;
                $EventInfo->save(false);
                return $this->asJson('OK');
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('Event not found');
            }
   
        }

    public function actionDeleteEvent()
        {
            $request = Yii::$app->request;
            $eventid = intval($request->post('eventid'));

            $event=\common\models\Event::findOne(['id' => $eventid]);
            if ($event)
            {
                $event->eventActive = 0;
                $event->save(false);
                return $this->asJson('OK');
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('Event not found');
            }


        }

    public function actionDeleteTemplate()
        {
            $request = Yii::$app->request;
            $templateid = intval($request->post('templateid'));

            $template=\common\models\Template::findOne(['id' => $templateid]);
            if ($template)
            {
                $template->templateActive = 0;
                $template->save(false);
                return $this->asJson('OK');
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('Template not found');
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
                    $itemsExist = false;

                    $FolderItems =  \common\models\ClientTemplateFolders::find()
                                    ->where(['parentid' => $folderid])
                                    ->all();

                    $arrlength = count($FolderItems);
                    for($x = 0; $x < $arrlength; $x++) {
                        $template_id = $FolderItems[$x]->templateid;


                        if ($template_id <0)
                        {
                            //folder
                            $itemsExist = true;


                        }
                        else
                        {
                            // //check is this event is active or not
                            $Tempates =  \common\models\Template::findAll([
                            'id' => $template_id , 'templateActive' => 1,
                            ]);

                            if (count($Tempates) > 0) {
                                 $itemsExist = true;
                            }
                        }
                    }


                    if ($itemsExist)
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
            $folderid = $request->post('folderid');

              if (substr($folderid, 0,  3) == 'tpl')
            {
                $folderid  = intval(substr($folderid, 3));
            }
            if (substr($folderid, 0,  3) == 'fld')
            {
                $folderid  = intval(substr($folderid, 3));
            }


            $itemids = $request->post('itemids');
            $myArray = explode(',', $itemids);

            $user_id = Yii::$app->user->identity->id;
            
             if (substr($folderid, 0,  3) == 'fld')
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
                //find item for this client and id

                $arrlength = count($myArray);
                for($x = 0; $x < $arrlength; $x++) { 

                 $templateid =   $myArray[$x];
                 if (substr($templateid, 0,  3) == 'tpl')
                 {
                     $templateid  = substr($templateid, 3);
                 }
                 
                 if (substr($templateid, 0,  3) == 'fld')
                {
                    $templateid  = substr($templateid, 3);
                    $TemplateFolder =  \common\models\ClientTemplateFolders::findOne(['id' => intval($templateid), 'clientid' => $clientID]);

                     
                if ($TemplateFolder)
                {
                    $TemplateFolder->parentid = $folderid;
                    $TemplateFolder->save(false);

                }

                }

                else
                {
                 
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
                    $itemsExist = false;

                    $FolderItems =  \common\models\ClientEventFolders::find()
                                    ->where(['parentid' => $folderid])
                                    ->all();

                    $arrlength = count($FolderItems);
                    for($x = 0; $x < $arrlength; $x++) {
                        $event_id = $FolderItems[$x]->eventid;


                        if ($event_id <0)
                        {
                            //folder
                            $itemsExist = true;


                        }
                        else
                        {
                            // //check is this event is active or not
                            $Events =  \common\models\Event::findAll([
                            'id' => $event_id , 'eventActive' => 1,
                            ]);

                            if (count($Events) > 0) {
                                 $itemsExist = true;
                            }
                        }
                    }

                    if ($itemsExist)
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
            if (substr($folderid, 0,  3) == 'fld')
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

                 //check is folder move to another folder
                if (substr($eventid, 0,  3) == 'fld')
                {
                    $eventid  = substr($eventid, 3);
                    $EventFolder =  \common\models\ClientEventFolders::findOne(['id' => intval($eventid), 'clientid' => $clientID]);

                    // return $this->asJson( $EventFolder);
                if ($EventFolder)
                {
                    $EventFolder->parentid = $folderid;
                    $EventFolder->save(false);
                }

                }

                else
                {
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
                
            }
            else
            {
                Yii::$app->response->statusCode = 400;
                return $this->asJson('User is not assigned to Client');
            }
        }




//  public function actionSaveTemplateJson()
//     {
//         //Get data from post request
//         if (!Yii::$app->user->isGuest) {
//             $request = Yii::$app->request;

//             $templateid = $request->post('templateid');
//             $clientid = intval($request->post('clientid'));

//             if ($templateid) {
//                 $templateXML = $request->post('diagram');
//                 //replace all '_' to '+'
//                 $templateXML = str_replace('_', '+', $templateXML);
//                 $templateXML = str_replace('~', '&', $templateXML);

//                 $imageXML = $request->post('image');
//                 //replace all '_' to '+'
//                 $imageXML = str_replace('_', '+', $imageXML);
//                 $imageXML = str_replace('~', '&', $imageXML);

//                 //remove data:image/png;base64,
//                 $imageXML = str_replace('data:image/png;base64,', '', $imageXML);

//                 //if first symbol is ! - need create new template, else find by id
//                 if ($templateid[0] == '!')
//                 {
//                     $templ = new Template();
//                     $templ->templateName = substr($templateid,1);
//                     $templ->templateActive = true;
//                     $templ->templateDefault = false;
                    

//                 }
//                 else
//                 {
//                     $templ = Template::findByID($templateid);
//                     if (!$templ)
//                     {
//                         $templ = new Template();
//                         $templ->templateName = substr($templateid,1);
//                         $templ->templateActive = true;
//                         $templ->templateDefault = false;
//                     }
//                 }


//                 if ($clientid > 0)
//                 {
//                     $templ->clientid = $clientid;
              
                    

//                 }
//                 else
//                 {
//                     //  $templ->clientid = null;
//                 }
//                 $templ->xmlCode  = $templateXML;
//                 $templ->created_by =  Yii::$app->user->identity->id;


//                 $templ->save(false);

//                 if ($clientid > 0)
//                 {
//                      $clientTemplates = \common\models\ClientTemplates::find()
//                         ->where(['clientid' => $clientid])
//                         ->andwhere(['templateid' =>$templ->id])->all();
//                     if (count($clientTemplates) == 0)
//                     {
//                         $clientTemplates = new \common\models\ClientTemplates();
//                         $clientTemplates->clientid = $clientid;
//                         $clientTemplates->templateid =  $templ->id;
//                         $clientTemplates->save(false);
//                     }
//                 }

//                 //$templ->save(true);
//                 //save to S3    
//                 $this::saveTemplate_S3($templ->id);

//                 //save to tmpl_images
//                 $fileNameWithPath = "site/media_srv/tmpl_images/" . $templ->id . '.png';
//                 if(file_put_contents($fileNameWithPath, base64_decode($imageXML) )) {
//                 }

//                 return $this->render('genlink');
//             }
//             else
//             {
//                 //template id or name is empty
//                 return $this->render('generr');
//             }
//         }
//         else{
//             return $this->goHome();
//         }
//     }    



    private function sendMail($id, $template_name, $template_id, $old_template_name) {
        $user = new User();
        $userInfo = User::findOne(['id' => $id]);
       // var_dump( $userInfo->email);
        // var_dump( $userInfo->email);
        // echo '--->';
        $UserName = $userInfo->username;
        $UserEmail = $userInfo->email;
        $emailTemplate              =   new EmailTemplate();
        $getEmailTemplate           =   $emailTemplate::findOne(['template_type'=>'notify_user_email']);
        $contetn_b =  $getEmailTemplate['content'];
        $find = ['{{first_name}}', '{{template_name}}'];
        $replacement = [Yii::$app->user->identity->firstname, $template_name];

        $emailContent = str_replace($find, $replacement, $contetn_b);
        // Prepare the email content
        // $emailContent = 'hello {username} {templatename} {template_id} {old_template_name}';
       //$emailContent = "We're reaching out to inform you we have updated <strong>{templatename}</strong> to your account.<br/> The floor plan template is now available for immediate use.<br/><br/><br/>The EventDraw Support Team";

        // $placeholders = ['{username}', '{templatename}', '{template_id}', '{old_template_name}'];
        // $replacements = [$UserName, $template_name, $template_id, $old_template_name];
        // $emailContent = str_replace($placeholders, $replacements, $emailContent);
        // print_r($emailContent);
     EmailHelper::sendTemplateChangeMail( $UserEmail,$emailContent,$UserName);
    //   var_dump($res);
        // EmailHelper::sendTemplateChangeMail('raghawsinghrathore@gmail.com',$emailContent,$UserName);
echo 'send';
        // die;
    }


    public function ActionTemplateChangeEmail($client_id,$template_name,$template_id,$old_template_name='')
    {   
        $frontTemplate  =   new FrontTemplate();
        $userList = \common\models\User::find()
            ->where(['clientid' => $client_id])
            ->all();
       // var_dump($userList);
         if(!empty($userList)){
         // $this->sendMail(2664,$template_name,$template_id,$old_template_name);
           
            foreach($userList as $obj){
             $this->sendMail($obj->id,$template_name,$template_id,$old_template_name);            
         } //foreach
        }
      
    }//TemplateChangeEmail


    public function ActionTemplateChangeBulletin($client_id,$template_name,$template_id,$old_template_name)
    {   
        $userList = \common\models\User::find()->where(['clientid' => $client_id])->all();
        $userIds    =   array();
        if (!empty($userList)) { foreach ($userList as $obj) {
            array_push($userIds,$obj->id);
        } }
        
        $newTemplate = new FrontTemplate();



        $newTemplate->client_id = $client_id;
        $newTemplate->template_name = $template_name ? $template_name : 'N/A';
        $newTemplate->template_id = $template_id ? $template_id : 0;
        $newTemplate->created_at = date('Y-m-d H:i:s');
        $newTemplate->old_template_name = $old_template_name ? $old_template_name : 'N/A';
        if(!empty($userIds)){
            $commaSerpatedIds           =   implode(',',$userIds);
            $newTemplate->show_to_user  =   $commaSerpatedIds;
        }
        $findUserInTemplate     =   frontTemplate::findOne(['template_id' => $template_id,'client_id'=>$client_id]);
        if(empty($findUserInTemplate)){
            $newTemplate->save();
            if (!empty($userList)) { foreach ($userList as $obj) {
                $templateChangeView   =   new TemplateChangeView();
                $templateChangeView->is_seen        =   0;
                $templateChangeView->template_id    =   $template_id;
                $templateChangeView->client_id      =   $client_id;
                $templateChangeView->user_id        =   $obj->id;
                if ($templateChangeView->save()) {
                    // echo json_encode(array('status' => 200, 'message' => "New Record Created Successfully"));
                } 
            } }
        }else{
            $findUserInTemplate->client_id = $client_id;
            $findUserInTemplate->template_name = $template_name ? $template_name : 'N/A';
            $findUserInTemplate->template_id = $template_id ? $template_id : 0;
            $findUserInTemplate->created_at = date('Y-m-d H:i:s');
            $findUserInTemplate->old_template_name = $old_template_name ? $old_template_name : 'N/A';
            if(!empty($userIds)){
                $commaSerpatedIds           =   implode(',',$userIds);
                $findUserInTemplate->show_to_user  =   $commaSerpatedIds;
                $findUserInTemplate->save();

                $templateChangeView = new TemplateChangeView();
                $getTemplateChangeData = TemplateChangeView::findAll(['template_id' => $template_id]);

                if (!empty($getTemplateChangeData)) {
                    foreach ($getTemplateChangeData as $obj) {
                        $findUserInTemplate = TemplateChangeView::findOne(['template_id' => $obj->template_id, 'user_id' => $obj->user_id]);
                        if ($findUserInTemplate) {
                            $findUserInTemplate->is_seen = 0;
                            $update     =   $findUserInTemplate->save();
                            if($update){
                                // echo json_encode(array('status' => 200, 'message' => "Record Updated Successfully"));
                            }
                        }
                    }
                }
            }
        }

        // die;
        // if ($newTemplate->save()) {
        //     echo json_encode(array('status' => 200, 'message' => "New Record Created Successfully"));
        // } else {
        //     $errors = $newTemplate->getErrors();
        //     echo json_encode(array('status' => 401, 'message' => 'Something went wrong, try again or later', 'errors' => $errors));
        // }    


        // $userList = \common\models\User::find()
        //     ->where(['clientid' => $client_id])
        //     ->all();

        // if (!empty($userList)) {
        //     foreach ($userList as $obj) {
        //         $newTemplate = new FrontTemplate();
        //         $newTemplate->client_id = $obj->id;
        //         $newTemplate->template_name = $template_name ? $template_name : 'N/A';
        //         $newTemplate->template_id = $template_id ? $template_id : 0;
        //         $newTemplate->created_at = date('Y-m-d H:i:s');
        //         $newTemplate->old_template_name = $old_template_name ? $old_template_name : 'N/A';
        //         $findUserInTemplate     =   frontTemplate::findOne(['template_id' => $template_id,'client_id'=>$obj->id]);
        //         if(empty($findUserInTemplate)){
        //             if ($newTemplate->save()) {
        //                 echo json_encode(array('status' => 200, 'message' => "New Record Created Successfully"));
        //             } else {
        //                 $errors = $newTemplate->getErrors();
        //                 echo json_encode(array('status' => 401, 'message' => 'Something went wrong, try again or later', 'errors' => $errors));
        //             }    
        //         }else{
        //             echo json_encode(array('status'=>'401','message'=>'Record matched in DB'));
        //         }
                
        //     }
        // } else {
        //     echo json_encode(array('status' => 404, 'message' => 'No users found'));
        // }

    }

public function actionSaveTemplateJson()
    {
        //Get data from post request
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;

            $templateid = $request->post('templateid');
            $clientid = intval($request->post('clientid'));

            
            if ($clientid == -1)
            {
                //if user is company admin - set automatically company
                if (Yii::$app->user->identity->company_admin)
                {
                    $clientid =Yii::$app->user->identity->clientid;
                }
            
            }

            if ($templateid) {
                $templateXML = $request->post('diagram');
                //replace all '_' to '+'
                // $templateXML = str_replace('_', '+', $templateXML);
                // $templateXML = str_replace('~', '&', $templateXML);

                $imageXML = $request->post('image');
                //replace all '_' to '+'
                // $imageXML = str_replace('_', '+', $imageXML);
                // $imageXML = str_replace('~', '&', $imageXML);

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


                if ($clientid > 0)
                {
                    $templ->clientid = $clientid;                   

                }
                else
                {
                    //  $templ->clientid = null;
                }
                $templ->xmlCode  = $templateXML;
                $templ->created_by =  Yii::$app->user->identity->id;


                $templ->save(false);

                $templateid = $templ->id;
                $template_name          =   $templ->templateName;
                $notify_user_bulletin   =   $request->post('notify_user_bulletin');
                $notify_user_email      =   $request->post('notify_user_email');                
                $old_template_name      =   $request->post('old_template_name');            

                if($notify_user_bulletin == 1){
                  $this->ActionTemplateChangeBulletin($clientid,$template_name,$templateid,$old_template_name);
                }
                if($notify_user_email == 1){
                 $this->ActionTemplateChangeEmail($clientid,$template_name,$templateid,$old_template_name);
                //var_dump($test);
                }


                if ($clientid > 0)
                {
                     $clientTemplates = \common\models\ClientTemplates::find()
                        ->where(['clientid' => $clientid])
                        ->andwhere(['templateid' =>$templ->id])->all();
                    if (count($clientTemplates) == 0)
                    {
                        $clientTemplates = new \common\models\ClientTemplates();
                        $clientTemplates->clientid = $clientid;
                        $clientTemplates->templateid =  $templ->id;
                        $clientTemplates->save(false);
                    }
                   //  if($notify_user_email == 1){
                     \common\models\Client::updateAll(
                            ['last_bulletin_email' => time()],
                            ['id' => $clientid]
                        );
                 // }
                }

                //$templ->save(true);
                //save to S3    
                $this::saveTemplate_S3($templ->id);

                // save to tmpl_images
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
        $authUrl = $this->genAuthUrl();     




        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->getResponse()->redirect('/frontend/web/site/eventdraw');
            // return $this->goBack(); 
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
                'authUrl' => $authUrl,
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
         $authUrl = $this->genAuthUrl();


        if ($model->load(Yii::$app->request->post()) && $model->login()) {       
        return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
                 'authUrl' => $authUrl,

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
    //     $userBulletinModel  =   new userBulletinModel();
    //     $templateChangeView         =   new TemplateChangeView();
    //     $this->layout = 'empty';
    //     $user_id = Yii::$app->user->identity->id;
    //     // echo $user_id;die;
    //     $userType   =   Yii::$app->user->identity->userType;

    //     $userData   =   User::findOne($user_id);

    //     $userLog        = new Query();

    //     $loggedEmail    =   Yii::$app->user->identity->email;

    //     $broadDataResult1 = (new Query())
    //                 ->select('T.*,UB.id as bullet_board_users_id,UB.has_seen')
    //                 ->from('new_user_broadcast_email_templates as T')
    //                 ->leftJoin('bulletboarduserdata as UB', 'T.id = UB.bullet_board_id')
    //                 ->where(['UB.email'=>$loggedEmail])
    //                 ->andWhere(['UB.user_id'=>$user_id])
    //                 ->andWhere(['!=', 'UB.has_seen', 0])
    //                 ->orderBy(['UB.id' => SORT_DESC])
    //                 ->one();
    //     $videoData = (new Query())
    //         ->select('T.*')
    //         ->from('new_user_broadcast_email_templates as T')
    //         ->where(['T.display_as' => 'Onboarding'])
    //         ->andWhere(['T.userType' => $userType])
    //         ->orderBy(['T.id' => SORT_DESC])
    //         ->all();
    //     $bulletinIdToUpdate = $broadDataResult1['bullet_board_users_id'];
    //     $userBulletinModel = \common\models\userBulletinModel::findIdentity($bulletinIdToUpdate);
    //     if ($userBulletinModel !== null) {
    //         $userBulletinModel->id = $bulletinIdToUpdate;
    //         $userBulletinModel->has_seen = $broadDataResult1['has_seen'] -1;
    //         $userBulletinModel->save();
    //     }
    //     $frontTemplate          =   new FrontTemplate();
    //     $userLoginInfo = Yii::$app->user->identity;
    //     $clientid       =   $userLoginInfo->clientid;
    //     $user_id        =   $userLoginInfo->id;

    //     // $getFrontTempData   =   $frontTemplate::findOne($clientid);
    //     $templateNameArray           =   array();
    //     $checkUserTemplateChangeView = $templateChangeView::find()->where(['user_id' => $user_id])->andwhere(['is_seen' => 0])->all();
    //     // echo '<pre>';
    //     // print_r($checkUserTemplateChangeView);die;
    //     if (!empty($checkUserTemplateChangeView)) {
    //         foreach ($checkUserTemplateChangeView as $viewObj) {
    //             $getFrontTempData = $frontTemplate::find()->where(['template_id' => $viewObj->template_id])->orderBy(['id' => SORT_DESC])->one();
    //             if (!empty($getFrontTempData)) {
    //                 array_push($templateNameArray,$getFrontTempData->template_name);
    //                 // $frontObj = reset($getFrontTempData); // Get the first element in the array
    //                 $templateChange = new TemplateChangeView();

    //                 $modal = $templateChange::find()->where(['user_id' => $viewObj->user_id])->andWhere(['is_seen' => 0])->one();
    //                 // print_r($modal);
    //                 if (!empty($modal)) {
    //                     $modal->is_seen = 1;
    //                     $modal->template_id = $getFrontTempData->template_id;
    //                     $modal->client_id = $getFrontTempData->client_id;
    //                     $modal->user_id = $user_id;
    //                     if ($modal->save()) {
    //                         // echo 'updated';
    //                     }
    //                 }
    //             }
    //         }
    //     } else {
    //         $templateNameArray = '';
    //     }
    //     // die;
    //     $emailTemplate              =   new EmailTemplate();
    //     $getEmailTemplate           =   $emailTemplate::findOne(['template_type'=>'notify_user_bulletin']);
        
    //     // echo Yii::$app->user->identity->userfullname;

    //     // die;
    //     // print_r($templateNameArray);die;
    //     return $this->render('evdr/index',
    //         [
    //             'broadDataResult'=>$broadDataResult1,
    //             'userType'=>$userType,
    //             'user_id'=>$user_id,
    //             'videoData'=>$videoData,
    //             'getFrontTempData'=>$templateNameArray,
    //             'getEmailTemplate'=>$getEmailTemplate
    //         ]);
    // } 
    public function actionEventdraw()
    {
        $this->layout = 'empty';
        $user_id  = Yii::$app->user->identity->id;
        $userType = Yii::$app->user->identity->userType;

        // Read Momentus Room Diagram URL parameters.
        // SpaceCode = Momentus Space Code (used to look up the EventDraw Template), EventID = Momentus Event ID.
        $orgCode             = trim((string) Yii::$app->request->get('OrgCode', ''));
        $momentusEventId     = (int) Yii::$app->request->get('EventID', 0);
        $spaceCode           = trim((string) Yii::$app->request->get('SpaceCode', ''));
        $eventSpaceDiagramId = (int) Yii::$app->request->get('EventSpaceDiagramID', 0);

        // Resolve the EventDraw template from OrgCode + SpaceCode.
        // OrgCode identifies the client; SpaceCode identifies the space within that client.
        // Both are required together because the same SpaceCode can exist across multiple Momentus organisations.
        $templateId = 0;
        if ($spaceCode !== '' && $orgCode !== '') {
            $client = \common\models\Client::find()
                ->where(['momentusOrgCode' => $orgCode])
                ->one();
            if ($client) {
                $tmpl = \common\models\Template::find()
                    ->where([
                        'momentusSpaceCode' => $spaceCode,
                        'clientid'          => $client->id,
                        'templateActive'    => 1,
                    ])
                    ->one();
                if ($tmpl) {
                    $templateId = (int) $tmpl->id;
                }
            }
        }

        // When a Momentus Template Link is clicked, persist the EventSpaceDiagramID
        // onto the template row so it is always available even if the URL param is later absent.
        if ($templateId > 0 && $eventSpaceDiagramId > 0) {
            $tmpl = $tmpl ?? \common\models\Template::findOne($templateId);
            if ($tmpl && (int) $tmpl->momentusEventSpaceDiagramId !== $eventSpaceDiagramId) {
                $tmpl->momentusEventSpaceDiagramId = $eventSpaceDiagramId;
                $tmpl->save(false, ['momentusEventSpaceDiagramId']);
            }
        }

        return $this->render('evdr/index', [
            'userType'               => $userType,
            'user_id'                => $user_id,
            'urlOrgCode'             => $orgCode,
            'urlMomentusEventId'     => $momentusEventId,
            'urlSpaceCode'           => $spaceCode,
            'urlEventSpaceDiagramId' => $eventSpaceDiagramId,
            'urlTemplateId'          => $templateId,
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
            // throw new BadRequestHttpException($e->getMessage());
            return $this->render('linkExpired');
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

    public function actionBoardDataPopup()
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $user_id = Yii::$app->user->identity->id;
            $loggedEmail = Yii::$app->user->identity->email;

            $broadDataResult = (new \yii\db\Query())
                ->select('T.*, UB.id as bullet_board_users_id, UB.has_seen')
                ->from('new_user_broadcast_email_templates as T')
                ->leftJoin('bulletboarduserdata as UB', 'T.id = UB.bullet_board_id')
                ->where(['UB.email' => $loggedEmail])
                ->andWhere(['UB.user_id' => $user_id])
                ->andWhere(['!=', 'UB.has_seen', 0])
                ->orderBy(['UB.id' => SORT_DESC])
                ->one();

            if ($broadDataResult) {
                $bulletinIdToUpdate = $broadDataResult['bullet_board_users_id'];
                $userBulletinModel = \common\models\userBulletinModel::findIdentity($bulletinIdToUpdate);

                if ($userBulletinModel !== null) {
                    $userBulletinModel->has_seen = $broadDataResult['has_seen'] - 1;
                    $userBulletinModel->save();
                }

                // Replace placeholders
                $content = str_replace(
                    ['{{full_name}}', '{{email_address}}'],
                    [Yii::$app->user->identity->userfullname, Yii::$app->user->identity->email],
                    $broadDataResult['template_image']
                );

               return [
                    'success' => true,
                    'data' => [
                        'heading' => $broadDataResult['heading'],
                        'content' => $content,
                        'seen' => $broadDataResult['has_seen']
                    ]
                ];
            }

          return [
                'success' => false,
                'message' => 'No bulletin found'
            ];
        }//actionBoardDataPopup

public function slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', ' ', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        if (empty($text))
            return '';
        return $text;
    }

public function actionUsersVideoData()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $userType = Yii::$app->user->identity->userType;

    $videoData = (new \yii\db\Query())
        ->select(['T.id', 'T.template_image', 'T.heading'])
        ->from('new_user_broadcast_email_templates as T')
        ->where(['T.display_as' => 'Onboarding'])
        ->andWhere(['T.userType' => $userType])
        ->orderBy(['T.id' => SORT_DESC])
        ->all();

    $dom = new \DOMDocument();
    $videoList = [];

    foreach ($videoData as $video) {
        $template_image = $video['template_image'];

        // Suppress warnings from invalid HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($template_image);
        libxml_clear_errors();

        $iframes = $dom->getElementsByTagName('iframe');

        foreach ($iframes as $iframe) {
            $src = $iframe->getAttribute('src');

            $videoList[] = [
                'videoURL' => $src,
                'videoThamb' => $src, // You may want to extract an actual thumbnail URL instead
                'videoTitle' => $this->slugify($video['heading']),
            ];
        }
    }

    return [
        'success' => true,
        'data' => $videoList
    ];
}//actionUsersVideoData

public function actionGetUserTemplateChanges()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $userLoginInfo = Yii::$app->user->identity;
    $user_id = $userLoginInfo->id;

    $templateNameArray = [];
    $templateChangeView = new TemplateChangeView();
    $frontTemplate = new FrontTemplate();

    // Find unseen template changes for user
    $checkUserTemplateChangeView = $templateChangeView::find()
        ->where(['user_id' => $user_id,'is_seen' => 0])
        ->all();

    if (!empty($checkUserTemplateChangeView)) {
        foreach ($checkUserTemplateChangeView as $viewObj) {
            $getFrontTempData = $frontTemplate::find()
                ->where(['template_id' => $viewObj->template_id])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if (!empty($getFrontTempData)) {
                $templateNameArray[] = $getFrontTempData->template_name;

                // Mark as seen
                $modal = $templateChangeView::find()
                    ->where(['user_id' => $viewObj->user_id, 'is_seen' => 0])
                    ->one();

                if (!empty($modal)) {
                    $modal->is_seen = 1;
                    $modal->template_id = $getFrontTempData->template_id;
                    $modal->client_id = $getFrontTempData->client_id;
                    $modal->user_id = $user_id;
                   $modal->save();
                }
            }
        }
    }

    if (!empty($templateNameArray)) {
        // Prepare email template
        $emailTemplate = EmailTemplate::findOne(['template_type' => 'notify_user_bulletin']);
        $templateList = implode('<br>', $templateNameArray);

        $find = ['{{first_name}}', '{{template_name}}'];
        $replace = [
            Yii::$app->user->identity->firstname,
            '<br><br>' . $templateList . '<br><br>'
        ];

        $content = str_replace($find, $replace, $emailTemplate->content ?? '');

       
        return [
            'success' => true,
            'data' => [
                'content' => $content
            ]

        ];
       
    }

    return [
        'success' => false,
        'message' => 'No template change found'
    ];
}//actionGetUserTemplateChanges

// public function actionSsoLogin()
// {
//     $request = Yii::$app->request;
//     // $client = new \yii\httpclient\Client();

//     $auth_code = $request->get('code');

//     if (!$auth_code) {
//         Yii::$app->session->setFlash('error', 'Authorization code missing.');
//         return $this->redirect(['site/login']);
//     }

//     $state = Yii::$app->request->get('state');
//     $sessionState = Yii::$app->session->get('azure_oauth_state');

//     if (!$state || $state !== $sessionState) {
//         Yii::$app->session->setFlash('error', 'Invalid authentication state.');
//         return $this->redirect(['site/login']);
//     }

//     $azure = Yii::$app->params['microsoft'];

//     $token_url = "https://login.microsoftonline.com/common/oauth2/v2.0/token";


//     // $token_url = "https://login.microsoftonline.com/" . $azure['tenant_id'] . "/oauth2/v2.0/token";

//     $post_fields = [
//         'client_id' => $azure['client_id'],
//         'client_secret' => $azure['client_secret'],
//         'code' => $auth_code,
//         'redirect_uri' => $azure['redirect_uri'],
//         'grant_type' => 'authorization_code',
//         'scope' => $azure['scope'],
//     ];

//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $token_url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//     $response = curl_exec($ch);
//     curl_close($ch);

//     // $response = $client->createRequest()
//     // ->setMethod('POST')
//     // ->setUrl($token_url)
//     // ->setData($post_fields)
//     // ->send();

//     $token_data = json_decode($response, true);

//     if (!isset($token_data['id_token'])) {
//         Yii::$app->session->setFlash('error', 'Microsoft authentication failed.');
//         return $this->redirect(['site/login']);
//     }

//     $id_token = $token_data['id_token'];

//     $token_parts = explode('.', $id_token);
//     $payload = json_decode(base64_decode($token_parts[1]), true);

//     $email = $payload['email'] ?? null;

//     if (!$email) {
//         Yii::$app->session->setFlash('error', 'Email not returned from Microsoft.');
//         return $this->redirect(['site/login']);
//     }

//     $user = \common\models\User::find()->where(['email' => $email])->one();

//     if (!$user) {

//         Yii::$app->session->setFlash(
//             'error',
//             'Your Microsoft account (' . $email . ') is not registered in EventDraw. Please contact administrator.'
//         );

//         return $this->redirect(['site/login']);
//     }

//     Yii::$app->user->login($user);

//     return $this->redirect(['site/eventdraw']);
// }


//sso upgarde


    public function actionSsoLogin()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        // 1. Handle Azure errors
        if ($error = $request->get('error')) {
            $session->setFlash('error', 'Microsoft login cancelled.');
            return $this->redirect(['site/login']);
        }

        $auth_code = $request->get('code');
        $state     = $request->get('state');

        if (!$auth_code) {
            $session->setFlash('error', 'Authorization code missing.');
            return $this->redirect(['site/login']);
        }

        // 2. Validate state
        if ($state !== $session->get('azure_oauth_state')) {
            $session->setFlash('error', 'Invalid authentication state.');
            return $this->redirect(['site/login']);
        }

        $azure = Yii::$app->params['microsoft'];

        // 3. Exchange token
        $token_url = "https://login.microsoftonline.com/common/oauth2/v2.0/token";

        $post_fields = [
            'client_id'     => $azure['client_id'],
            'client_secret' => $azure['client_secret'],
            'code'          => $auth_code,
            'redirect_uri'  => $azure['redirect_uri'],
            'grant_type'    => 'authorization_code',
            'scope'         => $azure['scope'],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $token_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post_fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Yii::error(curl_error($ch), 'sso');
            curl_close($ch);

            $session->setFlash('error', 'Connection error.');
            return $this->redirect(['site/login']);
        }

        curl_close($ch);

        $token_data = json_decode($response, true);

        if (!isset($token_data['id_token'])) {
            Yii::error($token_data, 'sso');

            $session->setFlash('error', 'Microsoft authentication failed.');
            return $this->redirect(['site/login']);
        }

        $id_token = $token_data['id_token'];

        // =========================
        // 🔐 STEP 1: Decode FIRST (to get tenant)
        // =========================

        list($headerB64, $payloadB64, $signatureB64) = explode('.', $id_token);

        $header = json_decode($this->base64UrlDecode($headerB64), true);
        $payload = json_decode($this->base64UrlDecode($payloadB64), true);

        $tenantId = $payload['tid'] ?? 'common';

        // =========================
        // 🔐 STEP 2: VERIFY SIGNATURE (tenant-specific)
        // =========================

        if (!$this->verifyJwtSignature($id_token, $tenantId)) {
            $session->setFlash('error', 'Invalid token signature.');
            return $this->redirect(['site/login']);
        }

        // =========================
        // 🔐 STEP 3: VALIDATE CLAIMS
        // =========================

        if (($payload['aud'] ?? null) !== $azure['client_id']) {
            $session->setFlash('error', 'Invalid audience.');
            return $this->redirect(['site/login']);
        }

        if (($payload['exp'] ?? 0) < time()) {
            $session->setFlash('error', 'Token expired.');
            return $this->redirect(['site/login']);
        }

        // issuer check
        $validIssuer = "https://login.microsoftonline.com/{$tenantId}/v2.0";

        if ($payload['iss'] !== $validIssuer) {
            $session->setFlash('error', 'Invalid issuer.');
            return $this->redirect(['site/login']);
        }

        // nonce check
        if (($payload['nonce'] ?? null) !== $session->get('azure_nonce')) {
            $session->setFlash('error', 'Invalid nonce.');
            return $this->redirect(['site/login']);
        }

        // =========================
        // 🔐 USER DATA
        // =========================

        $email = $payload['email']
            ?? $payload['preferred_username']
            ?? null;

        if (!$email) {
            $session->setFlash('error', 'Email not provided.');
            return $this->redirect(['site/login']);
        }

        $email = strtolower(trim($email));

        $user = \common\models\User::find()->where(['email' => $email])->one();

        // =========================
        // 🔐 AUTO CREATE USER
        // =========================

        if (!$user) {

            $time = time();

            $user = new \common\models\User();
            $user->username = $email;
            $user->email = $email;

            $user->firstname = $payload['given_name'] ?? null;
            $user->surname = $payload['family_name'] ?? null;
            $user->userfullname = $payload['name'] ?? $email;

            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->password_hash = Yii::$app->security->generatePasswordHash(
                Yii::$app->security->generateRandomString()
            );

            $user->status = 9;
            $user->expiry_date = strtotime('+7 days');

            $user->created_at = $time;
            $user->updated_at = $time;

            if (!$user->save()) {
                Yii::error($user->errors, 'sso');

                $session->setFlash('error', 'User creation failed.');
                return $this->redirect(['site/login']);
            }
        }

        // =========================
        // 🔐 LOGIN
        // =========================

        $user->last_login = time();
        $user->save(false);

        Yii::$app->user->login($user);

        // cleanup
        $session->remove('azure_oauth_state');
        $session->remove('azure_nonce');

        return $this->redirect(['site/eventdraw']);
    }

    // =========================
    // 🔐 HELPERS
    // =========================

    private function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function getMicrosoftKeys($tenantId)
    {
        $cache = Yii::$app->cache;
        $cacheKey = 'ms_keys_' . $tenantId;

        $keys = $cache->get($cacheKey);

        if ($keys === false) {
            $url = "https://login.microsoftonline.com/{$tenantId}/discovery/v2.0/keys";

            $jwks = file_get_contents($url);
            $keys = json_decode($jwks, true);

            $cache->set($cacheKey, $keys, 3600);
        }

        return $keys;
    }

    private function verifyJwtSignature($jwt, $tenantId)
    {
        list($headerB64, $payloadB64, $signatureB64) = explode('.', $jwt);

        $header = json_decode($this->base64UrlDecode($headerB64), true);

        $keys = $this->getMicrosoftKeys($tenantId);

        foreach ($keys['keys'] as $key) {

            if ($key['kid'] === $header['kid']) {

                $pem = $this->jwkToPem($key);

                $data = $headerB64 . '.' . $payloadB64;
                $signature = $this->base64UrlDecode($signatureB64);

                return openssl_verify($data, $signature, $pem, OPENSSL_ALGO_SHA256) === 1;
            }
        }

        return false;
    }

    private function jwkToPem($jwk)
    {
        $modulus = $this->base64UrlDecode($jwk['n']);
        $exponent = $this->base64UrlDecode($jwk['e']);

        $modulus = "\x02" . $this->encodeLength(strlen($modulus)) . $modulus;
        $exponent = "\x02" . $this->encodeLength(strlen($exponent)) . $exponent;

        $sequence = "\x30" . $this->encodeLength(strlen($modulus . $exponent)) . $modulus . $exponent;

        $bitstring = "\x03" . $this->encodeLength(strlen($sequence) + 1) . "\x00" . $sequence;

        $algo = "\x30\x0D\x06\x09\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01\x05\x00";

        $rsa = "\x30" . $this->encodeLength(strlen($algo . $bitstring)) . $algo . $bitstring;

        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($rsa), 64) .
            "-----END PUBLIC KEY-----";
    }

    private function encodeLength($length)
    {
        if ($length <= 0x7F) return chr($length);

        $temp = ltrim(pack('N', $length), "\x00");

        return chr(0x80 | strlen($temp)) . $temp;
    }

    private function genAuthUrl()
    {
        $session = Yii::$app->session;

        $state = Yii::$app->security->generateRandomString(32);
        $nonce = Yii::$app->security->generateRandomString(32);

        $session->set('azure_oauth_state', $state);
        $session->set('azure_nonce', $nonce);

        $azure = Yii::$app->params['microsoft'];

        return "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?"
            . http_build_query([
                'client_id' => $azure['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $azure['redirect_uri'],
                'response_mode' => 'query',
                'scope' => $azure['scope'],
                'state' => $state,
                'nonce' => $nonce,
                'prompt' => 'select_account'
            ]);
    }
//sso upgarde


}//class
