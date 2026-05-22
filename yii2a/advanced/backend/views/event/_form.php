<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */

$users = \common\models\User::find()->all();

    $items = ArrayHelper::map($users,'id','userfullname');
    $params = [
        'prompt' => '-- Select user --'
    ];

?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'eventName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'xmlCode')->textarea(['rows' => 6]) ?>

    <div class="panel-heading">
            <?= Html::a('Load version', ['event/versions', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

             <?= Html::a('Apply matterport', ['event/matterport', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </div>


    <?php
    echo $form->field($model, 'userid')->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'eventActive')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'event_shared')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

       


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
