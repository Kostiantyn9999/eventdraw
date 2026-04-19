<?php

use common\models\Client;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Client Templates: ' . $model->clientName;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Templates';

//get current list of templates for this client
$model->client_templates=$model::getClientTemplates($model->id);
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-templates']); ?>

            <?= $form->field($model, 'client_templates')->CheckBoxList($model::getTemplateList())?>

            <div class="form-group">
                <?= Html::submitButton('Set client templates', ['class' => 'btn btn-primary', 'name' => 'psw-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
