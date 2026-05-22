<?php



use yii\helpers\Html;

use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

use unclead\multipleinput\MultipleInput;

use yii\helpers\Url;

use dosamigos\tinymce\TinyMce;
use common\models\User;
/* @var $this yii\web\View */

/* @var $model common\models\Template */

/* @var $form yii\widgets\ActiveForm */

?>



<div class="template-form">

    <form id="bulk-mail-out-form" action="<?= Url::to(['/bulletin-template/updates/?id='.$getData->id]) ?>"  method="post" enctype="multipart/form-data">

        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>"/>

        <div class="row">

            <input type="hidden" name="t_id" value="<?= $getData->id;?>">
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <tbody>
                        <?php if(!empty($getData->show_to_user)){
                            $userList   =   User::find()->where(['clientid'=>$getData->client_id])->all();
                            $show_to_user =   $getData->show_to_user;
                            $show_to_user = explode(',',$show_to_user);
                            $count=1;foreach($userList as $obj){
                                // print_r($obj);
                                // $user =    User::findOne(['id'=>$obj]);
                            ?>
                            <tr>
                                <td>
                                    <?= $count;?>
                                </td>
                                <td><?= $obj->userfullname;?></td>
                                <td>
                                    <input type="checkbox" name="user_id[]" value="<?= $obj->id;?>" <?php if(in_array($obj->id,$show_to_user)) echo 'checked';?> >
                                </td>
                            </tr>
                        <?php $count++;} ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">

            <div class="col-md-12 form-group">

                <button class="btn btn-success" type="submit">Save</button>
            </div>
        </div>
    </form>
</div>