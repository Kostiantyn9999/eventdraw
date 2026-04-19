<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MomentusShape */
/* @var $returnUrl string */
/* @var $orgCode string */

$this->title = 'Update Shape: ' . $model->shapeType;
$this->params['breadcrumbs'][] = ['label' => 'Shapes', 'url' => ['shape-manager']];
$this->params['breadcrumbs'][] = ['label' => $model->shapeType, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shape-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'returnUrl' => $returnUrl ?? '',
        'orgCode' => $orgCode ?? '',
    ]) ?>

</div>
