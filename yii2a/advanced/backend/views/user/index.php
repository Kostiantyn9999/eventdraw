<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row-full" >

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('CSV export', ['export'], ['class' => 'btn btn-warning']) ?>
    </p>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'clientid',
                'format'=>'text',
                'content'=>function($data){
                    return $data->getClientName();
                },
                'filter' => \common\models\User::getClientList()
            ],
            //'userfullname',
            'firstname',
            'surname',
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
             'totSession',
             [
                'attribute'=>'userStencilid',
                'format'=>'text',
                'content'=>function($data){
                    return $data->getStencilName();
                },
                'filter' => \common\models\User::getStencilList()
            ],
//            'auth_key',
//            'password_hash',
//            'password_reset_token',

            [
                'attribute'=>'status',
                'filter'=>array("10"=>"Full version","9"=>"Trial","11"=>"Custom Trial"),

                'value' => function($model) {  return $model->getStatusName();}

            ],
            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/Y'],
            ],

//            [   'attribute' => 'updated_at',
//                'format' => ['date','dd/MM/YY HH:mm'],
//            ],

              //'verification_token',
            //'user_type',
            //'user_full_name',
            [   'attribute' => 'userIsAdmin',
                'filter'=>array("1"=>"Yes","0"=>"No"),
            'value' => function($model) { return $model->userIsAdmin == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'userIsSupport',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->userIsSupport == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'userPayment',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->userPayment     == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'company_admin',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->company_admin     == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            'maxSession',
            ['class' => 'yii\grid\ActionColumn','template' => '{view} {update}'],
        ],
    ]); ?>


</div>
