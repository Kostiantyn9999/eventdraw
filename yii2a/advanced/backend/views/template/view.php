<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $modelSubTemplate common\models\Subtemplates */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="template-view">

        <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Set image', ['image', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
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
            'templateName',
            'xmlCode:ntext',
            [   'attribute' => 'templateActive',
                'value' => function($model) { return $model->templateActive == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'templateDefault',
                'value' => function($model) { return $model->templateDefault == 1 ? 'Yes' : 'No';}
            ],
            [   'attribute' => 'shadow',
                'value' => function($model) { return $model->shadow == 1 ? 'Yes' : 'No';}
            ],
            [
                'attribute'=>'clientid',
                'format'=>'text',
                'value'=>function($data){
                    return $data->getClientName();
                },
            ],
            [   'attribute' => 'created_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            [   'attribute' => 'updated_at',
                'format' => ['date','dd/MM/Y HH:mm:ss'],
            ],
            ['label' => 'Preview image',

                'format' => ['image',['width'=>'140']],

                'value'=>function($model){

                    return('@web/templates/images/'.$model->image);

                },

            ],
        ],
    ]) ?>

</div>

<!--  sub templates-->
<div class="panel panel-default">
    <div class="panel-heading">
        <h4>
            <i class="glyphicon glyphicon-book"></i> Sub Template list
        </h4>
    </div>
    <div class="panel-body">
        <div class="container-items"><!-- widgetBody -->
            <!-- show list of subtemplates-->
            <?php foreach ($modelSubTemplate as $i => $modelSubTemplates): ?>

                <div class="row">
                    <?= Html::encode($modelSubTemplates->subtemplatename) ?>
                </div><!-- .row -->

            <?php endforeach; ?>
        </div>
    </div>
</div>