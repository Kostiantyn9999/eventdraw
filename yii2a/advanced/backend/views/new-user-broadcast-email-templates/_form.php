<?php

use dosamigos\tinymce\TinyMce;
use kartik\editors\Summernote;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="new-user-broadcast-email-templates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'heading')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'template_image')->widget(TinyMce::class, [
        'options' => ['id' => 'email-paragraph', 'placeholder' => 'We are gald to welcome ....', 'rows' => 20],
        'language' => 'en',
        'clientOptions' => [
            'plugins' => [
                "advlist autolink lists link charmap print preview anchor image",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
        ],
    ]); ?>

    <?= $form->field($model, 'link_heading')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'button_link')->textInput(['maxlength' => true]) ?>

    <!-- <?= $form->field($model, 'hours')->textInput(['maxlength' => true]) ?> -->
    <div class="row" style="display:none;">
        <div class="col-md-4">
            <?php  $form->field($model, 'hours')->dropDownList(['0'=>'1','1' => '2', '2' => '3','4'=>'4','5'=>'5']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'display_as')->dropDownList(['Onboarding'=>'Onboarding','Yes'=>'Yes','No'=>'No']) ?>
        </div>
        <div class="col-md-4">
            <?php  $form->field($model, 'no_of_time_delay')->dropDownList(['1'=>'1','2'=>'2','3'=>'3']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'no_of_time_bulletin_show')->dropDownList(['2'=>'2','2'=>'2','3'=>'3']) ?>
        </div>
    </div>
    <?= $form->field($model, 'order')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'userType')->dropDownList(['0'=>'Not Set','1' => 'Venue', '2' => 'Event Organiser']) ?>

    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button id="preview-email" type="button" class="btn btn-success">Preview Email</button>
        <div class="modal fade" id="previewTemplate1Modal" tabindex="-1" role="dialog" aria-labelledby="previewTemplate1Modal" aria-hidden="true">
            <?= $this->render('_preview-template1-modal') ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).on('click', '#preview-email', function() {
        var body = tinymce.get("email-paragraph").getContent();

        document.getElementById('email-template-body').innerHTML = body;

        $('#previewTemplate1Modal').modal('toggle');
    });
</script>
