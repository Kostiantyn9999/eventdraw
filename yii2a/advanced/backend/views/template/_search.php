<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TemplateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'templateName') ?>

    <?= $form->field($model, 'xmlCode') ?>

    <?= $form->field($model, 'templateActive') ?>

    <?= $form->field($model, 'templateDefault') ?>

    <?= $form->field($model, 'templateSize') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
