<?php

namespace console\controllers;

use common\models\NewUserBroadcastEmailTemplates;
use common\models\User;
use Yii;
use yii\console\Controller;

class NewUserEmailTemplateController extends Controller
{
    public function actionIndex()
    {
        $templates = NewUserBroadcastEmailTemplates::find()->orderBy('order')->all();
        $users = User::find()->all();

        foreach ($templates as $template) {
            foreach ($users as $model) {
                $created_at = date('Y-m-d H:i:s', strtotime("+" . $template->hours . " hours", $model->created_at));
                $current_date = date('Y-m-d H:i:s');

                if ($model->template_number === $template->order && $current_date > $created_at) {
                    $message = Yii::$app->mailer->compose(
                        'new-email-template-body',
                        [
                            'name' => $model->firstname,
                            'body' => $template->template_image,
                        ]
                    )
                        ->setFrom(Yii::$app->params['supportEmail'])
                        ->setTo($model->email)
                        ->setSubject($template->heading);

                    if (Yii::$app->mailer->send($message)) {
                        $model->last_email_date = date('Y-m-d H:i:s');
                        $model->template_number++;
                        $model->email_count++;

                        $model->save();

                        echo "Sent Email Template #" . $template->id . " to : " . $model->email . "\n";

                        sleep(5);
                    }
                }
            }
        }
    }
}