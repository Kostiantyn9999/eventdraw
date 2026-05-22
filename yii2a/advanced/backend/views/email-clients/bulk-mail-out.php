<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Email Clients';
$this->params['breadcrumbs'][] = ['label' => 'Email Clients', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', ['selectedUsers' => $selectedUsers, 'page' => $page,'searchModel'=>$searchModel,'dataProvider'=>$dataProvider]) ?>
</div>

<script>
    let keys = [];
    $('.select-on-check-all, .checkbox-row').change(function(){
        keys = $('#grid').yiiGridView('getSelectedRows');
    });

    $('#email-users').click(function(){
        window.location.href = "/backend/web/email-clients/bulk-mail-out/?selected=" + JSON.stringify(keys) + "&from=client";
    });

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

