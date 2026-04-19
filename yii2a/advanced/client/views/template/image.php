<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(
        [
            'options' => ['enctype' => 'multipart/form-data'],
        ]
    );

    $this->title = $model->templateName;
    $this->params['breadcrumbs'][] = ['label' => 'Templates', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;

    ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <h4>Please select image 140px * 140px</h4>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Set image', ['class' => 'btn btn-success', 'name' => 'image-button']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
