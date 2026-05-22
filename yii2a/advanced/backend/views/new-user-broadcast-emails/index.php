<?php

use kartik\editors\Summernote;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'New User Broadcast Emails';
$this->params['breadcrumbs'][] = ['label' => 'Send Bulk MailOut', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <label for="template-1">Template 1</label>
        <?= Summernote::widget([
            'name' => 'body',
            'enableHintEmojis' => true,
            'options' => ['id' => 'template-1', 'placeholder' => 'Create an Email']
        ]);
        ?>
    </div>
</div>
