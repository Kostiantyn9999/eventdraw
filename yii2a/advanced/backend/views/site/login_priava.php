<?php

/* @var $this yii\web\View */

/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginFormAdmin */



use yii\helpers\Html;

use yii\bootstrap\ActiveForm;

?>	
<div id="login_form">
	<div class="form_inner">

		<div class="title_sec">
				<img src="<?= Yii::$app->urlManager->baseUrl ?>/images/form-image-1.png" class="image-smile">
				<h1 class="priava-floor-plannin">priava floor planning</h1>
				<h4 class="please-log-in">Please log in</h4>
		</div>

		<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
				<div class="user_name_field">					
	    			<label for="uname" class="input_lable">Your Email or Username</label>
	    			<?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => "Email / Username"])->label(false); ?>
				</div>

				<div class="password_field">					
	    			<label for="psw" class="input_lable">Your Password</label>
	    			<?= $form->field($model, 'password')->passwordInput(['placeholder' => "Password"])->label(false); ?>
				</div>

				<div class="submit_field">
					<button type="submit" name="login-button"> <img src="<?= Yii::$app->urlManager->baseUrl ?>/images/login-arrow.png">Continue to login</button>
				</div>
		  <?php ActiveForm::end(); ?>

	<p class="having-problems-logg "><a href="request-password-reset">Having problems logging in?</a></p>
	</div>


<style type="text/css">
	body
{
background-image: url(<?= Yii::$app->urlManager->baseUrl ?>/images/login_bg_new.png);
background-size: contain;
background-repeat: no-repeat;
background-position: center center;
}

nav.navbar-inverse.navbar-fixed-top.navbar {
    display: none;
}

#login_form .form_inner {
    margin: 0 auto;
    max-width: 500px;
    background-color: #ffffff;
    box-shadow: 0 2px 20px 0 rgba(0,0,0,0.15);
}
#login_form .priava-floor-plannin {
  color: #333333;
      margin: 0;
  font-size: 28px;
  font-weight: 600;
  letter-spacing: 0;
  line-height: 45px;
  text-transform: capitalize;
}
#login_form .please-log-in {
  color: #999999;
      margin: 0;
  font-size: 25px;
  letter-spacing: 0;
  line-height: 25px;
    font-weight: 500;
}

#login_form .input_lable {
  color: #333333;
  font-size: 14px;
  font-weight: normal;
}
#login_form .submit_field button {
    display: block;
    border-radius: 49.38px;
    background-color: #ad76b3;
    height: 46px;
    color: #fff;
    font-size: 18px;
    letter-spacing: 0;
    padding: 10px 14px;
    border: 0;
    cursor: pointer;
}

#login_form .submit_field button:hover {
	opacity: 0.8;
}


#login_form .submit_field button img {
	vertical-align: middle;
	    margin-top: -5px;
    margin-right: 10px;
}
#login_form input {
    box-shadow: none !important;
    box-sizing: border-box;
  border: 1px solid #CBD6E2;
  border-radius: 3px;
  background-color: #F3F6F8;    
}
#login_form .having-problems-logg {
    color: #666666;
    font-size: 12px;
    letter-spacing: 0;
    line-height: 30px;
    font-family: 'Open Sans', sans-serif;
    background-color: #F2F2F5;
    padding: 15px 0 15px 40px;
    margin-top: 40px;
}
#login_form .having-problems-logg a
{
  color: #666666;
}
#login_form img.image-smile {
    width: 55px;
}

.title_sec {
    padding: 40px 40px 0;
}

form#login-form {
    padding: 20px 40px 0;
}

.submit_field {
    margin-top: 35px;
}
</style>