<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        If you have business inquiries or other questions, please fill out the following form to contact us. Thank you. <br>
		<br>
While our team answer your enquiry...
    </p>
	<p><a class="form-group" href="http://www.eventdraw.com.au"> <p><u>Why Not Have a Play With Event Draw Now!</u></a></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'subject') ?>

                <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
	<div class="col-lg-4">
                
                <p><strong>Australia - 
Phone: + 61 2 9520-5137</strong><br>
<br>
<strong>United States - 
Phone:  + 647 470 2121</strong><br>
<br>
<strong>sales@eventdraw.com.au</strong>					
</p>

                <p><a class="btn btn-default" href="http://www.eventdraw.com.au/">More &raquo;</a></p>
            </div>
</div>
