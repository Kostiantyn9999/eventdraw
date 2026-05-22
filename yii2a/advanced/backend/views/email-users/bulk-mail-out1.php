<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Bulk MailOut';
$this->params['breadcrumbs'][] = ['label' => 'Send Bulk MailOut', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [

    ]) ?>
</div>
