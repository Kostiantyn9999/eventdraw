<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'User Templates: ' . $model->userfullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Templates';

//get current list of templates for this user
$model->user_templates=$model::getUserTemplates($model->id);
?>

<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-templates']); ?>

            <div style='margin-bottom: 2px'>
                    <input id='check-all-templates' type='checkbox'/> Select All
            </div>

            <br>

            <?= $form->field($model, 'user_templates')->CheckBoxList($model::getTemplateListClient())?>

            <div class="form-group">
                <?= Html::submitButton('Set company user templates', ['class' => 'btn btn-primary', 'name' => 'psw-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<script>

$(document).on('change', '#check-all-templates', function() {
      var selector = $(this).is(':checked') ? ':not(:checked)' : ':checked';
      
      $('#user-user_templates input[type="checkbox"]' + selector).each(function() {
        $(this).trigger('click');
    });
});      


</script>
