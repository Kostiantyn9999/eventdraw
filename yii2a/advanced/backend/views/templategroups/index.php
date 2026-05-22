<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TemplategroupsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Template Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="templategroups-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Template Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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

            'parent_id',
            'groupname',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>


</div>
