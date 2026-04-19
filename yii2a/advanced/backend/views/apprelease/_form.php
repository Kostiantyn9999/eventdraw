<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Apprelease */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apprelease-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nrelease')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'htmlCode')->textarea(['rows' => 6]) ?>

       <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
