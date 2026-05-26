<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MomentusShape */
/* @var $form yii\widgets\ActiveForm */
/* @var $returnUrl string */
/* @var $orgCode string */
?>

<div class="shape-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (!empty($returnUrl)): ?>
        <?= Html::hiddenInput('return_url', $returnUrl) ?>
    <?php endif; ?>
    <?php if (!empty($orgCode)): ?>
        <?= Html::hiddenInput('org_code', $orgCode) ?>
    <?php endif; ?>

    <?php if (\common\models\Client::usesClientResourceScoping() && \common\models\MomentusShape::hasClientIdColumn()): ?>
        <?= Html::activeHiddenInput($model, 'clientid') ?>
    <?php endif; ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'shapeType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ed_shapes_category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shapetypes')->textInput(['maxlength' => true])->hint('Internal geometry type (ShapeType on canvas), if different from shape name.') ?>

    <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <?= $form->field($model, 'elevate')->textInput() ?>

    <?= $form->field($model, 'height')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
