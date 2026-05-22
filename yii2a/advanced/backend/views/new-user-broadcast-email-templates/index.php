<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\NewUserBroadcastEmailTemplatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'New Onboarding Template';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-user-broadcast-email-templates-index">

    <h1><?= Html::encode($this->title) ?> <span class="pull-right"><?= Html::a('New Onboarding Email / Bulletin', ['create'], ['class' => 'btn btn-success']) ?></span></h1>

    <h3>Venue</h3>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'heading',
            'link_heading',
            'link',
            'button_link',
            'hours',
            'userType',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
    <h3 style="margin-top:30px">Event Organizer</h3>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataOrganizer,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'heading',
            'link_heading',
            'link',
            'button_link',
            'hours',
            'userType',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
