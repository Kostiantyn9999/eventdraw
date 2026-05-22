<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\StencilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stencils';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stencil-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Stencil', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'stencilName',

            [
            'label' => 'XML',
            'format' => 'raw',
            'attribute' => 'stencilXML',
            'value' => function ($data) {

                return Html::a($data['stencilXML'], ['download' , 'id' => $data['stencilXML']]);

            },

            ],
            [   'attribute' => 'isMasterStencil',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->isMasterStencil== 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'stencilActive',
                'filter'=>array("1"=>"Yes","0"=>"No"),
                'value' => function($model) { return $model->stencilActive== 1 ? 'Yes' : 'No';}
            ],
            'stencilOrder',
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
