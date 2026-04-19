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


    <?= $form->field($model, 'status')->dropDownList([
    '10' => 'Active',
    '9' => 'Inactive'
    ])?>

    <?= $form->field($model, 'company_admin')
                    ->checkbox([
                        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                    ])
    ?>
    
   


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
