    <?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $modelUsers common\models\User */
/* @var $modelTemplate common\models\ClientTemplates */
/* @var $modelStencil common\models\ClientStencils */
/* @var $user common\models\User */
/* @var $userTemplate common\models\UserTemplates */
/* @var $userStencil common\models\UserStencils */
/* @var $userSettings common\models\Usersettings */
/* @var $form yii\widgets\ActiveForm */

$xmlPath = '../../frontend/web/site/stencils_favourite/' . $model['id'] . '_Favourites.xml';
$expiryDate = $model->isNewRecord ? date('M d, yy', strtotime('+30 days')) : $model->expiry_date;
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'clientName')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-4">
                <?= $form->field($model, 'clientCountry')
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
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'clientPayment')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>

         <div class="col-sm-2">
            <?= $form->field($model, 'Allow3D')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ])?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'ShowMaxCapPlans')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'AllowImportPdf')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ])?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'AllowSaveCloud')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'AllowFavouriteStencils')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
            <?php if (file_exists($xmlPath)) {
                echo Html::a($model['id'] . '_Favourites.xml', ['download', 'id' => $model->id]);
            } ?>

        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'AllowShare')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>
        
        <div class="col-sm-2">
            <?= $form->field($model, 'AllowMomentus')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ]) ?>
        </div>
        
        <div class="col-sm-2">
            <?= $form->field($model, 'AllowSaveFolder')->checkbox([
                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>'
            ])?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="help-block" style="margin-bottom: 10px;">
                Momentus Enterprise (Connect API): use <strong>API Base URL</strong>, <strong>API Token</strong>, and <strong>Subscription Key</strong> for each client&rsquo;s TEST database.
                Set <strong>Organisation Code</strong> to the client&rsquo;s Momentus org. Auth URL is only used for legacy Momentus Elite OAuth.
            </p>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'MomentusAPIUrl')->textInput(['maxlength' => true, 'placeholder' => 'https://api-sandbox.gomomentus.com/enterprise/connect/api']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'MomentusAPIKey')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'MomentusSecretKey')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'momentusOrgCode')->textInput(['maxlength' => true]) ?>
        </div>

    </div>

    <div class="row">

        <div class="col-sm-6">
            <?= $form->field($model, 'MomentusAPIUrlAuth')->textInput(['maxlength' => true]) ?>
        </div>

    </div>



    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList([
                '11' => 'Custom Trial',
                '10' => 'Full Version',
                '9' => 'Trial',
                '0' => 'No Access',
                '12' => 'Churn',
            ]) ?>
        </div>
         <div class="col-md-3">
            <?= $form->field($model, 'clientType')->dropDownList([
                '1' => 'Venue',
                '2' => 'Event Organiser',
                '0' => 'Not Set',
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'siDate')->dropDownList([
                '0' => 'Not Set',
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ]) ?>
        </div>
        <div class="col-md-3">
            <label class="control-label" for="expiry_date">Expiry Date</label>
            <?= DatePicker::widget([
                'model' => $model,
                'name'  => 'expiry_date',
                'value'  => $expiryDate,
                'dateFormat' => 'php:M d, yy',
                'options' => ['class' => 'form-control'],
            ]); ?>
            <?php /* $form->field($model, 'expiry_date')
            ->widget('\yii\jui\DatePicker', [
            'options' => ['class' => 'form-control'],
            'htmOptions' => ['defaultDate' => date('M d, Y', strtotime('+30 days'))],
            'clientOptions' => ['defaultDate' => date('M d, Y', strtotime('+30 days'))],
            ]) */ ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'ClientVenueType')
                ->dropDownList(
                    ArrayHelper::map(
                        \common\models\VenueType::find()->select(['id', 'venueTypeName'])->orderBy(['venueTypeName' => SORT_ASC])->all(),
                        'id',
                        'venueTypeName'
                    ),
                    ['prompt' => '-- Select Venue Type --']
                );
            ?>
        </div>
        <div class="col-sm-9">
            <?= $form->field($model, 'clientNotes')->textInput(['maxlength' => true]) ?>
        </div>
    </div>    

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if (!$model->isNewRecord) : ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="glyphicon glyphicon-user"></i> Users list</h4>
                <div class="display-flex-div">
                    <div class="width-100-percent">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#save-user-modal">
                            Create User
                        </button>
                    </div>
                    <button type="button" class="float-right-element btn btn-primary" id="email-select-users">
                        Send Logins
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="container-items">
                    <div class="table-client-users-padding row">
                        <div class="col-sm-2">
                            <b>Name</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Email / Login</b>
                        </div>
                        <div class="col-sm-1">
                            <b>Created</b>
                        </div>
                        <div class="col-sm-1">
                            <b>Last Login</b>
                        </div>
                        <div class="col-sm-1">
                            <b>Total Sessions</b>
                        </div>
                        <div class="col-sm-1">
                            <b>Company Admin</b>
                        </div>
                        <div class="col-sm-1">
                            <b>Status</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Actions</b>
                        </div>
                        <div class="col-sm-1">
                            <input id="check-all-users" type="checkbox" class="checkbox">
                        </div>
                    </div>
                    <div id="client-users">
                        <?php foreach ($modelUsers as $i => $modelUser): ?>
                            <div class="row" style="padding-top: 1%">
                                <div class="col-sm-1">
                                    <?= Html::encode($modelUser->firstname) . ' ' . Html::encode($user->surname) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= Html::encode($modelUser->email) ?>
                                </div>
                                <div class="col-sm-1">
                                    <?= Yii::$app->formatter->asDate($modelUser->created_at, 'dd/MM/Y'); ?>
                                    
                                </div>
                                <div class="col-sm-1">
                                    <?= Yii::$app->formatter->asDate($modelUser->last_login, 'dd/MM/Y'); ?>
                                   
                                </div>
                                <div class="col-sm-1">
                                    <?= Html::encode($modelUser->totSession) ?>
                                </div>
                                <div class="col-sm-1">
                                    <?= Html::encode($modelUser->company_admin == 1 ? 'Yes' : 'No') ?>
                                </div>
                                <div class="col-sm-1">
                                    <?= Html::encode($modelUser->getStatusName()) ?>
                                </div>
                                <div class="col-sm-2">
                                    <!-- 
                                    <button
                                        type="button"
                                        title="Edit"
                                        class="btn btn-success"
                                        data-toggle="modal"
                                        data-target="#edit-user-modal<?= $i ?>">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </button>
                                     -->
                                     <?= Html::a('Edit', ['user/update?id=' . $modelUser->id], ['class'=>'btn btn-success']) ?>
                                    <button
                                        type="button"
                                        title="Delete"
                                        class="btn btn-danger delete-client-user"
                                        id="<?= $modelUser->id ?>">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </button>
                                </div>
                                <div class="col-sm-1">
                                    <input id="<?= $modelUser->id ?>" type="checkbox" class="user-checkbox checkbox">
                                </div>
                            </div>
                                    <!-- Modal -->
                            
                            
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>



<div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-th"></i> Stencil list
           
                        (<?= Html::encode(count($modelStencil)) ?>)
                

            </h4>
            <?= Html::a('Choose Stencils', ['client/stencil', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
    
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="glyphicon glyphicon-book"></i> Template list
                (<?= Html::encode(count($modelTemplate)) ?>)
                </h4>
                <?= Html::a('Choose Templates', ['template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="panel-body">
                <div class="container-items">
                    <?php foreach ($modelTemplate as $i => $modelTemplates): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?= Html::encode($modelTemplates->getTemplateName()) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div id="save-user-modal" class="modal fade" role="dialog">
            <div class="modal-dialog user-modal-width">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create User</h4>
                    </div>
                    <div class="modal-body">
                        <?= $this->render('/user/_modal-form', [
                            'clientModel' => $model,
                            'model' => $user,
                            'modelTemplate' => $userTemplate,
                            'modelStencil' => $userStencil,
                            'modelSettings' => $userSettings,
                        ]) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (Yii::$app->controller->id != 'user'): ?>
    <script>
        $(document).on('click', ".delete-client-user", function (e) {
            const confirmation = confirm("Are you sure?");

            if (confirmation) {
                const data = {id: $(this).attr('id')};

                $.ajax({
                    async: false,
                    url: '<?= Url::to(['user/ajax-delete']) ?>',
                    type: 'post',
                    data: data,
                    success: function (response) {
                        alert(response.data.message);
                        $('#client-users').load(" #client-users > *");
                    },
                    error: function () {
                        console.log("error");
                    }
                });

                return false;
            }
        });

        $("#check-all-users").click(function(){
            $('.user-checkbox').not(this).prop('checked', this.checked);
        });

        $("#email-select-users").click(function(){
            const confirmation = confirm("Are you sure you want to email the credentials to the selected users?");
            let userIds = [];

            if (confirmation) {
                $(".user-checkbox").each(function() {
                    let $this = $(this);

                    if ($this.is(":checked")) {
                        userIds.push($this.attr("id"));
                    }
                });

                $.ajax({
                    async: false,
                    url: '<?= Url::to(['user/ajax-send-credentials']) ?>',
                    type: 'post',
                    data: { userIds },
                    success: function (response) {
                        alert(response);
                    },
                    error: function () {
                        console.log("error");
                    }
                });

                return false;
            }
        });
    </script>
<?php endif; ?>
