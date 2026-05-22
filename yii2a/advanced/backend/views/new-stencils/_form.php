<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NewStencils */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stencil-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'stencilName')->textInput(['maxlength' => true]) ?>


   <?= $form->field($model, 'stencilXML')->textarea(['rows' => 6]) ?>

        <div class="panel-heading">
            <?= Html::a('Load version', ['new-stencils/versions', 'id' => $model->ID], ['class' => 'btn btn-primary']) ?>
        </div>

    <?= $form->field($model, 'stencilActive')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

   
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
