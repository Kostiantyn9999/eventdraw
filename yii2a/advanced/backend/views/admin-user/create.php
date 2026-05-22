<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $modelTemplate common\models\Template */
/* @var $modelStencil common\models\Stencil */
/* @var $modelSettings common\models\Usersettings */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTemplate' => $modelTemplate,
        'modelStencil' => $modelStencil,
        'modelSettings' => $modelSettings,
    ]) ?>
</div>
