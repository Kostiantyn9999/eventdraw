<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'User Floorplans: ' . $model->userfullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Floorplans';

//get current list of templates for this user
$model->user_floorplans=$model::getUserFloorplans($model->id);

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-floorplans']); ?>

                <?php foreach ($model->user_floorplans  as $id => $eventName ): ?>
                     <div class="row">
                     
                      <?= Html::a($eventName,'https://test.eventdrawus.com/frontend/web/site/edit-event?EventID=' . $id,
                ['target'=>'_blank'])?>
             
                     </div>
                <?php endforeach; ?>

              
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
