<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */


$this->title = 'Admin EventDraw';
// var_dump($model->getId());
//var_dump( $model);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Admin/support page</h1>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Users</h2>
                <p><a class="btn btn-default" href="../web/user">Manage Users &raquo;</a></p>
                <p><a class="btn btn-default" href="../web/admin-user">Admin Users &raquo;</a></p>
                <p><a class="btn btn-default" href='../web/client'>Manage Clients &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Templates</h2>
                <p><a class="btn btn-default" href="../web/template">Manage Templates &raquo;</a></p>
                <p><a class="btn btn-default" href="../web/templategroups">Template Groups &raquo;</a></p>
            
            </div>

            <div class="col-lg-4">
                <h2>Other</h2>
                <p><a class="btn btn-default" href="../web/stencil">Manage Stencils &raquo;</a></p>
                <p><a class="btn btn-default" href="../web/apprelease">Manage Releases &raquo;</a></p>

                 <br>
                <p><a class="btn btn-default" href="../web/new-stencils">Manage User Stencils &raquo;</a></p>
            </div>

        </div>

    </div>
</div>

<div class="popup_box" style="<?php if($userData->userType=='1' || $userData->userType=='2') echo 'display:none;';?>">
    <?php $form = ActiveForm::begin([
        'action' => ['site/AjaxPopupUpdate'],
        'options' => ['class' => 'user-save-form','id'=>'popup_modal']
    ]); ?>
    <div class="extra_details">
        <h4>To assist our team with customised training and support for you, please choose one of the below!</h4>

        <div style="    border-bottom: 5px solid;">
            <h1><sup>*</sup> VENUE / PROPERTY <label> <input type="radio" id="venue" class="userType" name="user_type" value="1"><span></span></label></h1>
            <p>I'm a professionals working at Hotel, Wedding Venue, Performing Arts Venues, Convention Centres, Stadiums, Casino, Theatre's, Schools, Universities, Goverment, Council or Other Venues etc</p>
        </div>

        <div>
            <h1><sup>*</sup> EVENT ORGANISER  <label><input type="radio" id="organiser" class="userType" name="user_type" value="2"><span></span></label></h1>
            <p>I'm an independent Event Planner AV Company / Staging Company, Festival Planner of Caterer using many different venues for my events.</p>
        </div>
        <span class="close">X</span>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
jQuery(document).ready(function(){
    jQuery('span.close').click(function(){
        jQuery('.popup_box').hide();
    });
    $(document).on('click', ".userType", function (e) {
        var user_type   =   $('input[name="user_type"]:checked').val();
        const data = {'user_type':user_type};
        var yiiform = $(this);
        console.log(data);
        $.ajax({
            async: false,
            url: 'https://developer.eventdrawus.com/backend/web/site/update',
            type: 'post',
            data: data,
            success: function (response) {
                console.log(response);
                var get_res     =   $.parseJSON(response);
                if(get_res.status=='true'){  
                    console.log(get_res.message);
                    //$("#popup_modal").trigger('reset');
                    $('#popup_modal').modal('hide');
                }else{
                    console.log(get_res.message);
                }
            },
            error: function () {
                console.log("error");
            }
        });
    });
});
</script>
<style type="text/css">
    span.close {
    position: absolute;
    top: -10px;
    right: -10px;
    background: red;
    z-index: 2;
    opacity: 1;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 60px;
    padding: 5px;
}
    .extra_details {
    text-align: center;
    font-size: 12px;
}

.extra_details h1 {
    font-size: 20px;
    text-align: left;
}


.extra_details h1 label input {
    opacity: 0;
    position: absolute;
    top: 0;
    left: 0;
}

.extra_details h1 label {
    display: inline-block;
    width: 25px;
    padding: 5px;
    border: 2px solid;
    border-radius: 50px;
    height: 25px;
    position: relative;
    float: right;
    cursor:pointer;
}
.extra_details h1 label:hover span{
    background:#a8518a;
}
.extra_details h1 label input:checked + span {
    font-weight: bold;    background:#a8518a;
}
.extra_details h1 label span {
    border: 1px solid #aaa;
    padding: 4px;display:inline-block;width: 15px;height: 15px;border: 1px solid;border-radius: 10px;position: absolute;left: 3px;top: 3px;}

    .popup_box {
    position: fixed;
    top: 0;
    left: 0;
    background: #00000038;
    width: 100%;
    height: 100%;
    padding: 5vh;
}

.popup_box .extra_details {
    position: absolute;
    top: 23vh;
    max-width: 500px;
    margin: 0 auto;
    left: 0;
    right: 0;
    background: #fff;
    padding: 30px;
    border-radius: 20px;
    color: #a8518a;
}

</style>