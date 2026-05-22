<?php

use common\models\Client;
use dosamigos\tinymce\TinyMce;
use kartik\editors\Summernote;
use yii\helpers\Url;

?>
<link href="/backend/web/assets/select2/select2.css" rel="stylesheet">
<script type="text/javascript" src="/backend/web/assets/select2/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>
<div class="bulk-mail-out-form" xmlns="http://www.w3.org/1999/html">
    <button id="filters" class="accordion active-accordion">Filters <i class="glyphicon glyphicon-expand"></i></button>
    <div class="panel-accordion" style="display: none">
        <div class="row">
            <div class="col-md-6 form-group">
                <select class="form-control" name="userType" id="userType">
                    <option value="">Type</option>
                    <option value="1">Venue Owner</option>
                    <option value="2">Event Organizer</option>
                    <option value="0">Not Set</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <select class="form-control" name="last_login" id="last_login">
                    <option value="">Last Login</option>
                    <option value="0">Within 3 Month</option>
                    <option value="1">3-6 Months</option>
                    <option value="2">6 Months or More</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <select class="form-control" name="status" id="status">
                    <option value="">Status</option>
                    <option value="10">Full Version</option>
                    <option value="9">Trial</option>
                    <option value="11">Custom Trial</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <select class="form-control" name="last_email_date" id="last_email_date">
                    <option value="">Last Email Date</option>
                    <option value="1">Within 5 Days</option>
                    <option value="1">More Than 5 Days</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <select class="form-control" name="totSession" id="totSession">
                    <option value="">Total Session</option>
                    <option value="0">0-2</option>
                    <option value="1">3-5</option>
                    <option value="2">5-10</option>
                    <option value="3">10+</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <select class="form-control" name="siDate" id="siDate">
                    <option value="">SI Date</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="username">Search By Username <small>(name must be in comma separated)</small></label>
                    <input id="username" name="username" class="form-control" id="tag_it" type="text" placeholder="Enter Username"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="company">Search By Company</label><br/>
                    <select id="company" name="company" class="form-control select2"  multiple>
                        <option value="">Select an option</option>
                        <?php foreach (Client::find()->all() as $client) { ?>
                            <option value="<?= $client->clientName ?>"><?= $client->clientName ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
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
    <form
        id="bulk-mail-out-form"
        action="<?= Url::to(['/email-users/send-bulk-mail-out']) ?>"
        method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= Yii::$app->request->csrfToken; ?>"/>
        <div id="client-users">
            <?php if (!empty($selectedUsers)) { ?>
            <h5>Selected Users <b>(<?= count($selectedUsers) ?>)</b>
                <button type="button" class="btn btn-warning">CSV EXPORT</button>
                <div style='margin-bottom: 2px'>
                    <input id='check-all-users' checked='checked' type='checkbox'/> Select All
                </div>
                <br/>
                <?php foreach ($selectedUsers as $email) { ?>
                    <div style='margin-bottom: 2px'>
                        <input type='checkbox' checked='checked' value='<?= $email ?>' name='users[]' class='user-checkbox'/> <?= $email ?>
                    </div>
                    <br/>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="subject">Send From</label>
                    <select id="send_from" name="send_from" class="form-control" >
                        <option value="sales@eventdrawus.com" selected>sales@eventdrawus.com</option>
                        <option value="support@eventdrawus.com">support@eventdrawus.com</option>
                        <option value="accounts@eventdrawus.com">accounts@eventdrawus.com</option>
                        <option value="jessa@eventdrawus.com">jessa@eventdrawus.com</option>
                        <option value="winston@eventdrawus.com">winston@eventdrawus.com</option>
                        <option value="bookkeeper@eventdrawus.com">bookkeeper@eventdrawus.com</option>
                    
                    </select>
                </div>
            </div>
        </div>
        <hr/>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="subject">Template Name</label>
                    <select id="email_template_id" name="email_template_id" class="form-control" required>
                        <option value="">Choose Template Name</option>
                        <?php if(!empty($dataProvider)){foreach($dataProvider as $template_obj){?>
                        <option value="<?= $template_obj->id;?>"><?= $template_obj->template_name;?></option>
                        <?php } } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" class="form-control" type="text" required/>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="heading">Heading</label>
                    <input id="heading" name="heading" id="heading" class="form-control email-heading" type="text"/>
                </div>
            </div>
        </div>
        <!--<div class="row">
            <div class="col-md-12 display-flex-div">
                <div class="width-100-percent">
                    <a id="remove-body" href="#" class="float-right-element btn btn-danger margin-left-5px"><i
                                class="glyphicon glyphicon-remove"></i></a>
                    <a id="add-body" href="#" class="float-right-element btn btn-success"><i
                                class="glyphicon glyphicon-plus-sign"></i></a>
                </div>
            </div>
        </div>-->
        <div class="form-group">
            <div id="body" class="row">
                <div class="col-md-12">
                    <label for="body1">Body</label>
                    <textarea class="form-control summernote-editor" id="email-paragraph" name="body"></textarea>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="attachment">Attachments</label>
                    <input id="attachment" name="attachment" class="form-control" type="file"/>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
        <button id="preview-email" type="button" class="btn btn-success">Preview Email</button>
        <div class="modal fade" id="previewTemplate1Modal" tabindex="-1" role="dialog" aria-labelledby="previewTemplate1Modal" aria-hidden="true">
            <?= $this->render('_preview-template1-modal') ?>
        </div>
    </form>
</div>

<script>
    $('.summernote-editor').summernote({
        height:400
    });
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
                    url: '<?= Url::to(['email-users/shift-select']) ?>',
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

    $(document).on('change', "#last-login-date", function (e) {
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#company').val('');

        data = {filter: $(this).val(), type: 'last-login-date', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#last-login-month", function (e) {
        $('#last-login-date').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#company').val('');

        data = {filter: $(this).val(), type: 'last-login-month', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#is-subscribed", function (e) {
        $('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#username').val('');
        $('#company').val('');

        data = {filter: $(this).prop("checked"), type: 'is-subscribed', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#username", function (e) {
        $('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#company').val('');
        $('#last_login').val('');
        $('#totSession').val('');
        $('#siDate').val('');
        data = {filter: $(this).val(), type: 'username', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#totSession", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#company').val('');
        $('#last_login').val('');
        $('#status').val('');
        $('#userType').val('');*/
        data = {filter: $(this).val(), type: 'totSession', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });

      $(document).on('change', "#siDate", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#company').val('');
        $('#last_login').val('');
        $('#status').val('');
        $('#userType').val('');*/
        data = {filter: $(this).val(), type: 'siDate', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    
    $(document).on('change', "#company", function (e) {
        $('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#last_login').val('');
        $('#totSession').val('');
        $('#status').val('');
        $('#userType').val('');
        data = {filter: $(this).val(), type: 'company', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#userType", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#status').val('');
        $('#last_email_date').val('');
        $('#totSession').val('');*/
        data = {filter: $(this).val(), type: 'userType', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#last_login", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#status').val(' ');
        $('#userType').val(' ');
        $('#last_email_date').val('');
        $('#totSession').val('');*/
        data = {filter: $(this).val(), type: 'last_login', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    $(document).on('change', "#last_email_date", function (e) {
        /*$('#last-login-date').val('');
        $('#last-login-month').val('');
        $('#is-subscribed').prop('checked',false);
        $('#username').val('');
        $('#status').val('');
        $('#userType').val('');
        $('#last_login').val('');
        $('#totSession').val('');*/
        data = {filter: $(this).val(), type: 'last_email_date', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

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
        data = {filter: $(this).val(), type: 'status', page: <?= $page ?>, selectAll: 1, isSelectAllClicked: 1};

        ajaxEligibleUsers(data);

        return false;
    });
    function ajaxEligibleUsers(postData) {
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-users/eligible-email-users']) ?>',
            type: 'post',
            data: postData,
            success: function (response) {
                let obj = JSON.parse(response);
                let isChecked = false;
                console.log(obj.users,obj.totalSelectedUsers);
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
                        $('#client-users').append("<div style='margin-bottom: 2px'><input type='checkbox' checked='checked' value='" + email + "' name='users[]' class='user-checkbox'/> " + email + "</div><br/>");
                    } else {
                        $('#client-users').append("<div style='margin-bottom: 2px'><input type='checkbox' value='" + email + "' name='users[]' class='user-checkbox'/> " + email + "</div><br/>");
                        $('#check-all-users').removeAttr("checked");
                    }
                });

                if (obj.totalPages > 0) {
                    let objData = obj.data;
                    objData.selectAll = data.selectAll;
                    objData.isSelectAllClicked = 0;
                    for (let i = 1; i <= obj.totalPages; i++) {
                        objData.page = i;
                        $('#client-users').append("<a class='btn btn-primary' style='margin-right: 10px' onclick='paginate("+ JSON.stringify(objData) +")'>"+ i +"</a>");
                    }
                }

                shiftKeySelection(true);
            },
            error: function () {
                console.log("error");
            }
        });
    }

    $(document).on('change', ".user-checkbox", function () {
        $.ajax({
            async: false,
            url: '<?= Url::to(['email-users/update-session']) ?>',
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
            url: '<?= Url::to(['email-users/export']) ?>',
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

    $("#bulk-mail-out-form").submit(function() {
        return  confirm("Are you sure you want to send the email to all the selected users?");
    });


    $(document).on('change', ".email-heading", function (e) {
        $('#email-template-heading').text($('.email-heading').val());
    });

    $(document).on('click', '#preview-email', function() {
        var body = $('.summernote-editor').val();

        document.getElementById('email-template-body').innerHTML = body;

        $('#previewTemplate1Modal').modal('toggle');
    });
    $('.select2').select2();
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
                    $('#email-paragraph').summernote('code',get_res.content);
                    $('#heading').val(get_res.bulletin_heading);
                }
            }

        });
    })
</script>
