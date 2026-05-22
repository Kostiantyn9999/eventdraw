<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInput;

/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(

    );


    $clients = \common\models\Client::find()
            ->select(['id', 'clientName'])
            ->orderBy(['clientName' => SORT_ASC])->all();

    $items = ArrayHelper::map($clients,'id','clientName');
    $params = [
        'prompt' => '-- Select client --'
    ];


    $matterports = \common\models\Matterport::find()
            ->select(['id', 'name'])
            ->orderBy(['name' => SORT_ASC])->all();

    $MTitems = ArrayHelper::map($matterports,'id','name');
    $MTparams = [
        'prompt' => '-- Select matterport --'
    ];


    $realistics = \common\models\Realistic::find()
            ->select(['id', 'name'])
            ->orderBy(['name' => SORT_ASC])->all();

    $RT_items = ArrayHelper::map($realistics,'id','name');
    $RTparams = [
        'prompt' => '-- Select realistic --'
    ];



    ?>




    <?= $form->field($model, 'templateName')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'clientid')->dropDownList($items,$params);
    ?>
    
    <?php
    echo $form->field($model, 'matterportid')->dropDownList($MTitems,$MTparams);
    ?>

    <?php
    echo $form->field($model, 'realisticid')->dropDownList($RT_items,$RTparams);
    ?>




    <?= $form->field($model, 'xmlCode')->textarea(['rows' => 6]) ?>

        <div class="panel-heading">
            <?= Html::a('Load version', ['template/versions', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>

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
