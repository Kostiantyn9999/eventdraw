<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apprelease */

$this->title = 'Create Apprelease';
$this->params['breadcrumbs'][] = ['label' => 'Appreleases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apprelease-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
