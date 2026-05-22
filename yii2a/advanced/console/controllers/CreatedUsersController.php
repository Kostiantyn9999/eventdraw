<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class CreatedUsersController extends Controller
{
    public function actionIndex()
    {

        $email_to = 'sales@eventdraw.com';
        //$email_to = 'fedalal@gmail.com';
        $date_request = strtotime("-1 days");


        $body_text = 'List of new TRIAL users for ' . date('F j, Y',$date_request);

        $dataProvider = new SqlDataProvider([
            'sql' => 'select id, username, userfullname from user
where created_at >= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -1 DAY))
and (status = 9 or status = 11)
order by id',
              'pagination' => false,
        ]);

        $users = $dataProvider->getModels();

        $items = ArrayHelper::map($users,'id','username', 'userfullname');


        $arrlength = count($items);

        echo "Found new users:"  . $arrlength . "\n";
        
        if ($arrlength > 0) 
        {
            $body_text = $body_text . '<br>' . strval($arrlength) .  ' new Trial users';
            $body_text = $body_text . '<table  style="border-collapse: collapse;" border="1">';
            $body_text = $body_text . '<tr>';
            $body_text = $body_text . '<td> <strong>id</strong></td>';
            $body_text = $body_text . '<td> <strong>User Name</strong></td>';
            $body_text = $body_text . '<td> <strong>Full Name</strong></td>';
            $body_text = $body_text . '</tr>';
            

         for ($i = 0; $i <= $arrlength; $i++) {
           $item = $users[$i];

            $body_text = $body_text . '<tr>';
            $body_text = $body_text . '<td>' .  array_values($item)[0] .  '</td>';
            $body_text = $body_text . '<td>' .  array_values($item)[1] .  '</td>';
            $body_text = $body_text . '<td>' .  array_values($item)[2] .  '</td>';
            $body_text = $body_text . '</tr>';

           }

         $body_text = $body_text . '</tbody></table>';
         
        }
        else {
           $body_text = $body_text . '<br>' . 'No new Trial users';  
        }


        $message = Yii::$app->mailer->compose(
            'new-email-template-body',
            [
                'name' => '',
                'body' => $body_text,
            ]
        )
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($email_to)
            ->setTo('sales@eventdraw.com')
            ->setSubject('List of new TRIAL users for ' . date("F j, Y",$date_request));

        if (Yii::$app->mailer->send($message)) {
            echo "Sent new users email"  . "\n";
        }           
               
        
        
    }
}