<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $modelUser common\models\User */
/* @var $modelTemplate common\models\Template */

$this->title = 'Create Client';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
        'modelTemplate' => $modelTemplate,
    ]) ?>

</div>
