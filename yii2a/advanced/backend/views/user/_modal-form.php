<?php

use kartik\password\PasswordInput;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $editIndex int */
/* @var $clientModel common\models\Client */
/* @var $model common\models\User */
/* @var $modelTemplate common\models\UserTemplates */
/* @var $modelStencil common\models\UserStencils */
/* @var $modelSettings common\models\Usersettings */
/* @var $form yii\widgets\ActiveForm */

$userFormId = $model->isNewRecord ? 'new-user' : 'old-user';
$statues = [
    '11' => 'Custom Trial',
    '10' => 'Full Version',
    '9' => 'Trial',
];
?>

<div class="user-form">
    <?php $form = ActiveForm::begin([
        'action' => ['user/ajax-save'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'options' => ['class' => 'user-save-form', 'id' => $userFormId]
    ]); ?>

    <?= $form->field($model, 'clientid')->hiddenInput(['value' => $clientModel->id])->label(false) ?>
    <?= $form->field($model, 'id')->hiddenInput(['value' => $model->id])->label(false) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'email')
                ->textInput([
                    'maxlength' => true,
                    'id' => 'user-email' . $model->id,
                ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form
                ->field($model, 'status')
                ->textInput(['value' => $statues[$clientModel->status], 'readonly' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'expiry_date')
                ->textInput(['value' => date('M d, Y', $clientModel->expiry_date), 'readonly' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'maxSession')
                ->textInput(['type' => 'number', 'value' => $model->isNewRecord ? 1 : $model->maxSession]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'userIsSupport')
                ->checkbox([
                    'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                ])
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'company_admin')
                ->checkbox([
                    'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                ])
            ?>
        </div>
    </div>

    <div class="row">
        <?php if (!$model->isNewRecord) : ?>
            <div class="col-md-2"></div>
            <div class="col-md-3">
                <?= $form
                    ->field($model, 'username')
                    ->textInput([
                        'maxlength' => true,
                        'id' => 'user-login' . $model->id,
                        'class' => 'form-control',
                        'readonly' => true
                    ]) ?>
            </div>
            <div class="col-md-3">
                <?= $form
                    ->field($model, 'password_hash')
                    ->widget(PasswordInput::classname(), [
                        'name' => 'user-password' . $model->id,
                        'options' => [
                            'value' => '',
                            'id' => 'user-password' . $model->id,
                            'class' => 'form-control',
                            'maxlength' => true,
                        ],
                        'pluginOptions' => [
                            'showMeter' => false,
                            'toggleMask' => true
                        ]
                    ]); ?>
            </div>
            <div class="col-md-2">
                <a href="#" id="random-generator<?= $model->id ?>">
                    <i title="Generate Random Password" class="login-icon glyphicon glyphicon-refresh"></i>
                </a>
                <a href="#" onclick="copyToClipboard('#user-password<?= $model->id ?>')">
                    <i title="Copy to Clipboard" class="login-icon glyphicon glyphicon-copy"></i>
                </a>
            </div>
            <div class="col-md-2"></div>
    <?php else : ?>
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <?= $form
                    ->field($model, 'username')
                    ->textInput([
                        'maxlength' => true,
                        'id' => 'user-login' . $model->id,
                        'class' => 'form-control',
                        'readonly' => true
                    ]) ?>
            </div>
            <div class="col-md-4"></div>
    <?php endif; ?>
    </div>

    <div class="form-group">
        <button class="btn btn-success" type="submit">Save</button>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('submit', ".user-save-form", function (e) {
        const data = $(this).serializeArray();
        const url = $(this).attr('action');
        var yiiform = $(this);

        saveClientUser(data, url, yiiform);

        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    });

    $(".user-save-form").submit(function (e) {
        const data = $(this).serializeArray();
        const url = $(this).attr('action');
        var yiiform = $(this);

        saveClientUser(data, url, yiiform);

        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    });

    function saveClientUser(data, url, yiiform) {
        $.ajax({
            async: false,
            url: url,
            type: 'post',
            data: data,
            success: function (response) {
                if (!response.code) {
                    alert(response.data.message);

                    $("#new-user").trigger('reset');
                    $('#save-user-modal').modal('hide');
                    $('.edit-user-modal').modal('hide');
                    $('#client-users').load(" #client-users > *");
                }
            },
            error: function () {
                console.log("error");
            }
        });
    };

    $(document).on('change', "#user-email<?= $model->id?>", function () {
        $('#user-login<?= $model->id?>').val($(this).val());

        return false;
    });

    $(document).on('click', "#random-generator<?= $model->id?>", function () {
        $('#user-password<?= $model->id?>').val(randomPassword(16));

        return false;
    });

    function copyToClipboard(element) {
        const $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).val()).select();
        document.execCommand("copy");
        $temp.remove();
    }

    function randomPassword(length) {
        const chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>1234567890ABCDEFGHIJKLMNOP1234567890";
        let pass = "";
        for (let x = 0; x < length; x++) {
            let i = Math.floor(Math.random() * chars.length);
            pass += chars.charAt(i);
        }

        return pass;
    }
</script>
