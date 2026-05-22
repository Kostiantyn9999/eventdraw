<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unassigned Cloud Saved Floor plans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
     'attribute' => 'eventid',
     'value' => function ($model) {
          return Html::a(
              $model["eventid"],
              ['update', 'id' => $model["eventid"]],
              [
                 'title' => 'Edit',
              ]
          );
      },
      'format' => 'raw',
],


            ['attribute' => 'eventName',
            'format' => 'raw',
            'contentOptions' => ['style' =>'white-space:pre-line;max-width: 200px;'],
            'value' => function($model) {
                return Html::a($model["eventName"],'https://test.eventdrawus.com/frontend/web/site/edit-event?EventID=' . $model["eventid"],
                ['target'=>'_blank']);
              }
            ],

            [
                'attribute'=>'userName',
                'format'=>'text',
                // 'content'=>function($data){
                //     return $data->getUserName();
                // },
                //'filter' => \common\models\Event::getUserList()
            ],
            [
                'attribute'=>'clientName',
                'filter' => \common\models\Event::getClientList(),
                'format'=>'text',
            ],
             [
                'format' => 'html',    
                'attribute' => 'imageCode',
                'format' => ['image', ['width' => '140']]
            ],

              [
                'class' => 'yii\grid\ActionColumn',
                'header' => '-',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{matterport}',
                'buttons' => [
                  'matterport' => function ($url, $model) {
                      return Html::a('<span class="glyphicon glyphicon-picture">&nbsp</span>', $url, [
                                  'title' => 'Apply matterport',
                      ]);
                  },
      
                 
     
      
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                  if ($action === 'matterport') {
                      $url ='event/matterport?id='. $model["eventid"];
                      return $url;
                  }
      
                 
              
      
                }
                ],
            // [
            //     'attribute'=>'eventSize',
            //     'format'=>'text',
            //     'filter' => \common\models\Template::getSizeList(),
            //     'content'=>function($data){
            //         return $data->getSize();
            //     },
            //     'headerOptions' => ['style' => 'width:15%']
            // ],
            //'xmlCode:ntext',
            [   'attribute' => 'created_at',
                 'format' => ['date','dd/MM/Y HH:mm:ss'],
             ],
             [   'attribute' => 'updated_at',
                 'format' => ['date','dd/MM/Y HH:mm:ss'],
             ],

            //'eventdate',
            [   'attribute' => 'eventActive',
                 'filter'=>array("1"=>"Yes","0"=>"No"),
                  'value' => function($model) {
                      return $model["eventActive"]== 1 ? 'Yes' : 'No';
                    }
             ],
           
            //'imageCode:ntext',
            //'eventInfo:ntext',
            [
                'attribute'=>'totalSessions',
                'format'=>'text',
               
            ],
           
             [   'attribute' => 'event_shared',
                 'filter'=>array("1"=>"Yes","0"=>"No"),
                  'value' => function($model) {
                      return $model["event_shared"] > 0 ? 'Yes' : 'No';
                    }
             ],
             
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}{update}',
                'buttons' => [
                  'view' => function ($url, $model) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open">&nbsp</span>', $url, [
                                  'title' => 'View',
                      ]);
                  },
      
                  'update' => function ($url, $model) {
                      return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                  'title' => 'Update',
                      ]);
                  },
                //   'delete' => function ($url, $model) {
                //       return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                //                   'title' => Yii::t('app', 'lead-delete'),
                //       ]);
                //   }
      
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                  if ($action === 'view') {
                      $url ='event/view?id='. $model["eventid"];
                      return $url;
                  }
      
                  if ($action === 'update') {
                      $url ='event/update?id='.$model["eventid"];
                      return $url;
                  }
                //   if ($action === 'delete') {
                //       $url ='index.php?r=client-login/lead-delete&id='.$model["eventid"];
                //       return $url;
                //   }
      
                }
                ],
        ],
    ]); ?>


</div>
