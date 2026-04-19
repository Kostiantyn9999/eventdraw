<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
   

    <div class="row">
        <div class="col-lg-12">

            <div class="text-center">
               <img src="/backend/web/images/login_logo.png" alt="Event Draw">
            </div>

             <h1><?= Html::encode($this->title) ?></h1>

    <p>Please choose your new password:</p>

            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder' => 'Password'])->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<style type="text/css">
    
    .site-reset-password {
    max-width: 300px;
    margin: 0 auto;
}

body {
    background: #a8518a;
       color: #fff;
}

button.btn.btn-primary {
    width: 100%;
    background-color: #24312a;
}

form#reset-password-form {
    color: #fff;
}

p.help-block.help-block-error {
    color: #fff;
    background: red;
    padding: 0 5px;
    border-radius: 3px;
}
p.help-block.help-block-error:empty {display:none;}

</style>