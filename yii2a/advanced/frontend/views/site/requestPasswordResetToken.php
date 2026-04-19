<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    

    <div class="row">
        <div class="col-lg-12">

            <div class="text-center">
               <img src="/backend/web/images/login_logo.png" alt="Event Draw">
            </div>

            <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out your email. A link to reset password will be sent there.</p>


            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Email'])->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<style type="text/css">
    
    .site-request-password-reset {
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

form#form-signup {
    color: #fff;
}

.mb-30 { margin-bottom: 30px; }

p.help-block.help-block-error {
    color: #fff;
    background: red;
    padding: 0 5px;
    border-radius: 3px;
}
p.help-block.help-block-error:empty {display:none;}

</style>