<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Template */

$this->title = 'Update Bulletin Template';
$this->params['breadcrumbs'][] = ['label' => 'Template Change', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>
<div class="template-create">

    <h1><?= Html::encode($getData->template_name) ?></h1>

    <?= $this->render('_form_update', [
        'getData' => $getData,
    ]) ?>

</div>
