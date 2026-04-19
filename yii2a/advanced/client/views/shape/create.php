<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MomentusShape */

$this->title = 'Create Shape';
$this->params['breadcrumbs'][] = ['label' => 'Shapes', 'url' => ['shape-manager']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shape-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
