<?php

use common\models\Client;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Client Templates: ' . $model->clientName;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Templates';

//get current list of templates for this client
$model->client_templates=$model::getClientTemplates($model->id);
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <button type="button" onclick="select_all()" class = "btn btn-primary" id="btnClientTemplatesSelectAll" >Select All</button>
    <button type="button" onclick="clear_all()" class = "btn btn-primary" id="btnClientTemplatesClearAll" >Clear All</button>


    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-templates']); ?>
           
           
            <?= $form->field($model, 'client_templates')->CheckBoxList($model::getTemplateList(),['itemOptions'=>['class' => 'checkbox-row']])?>

            <div class="form-group">
                <?= Html::submitButton('Set client templates', ['class' => 'btn btn-primary', 'name' => 'psw-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
<script type="text/javascript">

    function select_all()
    {
         var $chkboxes = $('.checkbox-row');
         $chkboxes.prop('checked', true);
            
    }
    function clear_all()
    {
         var $chkboxes = $('.checkbox-row');
         $chkboxes.prop('checked', false);
            
    }

    $(document).ready(function() {
        var $chkboxes = $('.checkbox-row');
        var lastChecked = null;

        $chkboxes.click(function(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }

            if (e.shiftKey) {
                var start = $chkboxes.index(this);
                var end = $chkboxes.index(lastChecked);

                $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
            }

            lastChecked = this;
        });
    });
</script>