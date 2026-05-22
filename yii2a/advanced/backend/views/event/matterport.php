<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = 'Apply Matterport: ' . $model->eventName;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//get current list of stencils for this user

$matterport_templates = \common\models\Template::find()
           ->select('id, templateName')
           ->where( ['IS NOT', 'matterportid', null])
           ->orderBy(['templateName' => SORT_ASC])
           ->all();

    $items = ArrayHelper::map($matterport_templates,'id','templateName');
    $params = [
        'prompt' => '-- Select matterport template --'
    ];

//get existing applied matterport

$Matterport_assign=\common\models\MatterportAssign::find()
         ->where(['layout_id' =>$model->id ])
            ->orderBy(['matterport_id' => SORT_DESC])
            ->one();
        
        if ($Matterport_assign ){
            $model->event_matterport = $Matterport_assign->matterport_id;
        }

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-matterport']); ?>

            
            <?= $form->field($model, 'event_matterport')->dropDownList($items,$params)?>
            
           
           

            <div class="form-group">
                <?= Html::submitButton('Apply Matterport', ['class' => 'btn btn-danger', 'name' => 'matterport-button'
                ,   'data' => [
                'confirm' => 'Are you sure you want to apply selected matterport?',
                'method' => 'post',
            ],],
             
            ) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['update?id=' . $model->id], ['class'=>'float-right-element btn btn-info'],
                ) ?>
                
               
            </div>


            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>






