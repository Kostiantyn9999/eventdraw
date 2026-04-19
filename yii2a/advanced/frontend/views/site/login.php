<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <!--h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to <b>Admin/support </b>login:</p-->

    <div class="row">
        <div class="col-lg-12">

            <div class="text-center mb-30">
            <a href="https://www.eventdraw.com/">
                <img class="momentus_logo" alt="Event Draw" src="/frontend/web/images/momentus-logo.svg">                
            </a>
            <p class="ed_dark">Powered by EventDraw</p>
            </div>

            <div class="eventdraw-link">
             <h4>Momentus Event Diagramming</h4>
             <p>Secure Login</p>
            </div>



<div id="login_box">

    <?php if (Yii::$app->session->hasFlash('error')): ?>

<div class="alert alert-danger">
<?= Yii::$app->session->getFlash('error') ?>
</div>

<?php endif; ?>

<label>Email / Username</label>
            <?php $form = ActiveForm::begin(['id' => 'login-form' ,
                'encodeErrorSummary' => false,
                'errorSummaryCssClass' => 'locked-block',]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => "Email / Username"])->label(false); ?>

                <?= $form->field($model, 'password')->passwordInput(['placeholder' => "Password"])->label(false); ?>

                 <div class="form-group" style="margin: 0; padding-bottom: 15px;">
                    <div class="forgot-password">
                        <a href="/frontend/web/site/request-password-reset">Forgot or change password</a>

                        <?php //echo $this->getBaseUrl(); ?>
                    </div>
                 </div>

                <div class="form-group">
                    <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

                 <div style="text-align: center;">
           <p style="color:#5e5e5e;font-weight: bold;"> Or </p>
            
            <a class="login_ms btn" style="color:#fff" href="<?= $authUrl ?>">Login Via Microsoft</a>
        </div>


            <?php if($model->hasErrors('rememberMe')) : ?>
                <?= $form->errorSummary($model) ?>
            <?php endif?>

            <p class="ed_white">Powered by EventDraw</p>

          


            <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

   
<style type="text/css">

    .momentus_logo { width:350px; max-width:100%; }
    
.site-login {
    margin: 0 auto;
}

body {
    background: #a8518a;
}

button.btn.btn-primary {
    width: 100%;
    background-color: #24312a;
        font-size: 18px;
}
.forgot-password {
    text-align: right;
}

.forgot-password a {
 color: #5e5e5e;
    text-decoration: none;
    font-size: 16px;
}

.eventdraw-link {
    text-align: center;
}

.eventdraw-link a {
    color: #fff;
    text-decoration: none;
}

form#login-form {
    color: #fff;
}

nav.navbar-inverse.navbar-fixed-top.navbar { display: none; }
.mb-30 { margin-bottom: 30px; }

.checkbox label { color: #fff!important; }

p.help-block.help-block-error {
    color: #fff;
    background: red;
    padding: 0 5px;
    border-radius: 3px;
}
p.help-block.help-block-error:empty {display:none;}

.locked-block{
        color: black;
        background: #c6d8f0;
        padding: 0 5px;
        border-radius: 3px;
    }
    

    div#login_box {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    margin-top: 20px;
}


.eventdraw-link h4 {
    color: #fff;
    font-size: 3rem;
}

.eventdraw-link p {
    font-size: 2rem;
    color: #fff;
}

p.ed_dark {
    font-size: 1.8rem;
    color: #fff;
    margin-top: 15px;
}

p.ed_white {
    font-size: 1.8rem;
    color: #5e5e5e;
    padding: 10px;
    text-align: center;
    margin: 0;
}

label {
    text-align: center;
    display: block;
    font-size: 2rem;
    color: #5e5e5e;
}

button.btn.btn-primary, .login_ms {
    width: 100%;
    background-color: #24312a;
    font-size: 18px;
}
</style>
