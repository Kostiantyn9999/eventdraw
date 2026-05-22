<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $modelUsers common\models\User */
/* @var $modelTemplate common\models\Template */
/* @var $modelStencil common\models\ClientStencils */
/* @var $user common\models\User */
/* @var $userTemplate common\models\UserTemplates */
/* @var $userStencil common\models\UserStencils */
/* @var $userSettings common\models\Usersettings */

$this->title = 'Update Client: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelUsers' => $modelUsers,
        'modelTemplate' => $modelTemplate,
        'modelStencil' => $modelStencil,
        'user' => $user,
        'userTemplate' => $userTemplate,
        'userStencil' => $userStencil,
        'userSettings' => $userSettings,
    ]) ?>

</div>
