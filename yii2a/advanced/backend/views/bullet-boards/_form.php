<?php

use dosamigos\tinymce\TinyMce;
use kartik\editors\Summernote;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Client;
/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */
/* @var $form yii\widgets\ActiveForm */
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>
<div class="new-user-broadcast-email-templates-form">
    <button id="filters" class="accordion active-accordion">Filters <i class="glyphicon glyphicon-expand"></i></button>
    <div class="panel-accordion" style="display: none">
        <div class="row">
            <div class="col-md-6 form-group">
                <select class="form-control" name="userType" id="userType">
                    <option value="">Type</option>
                    <option value="4">All</option>
                    <option value="1">Venue Owner</option>
                    <option value="2">Event Organizer</option>
                    <option value="0">Not Set</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <select class="form-control" name="status" id="status">
                    <option value="">Status</option>
                    <option value="10">Full Version</option>
                    <option value="9">Trial</option>
                    <option value="11">Custom Trial</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="username">Search By Client Name <small>(name must be in comma separated)</small></label>
                <input id="username" name="username" class="form-control" id="tag_it" type="text" placeholder="Enter Client Full Name"/>
            </div>
            <div class="col-md-6 form-group">
                <label for="clientEmail">Search By Client Email <small>(name must be in comma separated)</small></label>
                <input id="clientEmail" name="clientEmail" class="form-control" id="tag_it" type="text" placeholder="Enter Client Email"/>
            </div>
            <div class="col-md-6 form-group">
                <label>Last Login</label>
                <select class="form-control" name="last_login" id="last_login">
                    <option value="">Choose One</option>
                    <option value="0">Last Week</option>
                    <option value="1">Within 30 Days</option>
                    <option value="2">Over 30 Days</option>
                    <option value="3">Not Set</option>
                </select>
            </div>
           <!--  <div class="col-md-6 form-group">
                <label>Last Bulletin Date</label>
                <select class="form-control" name="last_bulletin_date" id="last_bulletin_date">
                    <option value="">Choose One</option>
                    <option value="0">Last Week</option>
                    <option value="1">Within 30 Days</option>
                    <option value="2">Over 30 Days</option>
                    <option value="3">Not Set</option>
                </select>
            </div> -->
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="is-subscribed">Is Subscribed?</label>
                    <input id="is-subscribed" name="isSubscribed" type="checkbox" checked disabled />
                </div>
            </div>
        </div>
        <hr/>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'heading')->textInput(['maxlength' => true,'id'=>'heading']) ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'template_id')->dropDownList(
                \yii\helpers\ArrayHelper::merge(['' => 'Select a template'], \yii\helpers\ArrayHelper::map($tempData, 'id', 'template_name'))
            ) ?>

        </div>
    </div>
    <div id="client-users">
        <?php 
        if (!empty($selectedUsers)) { ?>
        <h5>Selected Users <b>(<?= count($selectedUsers) ?>)</b>
        <?php foreach ($selectedUsers as $obj) { ?>
        <div style='margin-bottom: 2px'>
            <?php if(!empty($obj['email'])){?>
            <input type='checkbox' checked='checked' value='<?= $obj['email'] ?>' name='user_id[]' class='user-checkbox'/>
                <?= $obj['email'];?>
            <?php } ?>
            <?php if(empty($obj['email'])){
                $client = Client::findOne($obj['id']);
                ?>
            <input type='checkbox' checked='checked' value='<?= $client->clientEmail;;?>' name='user_id[]' class='user-checkbox'/>
                <?= $client->clientEmail;;?>
            <?php } ?>
        </div>
        <br/>
        <?php } ?>
        <?php } ?>


    </div>

    <?= $form->field($model, 'template_image')->textarea(['maxlength' => true,'id'=>'email-paragraph','class'=>'summernote-editor form-control']) ?>
    <?= $form->field($model, 'link_heading')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'button_link')->textInput(['maxlength' => true]) ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'no_of_time_bulletin_show')->dropDownList(['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], ['options' => ['2' => ['selected' => true]]]) ?>
        </div>
    </div>

    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button id="preview-email" type="button" class="btn btn-success">Preview Email</button>
        <div class="modal fade" id="previewTemplate1Modal" tabindex="-1" role="dialog" aria-labelledby="previewTemplate1Modal" aria-hidden="true">
            <?= $this->render('_preview-template1-modal') ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>

    $('.summernote-editor').summernote({
        height:400
    })
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel */
            this.classList.toggle("active-accordion");

            /* Toggle between hiding and showing the active panel */
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
    $(document).on('click', '#preview-email', function() {
        var body = $('.summernote-editor').val();

        document.getElementById('email-template-body').innerHTML = body;

        $('#previewTemplate1Modal').modal('toggle');
    });
    $(document).on('change','#newuserbroadcastemailtemplates-template_id',function(){
        var email_template_id   =   $(this).val();
        console.log(email_template_id);
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-clients/get-template-data']) ?>',
            type: 'post',
            data: { email_template_id:email_template_id},
            success: function (response) {
                console.log(response);
                var get_res     = JSON.parse(response);
                if(get_res.status=='true'){
                    //tinymce.activeEditor.setContent(get_res.content);
                    $('#email-paragraph').summernote('code',get_res.content);
                    $('#heading').val(get_res.bulletin_heading);
                }
            }

        });
    })
</script>
<script>

    
    let data = [];

    $(document).ready(function() {
        shiftKeySelection(false);
    });

    function shiftKeySelection (changeSession) {
        let $checkboxes = $('.user-checkbox');
        let lastChecked = null;
        console.log($checkboxes);

        $checkboxes.click(function(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }

            if (e.shiftKey) {
                let start = $checkboxes.index(this);
                let end = $checkboxes.index(lastChecked);

                $checkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', lastChecked.checked);


                let emails=[];

                $checkboxes.each(
                    (key, checkbox) => {
                        if (checkbox.checked === true) {
                            emails.push(checkbox.value);
                        }
                    }
                )

                //console.log(emails)

                $.ajax({
                    async: false,
                    url: '<?= Url::to(['email-clients/shift-select']) ?>',
                    type: 'post',
                    data: {emails: emails, isChecked: $(this).prop("checked") === true},
                    success: function (response) {
                        let obj1 = JSON.parse(response);
                        console.log(obj1);
                        $('#total-selected-users').empty().append('(' + obj1.totalSelectedUsers + ')');
                        $('#check-all-users').removeAttr("checked");
                        data.selectAll = 0;
                    }
                });
            }
            lastChecked = this;
        });

    }

    $(document).on('change', "#userType", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#status').val('');
        $('#last_email_date').val('');
        $('#totSession').val('');*/
        data = {filter: $(this).val(), type: 'userType'};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#status", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#userType').val('');
        $('#last_email_date').val('');
        $('#last_login').val('');
        $('#totSession').val('');*/
        data = {filter: $(this).val(), type: 'status'};

        ajaxEligibleUsers(data);

        return false;
    });

    $(document).on('change', "#last_login", function (e) {
        data = {filter: $(this).val(), type: 'last_login'};
        ajaxEligibleUsers(data);
        return false;
    });

    $(document).on('change', "#username", function (e) {
        
        data = {filter: $(this).val(), type: 'username'};

        ajaxEligibleUsers(data);

        return false;
    });

    $(document).on('change', "#clientEmail", function (e) {
        
        data = {filter: $(this).val(), type: 'clientEmail'};

        ajaxEligibleUsers(data);

        return false;
    });

    function ajaxEligibleUsers(postData) {
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-clients/eligible-email-clients']) ?>',
            type: 'post',
            data: postData,
            success: function (response) {
                let obj = JSON.parse(response);
                let isChecked = false;
                $('#client-users').empty().append('<h5>Selected Users <b id="total-selected-users">('+ obj.totalSelectedUsers +')</b></h5>');
                $('#total-selected-users').append('<span style="margin-left: 10px"><a href=" <?= Url::to(['email-users/export']) ?>" class="btn btn-warning">CSV Export</a></span>');

                if (data.selectAll == 1) {
                    $('#client-users').append("<div style='margin-bottom: 2px'><input id='check-all-users' checked='checked' type='checkbox'/> Select All</div><br/>")
                } else {
                    $('#client-users').append("<div style='margin-bottom: 2px'><input id='check-all-users' type='checkbox'/> Select All</div><br/>")
                }
                obj.users.forEach(email => {
                    isChecked = false;
                    obj.selectedUsers.forEach(user => {
                        if (user.email === email && user.isChecked) {
                            isChecked = true;
                        }
                    });

                    if (isChecked) {
                        $('#client-users').append("<div style='margin-bottom: 2px'><input type='checkbox' checked='checked' value='" + email + "' name='user_id[]' class='user-checkbox'/> " + email + "</div><br/>");
                    } else {
                        $('#client-users').append("<div style='margin-bottom: 2px'><input type='checkbox' value='" + email + "' name='user_id[]' class='user-checkbox'/> " + email + "</div><br/>");
                        $('#check-all-users').removeAttr("checked");
                    }
                });

                // if (obj.totalPages > 0) {
                //     let objData = obj.data;
                //     objData.selectAll = data.selectAll;
                //     objData.isSelectAllClicked = 0;
                //     for (let i = 1; i <= obj.totalPages; i++) {
                //         objData.page = i;
                //         $('#client-users').append("<a class='btn btn-primary' style='margin-right: 10px' onclick='paginate("+ JSON.stringify(objData) +")'>"+ i +"</a>");
                //     }
                // }

                shiftKeySelection(true);
            },
            error: function () {
                console.log("error");
            }
        });
    }
    $(document).on('change','#email_template_id',function(){
        var email_template_id   =   $(this).val();
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-clients/get-template-data']) ?>',
            type: 'post',
            data: { email_template_id:email_template_id},
            success: function (response) {
                console.log(response);
                var get_res     = JSON.parse(response);
                if(get_res.status=='true'){
                    tinymce.activeEditor.setContent(get_res.content);
                }
            }

        });
    })
    $(document).on('change', ".user-checkbox", function () {
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-clients/update-session']) ?>',
            type: 'post',
            data: { email: $(this).val(), isChecked: $(this).prop("checked") === true },
            success: function (response) {
                let obj1 = JSON.parse(response);
                $('#total-selected-users').empty().append('(' + obj1.totalSelectedUsers + ')');
                $('#check-all-users').removeAttr("checked");
                data.selectAll = 0;
            }

        });
    });

    $(document).on('click', "#email-csv", function () {
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-clients/export']) ?>',
            type: 'post',
            data: { email: $(this).val() },
            success: function () {

                 }

        });
    });

    function paginate(objData) {
        ajaxEligibleUsers(objData);
    }

    $(document).on('change', '#check-all-users', function() {
        if ($(this).is(':checked')) {
            $('.user-checkbox:checkbox').prop('checked', true);

            data.selectAll = 1;
            data.isSelectAllClicked = 1;
            ajaxEligibleUsers(data)
        }
        else
        {
            $('.user-checkbox:checkbox').prop('checked', false);

            data.selectAll = 0;
            data.isSelectAllClicked = 1;
            ajaxEligibleUsers(data)

        }
    });

    $(document).on('change', '.user-checkbox', function() {
        if ($(this).is(':checked')) {
            if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
                $('#check-all-users').prop('checked', true);
            } else {
                $('#check-all-users').prop('checked', false);
            }
        } else {
            $('#check-all-users').prop('checked', false);
        }
    });

    $("#add-body").click(function () {
        let num = $("#body > div").length + 1;
        $('#body').append('<div class="col-md-12">\n' +
            '                        <label for="body' + num + '">Body Paragraph # ' + num + '</label>\n' +
            '                        <textarea id="body' + num + '" name="body[]" rows="5" class="form-control email-paragraph" required></textarea>\n' +
            '                    </div>');
    });
    $("#remove-body").click(function () {
        if ($("#body > div").length !== 1) {
            $('#body').children().last().remove();
        }
    });

    $("#bulk-mail-out-form").submit(function() {
        return  confirm("Are you sure you want to send the email to all the selected users?");
    });


    $(document).on('change', ".email-heading", function (e) {
        $('#email-template-heading').text($('.email-heading').val());
    });

    $(document).on('click', '#preview-email', function() {
        var body = tinymce.get("email-paragraph").getContent();

        document.getElementById('email-template-body').innerHTML = body;

        $('#previewTemplate1Modal').modal('toggle');
    });

    //jQuery('#clientData_dataTable').DataTable();
</script>

<style type="text/css">
    .select2-container--default .select2-selection--multiple{width: 300px !important;}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
<link href="/backend/web/assets/tagit/css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="/backend/web/assets/tagit/css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
<script src="/backend/web/assets/tagit/js/tag-it.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    $('#tag_it').tagit();
</script>