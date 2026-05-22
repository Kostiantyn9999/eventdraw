<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EmailsSentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emails Sents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emails-sent-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'email:email',
            ['header' => 'Is Sent', 'value' => function ($model) { return $model->is_sent ? 'Yes' : 'No'; } ],
            'date_time',
            'subject',
            ['class' => 'yii\grid\ActionColumn','header'=>'Mail Content', 'template' => '{view}'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
