<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sorry, this floor plan is restricted';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    

    <div class="row">
        <div class="col-lg-12">

            <div class="text-center mb-30">
            <a href="https://www.eventdraw.com/">
                <img alt="Event Draw" src="/backend/web/images/login_logo.png">
            </a>
            </div>

            <h1><?= Html::encode($this->title) ?></h1>

    
    

        </div>
    </div>
</div>
<style type="text/css">
    
    .site-request-password-reset {
    max-width: 600px;
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