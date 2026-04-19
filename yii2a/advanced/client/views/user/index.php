<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title =  Yii::$app->user->identity->getClientName() . ' Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row-full" >

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
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
            'userfullname',
            'email:email',
            'username',

            [
                    'class' => 'yii\grid\ActionColumn',
                'header'=>'',
                'template' => '{psw} {template}',
                'buttons' => [
                    'psw' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-lock"></span>',
                            $url);
                    },
                    'template' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-list-alt"></span>',
                            $url);
                    },
                  ],
            ],
            [   'attribute' => 'last_login',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'totSession',
                 'contentOptions' => ['style' =>'white-space:pre-line;max-width: 100px;'],

            ],

            
            //  [
            //     'attribute'=>'userStencilid',
            //     'format'=>'text',
            //     'content'=>function($data){
            //         return $data->getStencilName();
            //     },
            //     'filter' => \common\models\User::getStencilList()
            // ],
//            'auth_key',
//            'password_hash',
//            'password_reset_token',

            [
                'attribute'=>'status',
                'filter'=>array("10"=>"Active","9"=>"Inactive"),

                'value' => function($model) { return $model->status == 10 ? 'Active' : 'Inactive';}

            ],

             ['attribute' => 'company_admin',
                 'filter' => array("1" => "Yes", "0" => "No"),
                 'value' => function ($model) {
                     return $model->company_admin == 1 ? 'Yes' : 'No';
                 }
             ],

            // 'maxSession',

            ['class' => 'yii\grid\ActionColumn','template' => '{view} {update}'],
        ],
    ]); ?>


</div>
