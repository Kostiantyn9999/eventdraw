<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Event Versions: ' . $model->eventName;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$items  = [];

$s3 = Yii::$app->get('s3');
$filename_looking = 'eventdraw_data/events/' .  $model->id . '.xml';
$result = $s3->commands()->version($filename_looking)->execute();
$array = (array) $result;

$data = $array[array_keys($array)[0]];
   

foreach($data["Versions"] as $value){

    $item_date = $value["LastModified"]->format('d M Y H:i:s');
    $items[$value["VersionId"]] = $item_date . ' -  Size: ' . $value["Size"] . ' bytes';
}


?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-versions']); ?>

            <?= $form->field($model, 'event_version')->dropDownList($items)?>


            <div class="form-group">
                <?= Html::submitButton('Load selected version', ['class' => 'btn btn-danger', 'name' => 'versions-button'
                ,   'data' => [
                'confirm' => 'Are you sure you want to load selected version?',
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






