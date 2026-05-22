<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */

$this->title = 'Create New User Broadcast Email Templates';
$this->params['breadcrumbs'][] = ['label' => 'New User Broadcast Email Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-user-broadcast-email-templates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
