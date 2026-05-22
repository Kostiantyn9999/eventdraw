<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $modelTemplate common\models\Template */
/* @var $modelStencil common\models\Stencil */
/* @var $modelSettings common\models\Usersettings */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update User: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTemplate' => $modelTemplate,
        'modelStencil' => $modelStencil,
        'modelSettings' => $modelSettings,
    ]) ?>
</div>
