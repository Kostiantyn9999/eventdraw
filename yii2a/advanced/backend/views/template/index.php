<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\Sort;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row-full">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Template', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'columns' => [
            [
     'attribute' => 'id',
     'value' => function ($model) {
          return Html::a(
              $model->id,
              ['update', 'id' => $model->id],
              [
                 'title' => 'Edit',
              ]
          );
      },
      'format' => 'raw',
],
            'templateName',
//              [
//                 'attribute' => 'xmlCode',
//                 'contentOptions' => [
//                     'style' => [
//                         'max-width' => '700px',
//                         'white-space' => 'normal',
//                     ],
//                 ],
//             ],
             [
                 'attribute'=>'clientid',
                 'format'=>'text',
                 'content'=>function($data){
                     return $data->getClientName();
                 },
                 'filter' => \common\models\Template::getClientList()
             ],
             [
                 'attribute'=>'created_by',
                 'format'=>'text',
                 'content'=>function($data){
                     return $data->getCreatedByName();
                 },
                 
             ],
             [
                 'attribute'=>'templateSize',
                 'format'=>'text',
                 'filter' => \common\models\Template::getSizeList(),
                 'headerOptions' => ['style' => 'width:15%']
             ],
             [
                 'attribute'=>'matterportid',
                 'format'=>'text',
                 'content'=>function($data){
                     return $data->getMatterportName();
                 },
                 'filter' => \common\models\Template::getMatterportList()
             ],
             [   'attribute' => 'created_at',
                 'format' => ['date','dd/MM/Y HH:mm:ss'],
             ],
             [   'attribute' => 'updated_at',
                 'format' => ['date','dd/MM/Y HH:mm:ss'],
             ],
             [
                 'class' => 'yii\grid\ActionColumn',
                 'header'=>'',
                 'template' => '{image}',
                 'buttons' => [
                     'image' => function ($url,$model) {
                         return Html::a(

                             '<span class="glyphicon glyphicon-picture"></span>',

                             $url);
                     },
                 ],
             ],
             [   'attribute' => 'templateActive',
                 'filter'=>array("1"=>"Yes","0"=>"No"),
                 'value' => function($model) { return $model->templateActive== 1 ? 'Yes' : 'No';}
             ],
             [   'attribute' => 'templateDefault',
                 'filter'=>array("1"=>"Yes","0"=>"No"),
                 'value' => function($model) { return $model->templateDefault== 1 ? 'Yes' : 'No';}
             ],
            //  [   'attribute' => 'shadow',
            //      'filter'=>array("1"=>"Yes","0"=>"No"),
            //      'value' => function($model) { return $model->shadow== 1 ? 'Yes' : 'No';}
            //  ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
