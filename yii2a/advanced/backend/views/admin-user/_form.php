<?php

use common\models\Client;
use common\models\Stencil;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clientId int */
/* @var $editIndex int */
/* @var $model common\models\User */
/* @var $modelTemplate common\models\UserTemplates */
/* @var $modelStencil common\models\UserStencils */
/* @var $modelSettings common\models\Usersettings */
/* @var $form yii\widgets\ActiveForm */

$userFormId = $model->isNewRecord ? 'new-user' : 'old-user';
?>

    <div class="user-form">
        <?php if (Yii::$app->controller->id === 'user'):
            $form = ActiveForm::begin();
        else:
            $form = ActiveForm::begin(['action' => ['user/ajax-save'], 'options' => ['class' => 'user-save-form', 'id' => $userFormId]]);
        endif; ?>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form
                    ->field($model, 'username')
                    ->textInput(['maxlength' => true, 'class' => 'form-control user-login', 'readonly' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class' => 'form-control user-email']) ?>
            </div>
        </div>

        <?php if (!empty($clientId)) : ?>
            <?= $form->field($model, 'clientid')->hiddenInput(['value' => $clientId])->label(false) ?>
        <?php else: ?>
            <?= $form->field($model, 'clientid')
                ->dropDownList(
                    ArrayHelper::map(
                        Client::find()->select(['id', 'clientName'])->orderBy(['clientName' => SORT_ASC])->all(),
                        'id',
                        'clientName'
                    ),
                    ['prompt' => '-- Select client --']
                );
            ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList(['10' => 'Full Version', '9' => 'Trial', '11' => 'Custom Trial', '0' => 'No Access', '12' => 'Churn']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'expiry_date')
                    ->widget('\yii\jui\DatePicker', ['options' => ['class' => 'form-control']]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'userStencilid')
                    ->dropDownList(
                        ArrayHelper::map(
                            Stencil::find()->select(['id', 'stencilName'])->orderBy(['stencilName' => SORT_ASC])->all(),
                            'id',
                            'stencilName'
                        ),
                        ['prompt' => '-- Select stencil --']
                    );
                ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'maxSession')
                    ->textInput(['type' => 'number', 'value' => $model->isNewRecord ? 1 : $model->maxSession]) ?>
            </div>
        </div>

         <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'userType')->dropDownList(['0'=>'Not Set','1' => 'Venue', '2' => 'Event Organiser']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'UserCompanyName')->textInput(['maxlength' => true]) ?>
            </div>

                <div class="col-md-3">
                <?= $form->field($model, 'UserCountry')
                    ->dropDownList(
                        ArrayHelper::map(
                            \common\models\Country::find()->select(['COUNTRY_ISO3', 'NAME'])->orderBy(['COUNTRY_ORDER' => SORT_ASC, 'NAME' => SORT_ASC])->all(),
                            'COUNTRY_ISO3',
                            'NAME'
                        ),
                        ['prompt' => '-- Select country --']
                    );
                ?>
            </div>

        </div>


        <div class="row">
            <div class="col-md-2">
                <?= $form->field($model, 'userPayment')
                    ->checkbox([
                        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                    ])
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'userIsAdmin')
                    ->checkbox([
                        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                    ])
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'userIsSupport')
                    ->checkbox([
                        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                    ])
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'company_admin')
                    ->checkbox([
                        'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
                    ])
                ?>
            </div>

            <div class="col-sm-3">  
            <?= $form->field($model, 'AllowSaveCloud')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>

        </div>

        

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php if (Yii::$app->controller->id === 'user'): ?>
    <!-- Settings -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><i class="glyphicon glyphicon-cog"></i> Settings</h4>
            <?= Html::a('Change Settings', ['user/settings', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>

        <div class="panel-body">
            <div class="container-items">
                <?php if ($modelSettings) : ?>
                    <div class="row">
                        <?= Html::encode('Measurement Units:  ' . $modelSettings->getMeasurementName($modelSettings->meas_unit)) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-book"></i> Template list
                (<?= Html::encode(count($modelTemplate)) ?>)
            </h4>
            <?= Html::a('Choose Templates', ['user/template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="panel-body">
            <div class="container-items">
                <?php foreach ($modelTemplate as $i => $modelTemplates): ?>
                    <div class="row">
                        <?= Html::encode($modelTemplates->getTemplateName()) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-th"></i> Stencil list
                (<?= Html::encode(count($modelStencil)) ?>)
            </h4>
            <?= Html::a('Choose Stencils', ['user/stencil', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="panel-body">
            <div class="container-items">
                <?php foreach ($modelStencil as $i => $modelStencils): ?>
                    <div class="row">
                        <?= Html::encode($modelStencils->getStencilName()) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (Yii::$app->controller->id != 'user'): ?>
    <script>
        $(document).on('submit', ".user-save-form", function (e) {
            const data = $(this).serializeArray();
            const url = $(this).attr('action');

            saveClientUser(data, url);

            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });

        $(".user-save-form").submit(function (e) {
            const data = $(this).serializeArray();
            const url = $(this).attr('action');

            saveClientUser(data, url);

            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });

        function saveClientUser (data, url) {
            $.ajax({
                async: false,
                url: url,
                type: 'post',
                data: data,
                success: function (response) {
                    alert(response.data.message);
                    $("#new-user").trigger('reset');
                    $('#save-user-modal').modal('hide');
                    $('.edit-user-modal').modal('hide');
                    $('#client-users').load(" #client-users > *");
                },
                error: function () {
                    console.log("error");
                }
            });
        }
    </script>
<?php endif; ?>

<script>
    $(".user-email").change(function() {
        $('.user-login').val($(this).val());
    });

    $(".user-login").keypress(function() {
        $('#user-email').val($(this).val());
    });
</script>
