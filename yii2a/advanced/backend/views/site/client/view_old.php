<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-view">

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
            'clientName',
            'clientEmail:email',
            [   'attribute' => 'clientActive',
                'value' => function($model) { return $model->clientActive == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'clientPayment',
                'value' => function($model) { return $model->clientPayment == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
        ],
    ]) ?>

</div>
