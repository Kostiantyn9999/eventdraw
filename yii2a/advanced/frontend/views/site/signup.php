<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use yii\captcha\Captcha;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    

    <div class="row">
        <div class="col-lg-12">

            <div class="text-center">
               <img src="/backend/web/images/login_logo.png" alt="Event Draw">
            </div>

            <h1><?= Html::encode($this->title) ?></h1>
            <p>Please fill out the following fields to signup:</p>
            <p>For assistance visit <a href="https://eventdraw.com" style="font-weight: bold;color: #fff;">eventdraw.com</a></p>
            <?= Yii::$app->session->getFlash('success');?>
            
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'firstname')->textInput(['autofocus' => true,'placeholder' => "First Name"])->label(false);?>
                <?= $form->field($model, 'surname')->textInput(['autofocus' => true,'placeholder' => "Surname"])->label(false); ?>

                <?= $form->field($model, 'UserCompanyName')->textInput(['autofocus' => true,'placeholder' => "Company Name"])->label(false); ?>


                <?= $form->field($model, 'email')->textInput()->input('email', ['placeholder' => "Email"])->label(false); ?>
                
                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true,'placeholder' => "Password"])->label(false); ?>
                
                <?= Yii::$app->session->getFlash('error');?>
                <div class="g-recaptcha" data-sitekey="6LesJIYcAAAAAFHymlWw8N0vzFCiCmk2KdtnQbLY" data-callback="recaptchaFunction"></div>
                <div class="form-group" style="margin-top: 15px;">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button','disabled'=>'disabled']) ?>
                </div>

            <?php ActiveForm::end(); ?>

            <!-- <div class="form-group">
                <p>
                    <a href="http://login.eventdraw.com.ai/" target="_blank" class="btn btn-info btn-block">Login</a>
                </p>
            </div> -->
        </div>
    </div>
</div>
<script src="/frontend/web/assets/4fdb898c/jquery.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script type="text/javascript">
    function recaptchaFunction(){
        $(".btn-primary").removeAttr("disabled");   
    }
</script>
<style type="text/css">
    
    .site-signup {
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

form#form-signup{
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
mark {
    color: #2040a0;
    font-size: 20px;
    cursor: none;
    pointer-events: none;
    user-select: none;
    font-style: italic;
}
</style>