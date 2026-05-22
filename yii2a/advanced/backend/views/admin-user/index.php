<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\db\Expression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin Users';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row-full">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="display-flex-div">
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

[
     'attribute' => 'clientid',
     'value' => function ($model) {
          return Html::a(
              $model->getClientName(),
              ['/../web/client/update', 'id' => $model->clientid],
              [
                 'title' => 'Update Client info',
              ]
          );
      },
      'format' => 'raw',
      'contentOptions' => ['style' =>'white-space:pre-line;max-width: 150px;'],
      'filter' => \common\models\User::getClientList(),
],

           
            
//             [
//      'attribute' => 'userSavedFloorplansCount',
//      'value' => function ($model) {
//           return Html::a(
//               $model->userSavedFloorplansCount,
//               ['floorplans', 'id' => $model->id],
//               [
//                  'title' => 'List of Saved Floorplans',
//               ]
//           );
//       },
//       'format' => 'raw',
// ],

            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'header' => '',
            //     'template' => '{floorplans}',
            //     'buttons' => [
            //         'floorplans' => function ($url, $model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-file"></span>',
            //                 $url);
            //         },
            //     ],
            // ],
            

            //'userfullname',
            'firstname',
            ['attribute' => 'surname',
            'contentOptions' => ['style' =>'white-space:pre-line;max-width: 150px;'],
            ],

            ['attribute' => 'email',
            'format' => 'email',
            'contentOptions' => ['style' =>'white-space:pre-line;max-width: 270px;'],
            ],  
        //    ['attribute' => 'username',
        //      'contentOptions' => ['style' =>'white-space:pre-line;max-width: 200px;'],
        //      'content' => function ($data) {
        //           if(strlen($data->username) > 25)
        //           {
        //             return substr($data->username, 0, 25) . ' ' . substr($data->username, 25);
        //           }
        //           else
        //           {
        //             return $data->username ;
        //           }
                    
        //         },
        //     ],

            // ['attribute' => 'UserCompanyName',
            //  'contentOptions' => ['style' =>'white-space:pre-line;max-width: 300px;'],
            //  'content' => function ($data) {
            //     //   if(strlen($data->UserCompanyName) > 25)
            //     //   {
            //     //     return substr($data->UserCompanyName, 0, 25) . ' ' . substr($data->UserCompanyName, 25);
            //     //   }
            //     //   else
            //     //   {
            //         return $data->UserCompanyName ;
            //     //   }
                    
            //     },
            // ],

//  [
//                 'attribute' => 'UserCountry',
//                 'value' => function ($model) {
//                     return  $model->getCountryName();
                    
//                 },
//                 'format' => 'raw',
//                 'contentOptions' => ['style' =>'white-space:pre-line;max-width: 150px;'],
//                 'filter' => \common\models\Country::getCountryList(),
//             ],
           

            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'header' => '',
            //     'template' => '{psw} {template} {stencil} {settings}',
            //     'buttons' => [
            //         'psw' => function ($url, $model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-lock"></span>',
            //                 $url);
            //         },
            //         'template' => function ($url, $model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-list-alt"></span>',
            //                 $url);
            //         },
            //         'stencil' => function ($url, $model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-th"></span>',
            //                 $url);
            //         },
            //         'settings' => function ($url, $model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-cog"></span>',
            //                 $url);
            //         },
            //     ],
            // ],

            [   'attribute' => 'last_login',
                'format' => ['date','dd/MM/y HH:mm:ss'],
                'filter' => \common\models\client::getLastLoginList(),
            ],


            // 'totSession',
            // [
            //     'attribute' => 'userStencilid',
            //     'format' => 'text',
            //     'content' => function ($data) {
            //         return $data->getStencilName();
            //     },
            //     'filter' => \common\models\User::getStencilList()
            // ],
//            'auth_key',
//            'password_hash',
//            'password_reset_token',

            // [
            //     'attribute' => 'status',
            //     'filter' => array("10" => "Full version", "9" => "Trial", "11" => "Custom Trial", "0" => "No Access", "12" => "Churn"),

            //     'value' => function ($model) {
            //         return $model->getStatusName();
            //     }

            // ],
            // [
            //     'attribute' => 'userType',
            //     'filter' => array( "1" => "Venue", "2" => "Event Organiser", "0" => "Not Set"),

            //     'value' => function ($model) {
            //         return $model->getTypeName();
            //     }

            // ],
            // [   'attribute' => 'AllowSaveCloud',
            //     'filter'=>array("1"=>"Yes","0"=>"No"),
            //     'value' => function($model) { return $model->AllowSaveCloud== 1 ? 'Yes' : 'No';}
            // ],
            // ['attribute' => 'expiry_date',
            //     'format' => ['date', 'dd/MM/y'],
            //      'contentOptions' => function($model) {
            //           if ($model->expiry_date < strtotime("now")) {
            //               return ['style' => 'color:red'];
            //           } 
            //             else return ['style' => 'color:black'];
            //     },
            // ],

//            [   'attribute' => 'updated_at',
//                'format' => ['date','dd/MM/YY HH:mm'],
//            ],

            //'verification_token',
            //'user_type',
            //'user_full_name',
            // ['attribute' => 'userIsAdmin',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->userIsAdmin == 1 ? 'Yes' : 'No';
            //     }
            // ],
            // ['attribute' => 'userIsSupport',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->userIsSupport == 1 ? 'Yes' : 'No';
            //     }
            // ],
            // ['attribute' => 'userPayment',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->userPayment == 1 ? 'Yes' : 'No';
            //     }
            // ],
            // ['attribute' => 'company_admin',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->company_admin == 1 ? 'Yes' : 'No';
            //     }
            // ],
            // ['attribute' => 'created_at',
            //     'format' => ['date', 'dd/MM/y'],
            // ],
            // 'maxSession',
            // ['attribute' => 'is_subscribed',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->is_subscribed == 1 ? 'Yes' : 'No';
            //     }
            // ],
            //  ['attribute' => 'is_subscribed',
            //     'filter' => array("1" => "Yes", "0" => "No"),
            //     'value' => function ($model) {
            //         return $model->is_subscribed == 1 ? 'Yes' : 'No';
            //     }
            // ],
            // 'email_count',
            // [   'attribute' => 'last_email_date',
            //     'format' => ['date','dd/MM/y HH:mm:ss'],
            //     'filter' => \common\models\client::getLastLoginList(),
            // ],


            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>

</div>

<script>
    let keys = [];
    $('.select-on-check-all, .checkbox-row').change(function(){
        keys = $('#grid').yiiGridView('getSelectedRows');
    });

    $('#email-users').click(function(){
        window.location.href = "/backend/web/email-users/bulk-mail-out/?selected=" + JSON.stringify(keys);
    });
    $('#bulletins').click(function(){
        window.location.href = "/backend/web/bullet-boards/index/?selected=" + JSON.stringify(keys);
    });

    
    $("#email-select-users").click(function(){

           
            const confirmation = confirm("Are you sure you want to email the credentials to the selected users?");
            let userIds = [];

            if (confirmation) {

                
            keys.forEach((element) => {
              userIds.push(element);
            });


                $.ajax({
                    async: false,
                    url: '<?= Url::to(['user/ajax-send-credentials']) ?>',
                    type: 'post',
                    data: { userIds },
                    success: function (response) {
                        alert(response);
                    },
                    error: function () {
                        console.log("error");
                    }
                });

                return false;
            }
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
