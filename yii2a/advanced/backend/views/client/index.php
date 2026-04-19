<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row-full">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'clientName',
            'clientEmail:email',
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'',
                'template' => '{template}',
                'buttons' => [
                    'template' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-list-alt"></span>',
                            $url);
                    },
                ],
            ],

            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],

            [   'attribute' => 'clientPayment',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->clientPayment== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'ShowMaxCapPlans',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->ShowMaxCapPlans== 1 ? 'Yes' : 'No';}
            ],
            [
                'attribute'=>'status',
                'filter'=>array("10"=>"Full version","9"=>"Trial","11"=>"Custom Trial"),

                'value' => function($model) {  return $model->getStatusName();}

            ],
            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/Y'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
