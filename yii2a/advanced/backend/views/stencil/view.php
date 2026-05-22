<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Stencil */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Stencils', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="stencil-view">

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
                'value' => function($model) { return $model->isMasterStencil == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'stencilActive',
                'value' => function($model) { return $model->stencilActive == 1 ? 'Yes' : 'No';}
            ],
            'stencilOrder',
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
        ],
    ]) ?>

</div>
