<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NewUserBroadcastEmailTemplates */

$this->title = 'Bulletin Boards';
$this->params['breadcrumbs'][] = ['label' => 'New Bulletin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-user-broadcast-email-templates-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <!-- <div id="client-users">
        <?php 
        if (!empty($selectedUsers)) { ?>
        <h5>Selected Users <b>(<?= count($selectedUsers) ?>)</b>
        <?php foreach ($selectedUsers as $email) { ?>
        <div style='margin-bottom: 2px'>
            <input type='checkbox' checked='checked' value='<?= $email ?>' name='users[]' class='user-checkbox'/> <?= $email ?>
        </div>
        <br/>
        <?php } ?>
        <?php } ?>
    </div> -->
    <?= $this->render('_form', [
        'model' => $model,
        'selectedUsers'=>$selectedUsers,
        'tempData'=>$tempData
    ]) ?>
    
</div>
