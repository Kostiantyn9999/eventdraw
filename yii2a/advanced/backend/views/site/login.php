<?php



/* @var $this yii\web\View */

/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginFormAdmin */



use yii\helpers\Html;

use yii\bootstrap\ActiveForm;



$this->title = 'Login';

//$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-login test1">

    <!--h1><?= Html::encode($this->title) ?></h1>



    <p>Please fill out the following fields to <b>Admin/support </b>login:</p-->



    <div class="row">

        <div class="col-lg-12">



            <div class="text-center mb-30">

               <img src="<?= Yii::$app->urlManager->baseUrl ?>/images/login_logo.png" alt="Event Draw">

            </div>

            <div class="eventdraw-link">
              <a href="https://www.eventdraw.com/">www.eventdraw.com</a>
            </div>




            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>



                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => "Email / Username"])->label(false); ?>



                <?= $form->field($model, 'password')->passwordInput(['placeholder' => "Password"])->label(false); ?>



                 <div class="form-group">

                    <div class="forgot-password">

                        <a href="request-password-reset">Forgot password</a>



                        <?php //echo $this->getBaseUrl(); ?>

                    </div>

                 </div>

                 

                <?= $form->field($model, 'rememberMe')->checkbox() ?>



                <div class="form-group">

                    <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>

                </div>



            <?php ActiveForm::end(); ?>

        </div>

    </div>

</div>

<style type="text/css">



.eventdraw-link {
    text-align: center;
}
    .eventdraw-link a {
    color: #fff;
    text-decoration: none;
}

    .site-login {

    max-width: 300px;

    margin: 0 auto;

}



body {

    background: #a8518a;

}



button.btn.btn-primary {

    width: 100%;

    background-color: #24312a;

}

.forgot-password {

    text-align: right;

}



.forgot-password a {

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

</style>

