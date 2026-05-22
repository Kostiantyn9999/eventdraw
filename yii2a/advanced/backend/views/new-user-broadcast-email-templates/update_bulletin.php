<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */

$this->title = 'Update New Bulletin: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bulletin', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="new-user-broadcast-email-templates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-bulletin', [
        'model' => $model,
    ]) ?>

</div>
