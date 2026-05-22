<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */

$this->title = 'Create New Bulletin';
$this->params['breadcrumbs'][] = ['label' => 'New Bulletin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-user-broadcast-email-templates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-bulletin', [
        'model' => $model,
    ]) ?>

</div>
