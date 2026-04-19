<?php

namespace frontend\controllers;

use common\models\GuestAllocation;
use Yii;
use yii\base\Security;use yii\helpers\Html;
use yii\validators\EmailValidator;
use yii\web\HttpException;


class GenerateLinkController extends \yii\web\Controller
{
    private function generateSecret($length = 20)
    {
        $security = new Security();
        $full = $security->generateRandomString($length);
        return substr($full, 0, $length);
    }

    public function actionIndex()
    {
        //Get data from post request
        $request = Yii::$app->request;

        $allocName = $request->post('allocname');
        $allocExpiry = $request->post('expiry');
        $allocPsw = $request->post('password');
        $allocEmail = $request->post('email');
        $allocComments = $request->post('comments');
        $allocXML = $request->post('diagram');

        $allocLink =  $this->generateSecret(20);
        $allocFullLink = 'http://allocation.eventdraw.com.au?id=' . $allocLink;

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
            if ($allocEmail){
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

            return $this->render('index');
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
}
