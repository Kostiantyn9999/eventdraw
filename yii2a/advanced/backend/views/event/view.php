<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'eventName',
            [
                'attribute'=>'userid',
                'format'=>'text',
                'value'=>function($data){
                    return $data->getUserName();
                },
            ],
            'xmlCode:ntext',
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'eventActive',
                'value' => function($model) { return $model->eventActive == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'event_shared',
                'value' => function($model) { return $model->event_shared == 1 ? 'Yes' : 'No';}
            ],
            ['label' => 'Preview image',

            'format' => ['image',['width'=>'140']],

             'value'=>function($data){
                    return $data->imageCode;
                },

        ],

        ],
    ]) ?>

</div>
