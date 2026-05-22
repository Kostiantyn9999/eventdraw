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

    <div class="display-flex-div">
        <p>
            <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <p class="width-100-percent">
            <a id="email-users" class="float-right-element btn btn-primary">Email Users</a>

            <a id="bulletins"  class="float-right-element btn btn-info" style="margin-right: 5px;">Bulletins</a>&nbsp;&nbsp;&nbsp;
        </p>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'rowOptions' => function($model, $key, $index, $column){
            if (!is_null($model->expiry_date) and $model->expiry_date < strtotime("now")) {
                          return ['style' => 'background-color: pink'];
                      } 

        },
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model) {
                    return ['value' => $model->id, 'class' => 'checkbox-row', 'id' => 'checkbox'];
                }
            ],
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

            [   'attribute' => 'clientName',
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 200px;'],
            ],

            [   'attribute' => 'clientEmail',
                'format' => 'email',
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 200px;'],
            ],

            [
                'attribute' => 'clientCountry',
                'value' => function ($model) {
                    return  $model->getCountryName();
                    
                },
                'format' => 'raw',
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 150px;'],
                'filter' => \common\models\Country::getCountryList(),
            ],
            

             [   'attribute' => 'last_client_login',
                'format' => ['date','dd/MM/y HH:mm:ss'],
                'filter' => \common\models\client::getLastLoginList(),
            ],
             [   'attribute' => 'last_client_email',
                'format' => ['date','dd/MM/y HH:mm:ss'],
                'filter' => \common\models\client::getLastLoginList(),
            ],
             [
                'attribute'=>'status',
                'filter'=>array("10"=>"Full version","9"=>"Trial","11"=>"Custom Trial","0"=>"No Access","12"=>"Churn"),

                'value' => function($model) {  return $model->getStatusName();}

            ],
            [   'attribute' => 'client_max_sessions',
                 'filter'=>array("1"=>"0-100","2"=>"101-500","3"=>"501-1000","4"=>"More 1000"),
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 100px;'],
            ],

            [
                'attribute'=>'clientType',
                'filter'=>array("1"=>"Venue","2"=>"Event Organiser","0"=>"Not Set"),

                'value' => function($model) {  return $model->getTypeName();}

            ],
            [
                'attribute' => 'ClientVenueType',
                'value' => function ($model) {
                    return  $model->getVenueTypeName();
                    
                },
                'format' => 'raw',
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 250px;'],
                'filter' => \common\models\VenueType::getVenueTypeList(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'',
                'template' => '{template} {stencil} {settings}',
                'buttons' => [
                    'template' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-list-alt"></span>',
                            $url);
                    },
                    'stencil' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-th"></span>',
                            $url);
                    },
                    'settings' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-cog"></span>',
                            $url);
                    },
                ],
            ],

            [   'attribute' => 'clientNotes',
                'contentOptions' => ['style' =>'white-space:pre-line;max-width: 4000px;'],
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
                'filter' => \common\models\client::getLastLoginList(),
            ],
            [
                'attribute'=>'siDate',
                'filter'=>array("1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December"),

                'value' => function($model) {  return $model->getSiDateName();}

            ],

            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/y HH:mm:ss'],
            ],

            [   'attribute' => 'clientPayment',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->clientPayment== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'ShowMaxCapPlans',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->ShowMaxCapPlans== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowSaveCloud',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->AllowSaveCloud== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowFavouriteStencils',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->AllowFavouriteStencils== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'Allow3D',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->Allow3D== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowImportPdf',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->AllowImportPdf== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'AllowShare',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->AllowShare== 1 ? 'Yes' : 'No';}
            ],
           [   'attribute' => 'AllowSaveFolder',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->AllowSaveFolder== 1 ? 'Yes' : 'No';}
            ],

            [   'attribute' => 'expiry_date',
                'format' => ['date','dd/MM/y'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<script>
    let keys = [];
    $('.select-on-check-all, .checkbox-row').change(function(){
        keys = $('#grid').yiiGridView('getSelectedRows');
    });

    $('#email-users').click(function(){
        window.location.href = "/backend/web/email-clients/bulk-mail-out/?selected=" + JSON.stringify(keys) + "&from=client";
    });

    $('#bulletins').click(function(){
        window.location.href = "/backend/web/bullet-boards/create/?selected=" + JSON.stringify(keys)+ "&from=client";
    });

    $(document).ready(function() {
        var $chkboxes = $('.checkbox-row');
        var lastChecked = null;

        $chkboxes.click(function(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }

            if (e.shiftKey) {
                var start = $chkboxes.index(this);
                var end = $chkboxes.index(lastChecked);

                $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
            }

            lastChecked = this;
        });
    });
</script>
