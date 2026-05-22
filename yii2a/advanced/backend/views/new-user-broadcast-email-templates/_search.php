<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplatesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="new-user-broadcast-email-templates-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'heading') ?>

    <?= $form->field($model, 'template_image') ?>

    <?= $form->field($model, 'link_heading') ?>

    <?= $form->field($model, 'link') ?>

    <?= $form->field($model, 'hours') ?>

    <?php // echo $form->field($model, 'button_link') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
