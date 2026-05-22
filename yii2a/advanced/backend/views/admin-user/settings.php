<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'User Settings: ' . $model->userfullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Settings';

//get current list of stencils for this user
//$model->user_settings=$model::getUserSettings($model->id);
$model->user_settings_meas_unit = $model::getUserSettings($model->id,'meas_unit');
if (!$model->user_settings_meas_unit)
{
    $model->user_settings_meas_unit = 'M'; //meters by default
}

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-settings']); ?>

            <?= $form->field($model, 'user_settings_meas_unit')->dropDownList([
                'BOTH' => 'Both (Meters and Feet)',
                'M' => 'Meters',
                'FT' => 'Feet',

            ])?>


            <div class="form-group">
                <?= Html::submitButton('Set user settings', ['class' => 'btn btn-primary', 'name' => 'settings-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>






