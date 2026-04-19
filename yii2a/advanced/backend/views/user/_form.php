<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin();
    $clients = \common\models\Client::find()->all();

    $items = ArrayHelper::map($clients,'id','clientName');
    $params = [
        'prompt' => '-- Select client --'
    ];

    $stensils = \common\models\Stencil::find()->all();

    $itemStencils = ArrayHelper::map($stensils,'id','stencilName');
    $paramStencils = [
        'prompt' => '-- Select stencil --'
    ];
    ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>


    <?php
    echo $form->field($model, 'clientid')->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'userPayment')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'status')->dropDownList([
    '10' => 'Full Version',
    '9' => 'Trial',
    '11' => 'Custom Trial',
    ])?>

    <?= $form->field($model, 'expiry_date')->widget(\yii\jui\DatePicker::class, [
        // if you are using bootstrap, the following line will set the correct style of the input field
        'options' => ['class' => 'form-control'],
        // ... you can configure more DatePicker properties here
    ]) ?>

    <!--    --><?php //echo DatePicker::widget([
//        'model' => $model,
//        'attribute' => 'expiry_date',
//        //'language' => 'ru',
//        //'dateFormat' => 'yyyy-MM-dd',
//    ]);?>

    <?= $form->field($model, 'userIsAdmin')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'userIsSupport')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'company_admin')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'maxSession')->textInput(['type' => 'number']) ?>

   <?php
    echo $form->field($model, 'userStencilid')->dropDownList($itemStencils,$paramStencils);
    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
