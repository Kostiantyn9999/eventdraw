<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Template */

$this->title = 'Create Email Template';
$this->params['breadcrumbs'][] = ['label' => 'Email Template', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>
<div class="template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_update', [
        'getData' => $getData,
    ]) ?>

</div>
