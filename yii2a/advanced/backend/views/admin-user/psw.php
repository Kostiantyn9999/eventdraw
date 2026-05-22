<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Password  User: ' . $model->userfullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Password';

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-psw']); ?>

               <?= $form->field($model, 'new_password')->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Set new password', ['class' => 'btn btn-primary', 'name' => 'psw-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
