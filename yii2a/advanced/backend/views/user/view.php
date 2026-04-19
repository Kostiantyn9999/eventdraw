<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Set Password', ['psw', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Set Templates', ['template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            //'userfullname',
            'firstname',
            'surname',
            'email:email',
            [
                'attribute'=>'clientid',
                'format'=>'text',
                'value'=>function($data){
                    return $data->getClientName();
                },
            ],
             [   'attribute' => 'status',
                'value' => function($model) { return $model->getStatusName();}
            ],
            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],

            [   'attribute' => 'userIsAdmin',
                'value' => function($model) { return $model->userIsAdmin == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'userIsSupport',
                'value' => function($model) { return $model->userIsSupport == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'userPayment',
                'value' => function($model) { return $model->userPayment     == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'company_admin',
                'value' => function($model) { return $model->company_admin     == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'last_login',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            'maxSession',
            'totSession',
            [
                'attribute'=>'userStencilid',
                'format'=>'text',
                'value'=>function($data){
                    return $data->getStencilName();
                },
            ],
        ],
    ]) ?>

</div>
