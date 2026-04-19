<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Stencil */

$this->title = 'Create Stencil';
$this->params['breadcrumbs'][] = ['label' => 'Stencils', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stencil-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
