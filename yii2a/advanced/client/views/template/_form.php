<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInput;
use common\models\Client;

/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(

    );

    ?>




    <?= $form->field($model, 'templateName')->textInput(['maxlength' => true]) ?>

    <?php if (Client::usesClientResourceScoping()): ?>
        <?= Html::activeHiddenInput($model, 'clientid') ?>
    <?php else: ?>
        <?php
        $clients = \common\models\Client::find()->all();
        $items = ArrayHelper::map($clients, 'id', 'clientName');
        echo $form->field($model, 'clientid')->dropDownList($items, ['prompt' => '-- Select client --']);
        ?>
    <?php endif; ?>
    
    <?= $form->field($model, 'xmlCode')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'templateActive')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'templateDefault')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'shadow')->checkbox([
        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
    ])?>

    <?= $form->field($model, 'subtemplate')->widget(MultipleInput::className(), [
        'max' => 20,
        'min' => 0, // should be at least 0 rows
        'columns' => [
            [
                'name'  => 'id',
                'type'  => 'hiddenInput',
            ],
            [
                'name'  => 'active',
                'type'  => 'checkbox',
                'title' => 'Active',
                'defaultValue' => true,

            ],

            [
                'name'  => 'subtemplatename',
                'title' => 'Name',
                'enableError' => true,
                'options' => [
                    'class' => 'input-priority'
                ]
            ],
            [
                'name'  => 'xmlCode',
                'title' => 'XML Code',
                'enableError' => true,
                'options' => [
                    'class' => 'input-priority'
                ]
            ],

        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
