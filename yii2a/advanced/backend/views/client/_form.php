<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $modelUser common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'clientName')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'clientEmail')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
         <div class="col-sm-2">
            <?= $form->field($model, 'clientPayment')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'ShowMaxCapPlans')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ])?>
        </div>

    </div>

    <div class="row">

        <div class="col-sm-2">
          <?= $form->field($model, 'status')->dropDownList([
          '10' => 'Full Version',
          '9' => 'Trial',
         '11' => 'Custom Trial',
           ])?>
        </div>
        <div class="col-sm-2">
             <?= $form->field($model, 'expiry_date')->widget(\yii\jui\DatePicker::class, [
           // if you are using bootstrap, the following line will set the correct style of the input field
           'options' => ['class' => 'form-control'],
          // ... you can configure more DatePicker properties here
    ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-user"></i> User list
            </h4>
            <p>
                <?= Html::a('Create User', ['/user/create'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <!--                users header-->
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-2">
                            <b>First Name</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Surname</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Email</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Login</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Company Admin</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Status</b>
                        </div>
                    </div>
                </div>
                <!-- show list of users-->
                <?php foreach ($modelUser as $i => $modelUsers): ?>

                    <div class="row">
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->firstname) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->surname) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->email) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->username) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode( $modelUsers->company_admin     == 1 ? 'Yes' : 'No') ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->getStatusName()) ?>
                        </div>
                    </div><!-- .row -->

                <?php endforeach; ?>
            </div>
        </div>
    </div>



    <?= Html::a('Choose Templates', ['template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>

</div>
