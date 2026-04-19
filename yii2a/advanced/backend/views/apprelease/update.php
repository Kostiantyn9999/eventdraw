<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apprelease */

$this->title = 'Update Apprelease: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Appreleases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="apprelease-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
