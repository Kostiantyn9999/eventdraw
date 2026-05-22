<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $modelTemplate common\models\UserTemplates */
/* @var $modelStencil common\models\UserStencils */
/* @var $modelSettings common\models\Usersettings */

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

        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>

    
   <?= Html::a('Send Login', ['sendlogin', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to send login info?',
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
            [   'attribute' => 'userType',
                'value' => function($model) { return $model->getuserType();}
            ],
             'UserCompanyName',

             [   'attribute' => 'UserCountry',
                'value' => function($model) { return $model->getCountryName();}
            ],


            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
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
            [   'attribute' => 'AllowSaveCloud',
                'value' => function($model) { return $model->AllowSaveCloud == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'last_login',
                'format' => ['date','dd/MM/y HH:mm:ss'],
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
        'template' => '<tr><th>{label}</th><td style="width:70%;">{value}</td></tr>',
    ]) ?>

    <!--    Settings-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-cog"></i> Settings
            </h4>
            <?= Html::a('Change Settings', ['settings', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        </div>

        <?= DetailView::widget([
            'model' => $modelSettings,
            'attributes' => [
                [
                    'attribute'=>'meas_unit',
                    'format'=>'text',
                    'value'=>function($data){
                        return $data->getMeasurementName($data->meas_unit);
                    },
                ],
            ],
            'template' => '<tr><th>{label}</th><td style="width:70%;">{value}</td></tr>',
        ]) ?>


    </div>

    <!--    templates-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-book"></i> Template list
            </h4>
            <?= Html::a('Choose Templates', ['template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <!-- show list of templates-->
                <?php foreach ($modelTemplate as $i => $modelTemplates): ?>

                    <div class="row">
                        <?= Html::encode($modelTemplates->getTemplateName()) ?>
                    </div><!-- .row -->

                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-th"></i> Stencil list
            </h4>
            <?= Html::a('Choose Stencils', ['stencil', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <!-- show list of templates-->
                <?php foreach ($modelStencil as $i => $modelStencils): ?>

                    <div class="row">
                        <?= Html::encode($modelStencils->getStencilName()) ?>
                    </div><!-- .row -->

                <?php endforeach; ?>
            </div>
        </div>


    </div>




</div>
