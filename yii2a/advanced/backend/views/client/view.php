<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $modelUser common\models\User */
/* @var $modelTemplate common\models\ClientTemplates */
/* @var $modelStencil common\models\ClientStencils */

$this->title = $model->clientName;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('User settings', ['settings', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'clientName',
            'clientEmail:email',
            [   'attribute' => 'clientCountry',
                'value' => function($model) { return $model->getCountryName();}
            ],
            [   'attribute' => 'clientPayment',
                'value' => function($model) { return $model->clientPayment == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'ShowMaxCapPlans',
                'value' => function($model) { return $model->ShowMaxCapPlans == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowSaveCloud',
                'value' => function($model) { return $model->AllowSaveCloud == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowFavouriteStencils',
                'value' => function($model) { return $model->AllowFavouriteStencils == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'Allow3D',
                'value' => function($model) { return $model->Allow3D == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowImportPdf',
                'value' => function($model) { return $model->AllowImportPdf == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowShare',
                'value' => function($model) { return $model->AllowShare == 1 ? 'Yes' : 'No';}
            ],
             [   'attribute' => 'AllowMomentus',
                'value' => function($model) { return $model->AllowMomentus == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowSaveFolder',
                'value' => function($model) { return $model->AllowSaveFolder == 1 ? 'Yes' : 'No';}
            ],
            'MomentusAPIUrl',
            'MomentusAPIKey',
            'MomentusSecretKey',
            'momentusOrgCode',
            'MomentusAPIUrlAuth',
            [
                'label' => 'Favourite Stencil',
                'format' => 'raw',
                'attribute' => 'id',
                'value' => function ($data) {

                    $path= '../../frontend/web/site/stencils_favourite/' . $data['id'] . '_Favourites.xml';
                    if (file_exists($path)) {
                        return Html::a($data['id'] . '_Favourites.xml', ['download' , 'id' => $data['id']]);
                    }
                    else
                    {
                        return 'Not set';
                    }


                },

            ],
            [   'attribute' => 'status',
                'value' => function($model) { return $model->getStatusName();}
            ],
            [   'attribute' => 'clientType',
                'value' => function($model) { return $model->getTypeName();}
            ],
            [   'attribute' => 'ClientVenueType',
                'value' => function($model) { return $model->getVenueTypeName();}
            ],
             'clientNotes',
            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'siDate',
                'value' => function($model) { return $model->getSiDateName();}
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'last_client_login',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
            [   'attribute' => 'last_client_email',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],
        ],
    ]) ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-user"></i> User list
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <!--                users header-->
                <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-2">
                        <b>First Name</b>
                    </div>
                    <div class="col-sm-2">
                        <b>Surname</b>
                    </div>
                    <div class="col-sm-2">
                        <b>Email</b>
                    </div>
                    <div class="col-sm-2">
                        <b>Login</b>
                    </div>
                    <div class="col-sm-2">
                        <b>Company Admin</b>
                    </div>
                    <div class="col-sm-2">
                        <b>Status</b>
                    </div>
                </div>
                </div>
                <!-- show list of users-->
                <?php foreach ($modelUser as $i => $modelUsers): ?>

                    <div class="row">
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->firstname) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->surname) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->email) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->username) ?>
                        </div>
                        <div class="col-sm-2">
                            <?= Html::encode( $modelUsers->company_admin     == 1 ? 'Yes' : 'No') ?>
                         </div>
                        <div class="col-sm-2">
                            <?= Html::encode($modelUsers->getStatusName()) ?>
                        </div>
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






</div>
