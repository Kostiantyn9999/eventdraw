<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Email Users';
$this->params['breadcrumbs'][] = ['label' => 'Email Users', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['selectedUsers' => $selectedUsers, 'page' => $page,'dataProvider'=>$dataProvider]) ?>
</div>
