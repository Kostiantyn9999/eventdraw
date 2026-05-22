<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\NewUserBroadcastEmailTemplatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\userBulletinModel;
use common\models\User;
$this->title = 'Bulletin Boards';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .row.mb-3 {
        margin-bottom: 20px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
<div class="new-user-broadcast-email-templates-index">
    <div class="row mb-3">
        <div class="col-md-10"></div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".all-bulletin-delete-modal">All Delete</button>
        </div>
        <div class="col-md-1">
            <button id="multiple-bulletin-delete" class="float-right-element btn btn-danger">Delete</button>
        </div>
    </div>
    <div class="modal fade all-bulletin-delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <form method="post" onsubmit="return false" id="bulletin_delete_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p><strong>Alert!</strong> Once you delete the bulletin, you cannot revert this action. Are you sure you want to delete this?</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" id="confirm_delete">Confirm</button>
                                <!-- <a href="<?= Url::to(['bullet-boards/bulk-bulletin-delete']) ?>" class="btn btn-success">Confirm</a> -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <table class="table table-striped" id="email_template_table">

        <thead>

            <tr>
                <th><input type="checkbox" class="select-on-check-all" name="selection_all"></th>
                <th>Sr.No</th>
                <th>Heading</th>
                <th>Username</th>
                <th>Action</th>

            </tr>

        </thead>

        <tbody>

            <?php 
            $count=1; if(!empty($dataProvider)){foreach($dataProvider as $key=> $obj){?>

            <tr>
                <td>
                    <input type="checkbox" id="checkbox" class="checkbox-row" name="selection[]" value="<?= $obj->id;?>">
                </td>
                <td><?= $counts;?></td>
                <td><?= $obj->heading;?></td>
                <td>
                    <?php 
                        $userBulletinModel  =   new userBulletinModel();
                        $user               =   new User();
                        $allDala = userBulletinModel::find()->where(['bullet_board_id' => $obj->id])->all();
                        if(!empty($allDala)){foreach($allDala as $row){
                            $user_info      =   $user::find()->where(['id'=>$row->user_id])->one();
                            echo $user_info->userfullname.' , ';
                        }}
                    ?>
                </td>
                <td>
                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/bullet-boards/update/?id='.$obj->id])?>"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/bullet-boards/view/?id='.$obj->id])?>"><i class="fa fa-copy"></i></a>
                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/bullet-boards/delete/?id='.$obj->id])?>"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            <?php $count++; } }?>
        </tbody>
    </table>
</div>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select-on-check-all').change(function() {
            if (this.checked) {
                // If "Select All" is checked, check all checkboxes with the class "checkbox-row"
                $('.checkbox-row').prop('checked', true);
            } else {
                // If "Select All" is unchecked, uncheck all checkboxes with the class "checkbox-row"
                $('.checkbox-row').prop('checked', false);
            }
        });

        $('.checkbox-row').change(function() {
            // Check if all "checkbox-row" checkboxes are checked and update "Select All" accordingly
            if ($('.checkbox-row:checked').length === $('.checkbox-row').length) {
                $('.select-on-check-all').prop('checked', true);
            } else {
                $('.select-on-check-all').prop('checked', false);
            }
        });
        let keys = [];

        $(document).ready(function() {
            $('.checkbox-row, .select-on-check-all').change(function() {
                keys = $('.checkbox-row:checked').map(function() {
                    return $(this).val();
                }).get();

                // Move the console.log inside the event handler
                console.log(keys);
            });
        });
        $('#email_template_table').DataTable({
            paging: true,       // Enable pagination
            pageLength: 10,     // Default page length
            lengthMenu: [10, 50, 100, 500, 5000],
            columnDefs: [
                {
                    targets: [0,4], // Third column (Action column)
                    orderable: false, // Disable sorting on this column
                    searchable: false, // Disable searching on this column
                },
            ],
        });

        $('#multiple-bulletin-delete').click(function(){
            window.location.href = "/backend/web/bullet-boards/bulk-delete-bulletin/?selected=" + JSON.stringify(keys);
        });

        $('#confirm_delete').on('click',function(){
            console.log('confirm_delete');
            $.ajax({
                type: "POST",
                url: "<?= Url::to(['bullet-boards/ajax-bulk-bulletin-delete']) ?>",
                success: function(response) {
                    console.log(response);
                    if(response=='success'){
                        toastr.success('Pending Bulletin Deleted Successfully');
                        window.location.reload();
                    }else{
                        toastr.warning('Something went wrong, try again or later');
                    }
                }
            });
        })

    });
</script>