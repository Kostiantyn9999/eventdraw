<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MomentusShapeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shape-search">

    <?php $form = ActiveForm::begin([
        'action' => ['shape-manager'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'shapeType') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'model') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
