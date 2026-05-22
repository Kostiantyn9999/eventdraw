<?php

namespace backend\commands;

use yii\console\Controller;

class NewUserEmailTemplate extends Controller
{
    public $message;

    public function options($actionID)
    {
        return ['message'];
    }

    public function optionAliases()
    {
        return ['m' => 'message'];
    }

    public function actionIndex()
    {
        echo $this->message . "\n";
    }
}