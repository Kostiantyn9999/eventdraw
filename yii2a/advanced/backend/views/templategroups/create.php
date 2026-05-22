<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Templategroups */

$this->title = 'Create Templategroups';
$this->params['breadcrumbs'][] = ['label' => 'Templategroups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="templategroups-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
