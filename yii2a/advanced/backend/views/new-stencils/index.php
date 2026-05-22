<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NewStencilsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Stencils';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stencil-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
  

            [
     'attribute' => 'ID',
     'value' => function ($model) {
          return Html::a(
              $model->ID,
              ['update', 'id' => $model->ID],
              [
                 'title' => 'Edit',
              ]
          );
      },
      'format' => 'raw',
],

[
     'attribute' => 'userid',
     'value' => function ($model) {
          return
              $model->getUserName()
          ;
      },
      'format' => 'raw',
],

            'stencilName',



            
            [   'attribute' => 'stencilActive',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->stencilActive== 1 ? 'Yes' : 'No';}
            ],
            
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
                'filter' => \common\models\NewStencils::getDataList(),
            ],


            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
                 'filter' => \common\models\NewStencils::getDataList(),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
