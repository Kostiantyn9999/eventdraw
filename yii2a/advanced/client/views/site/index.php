<?php

/* @var $this yii\web\View */

$this->title = 'Admin EventDraw';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?php echo Yii::$app->user->identity->getClientName() ?> Admin page</h1>
    </div>

    <div class="body-content">
        <div class="row">

            <div class="col-lg-4">
                <h2>Account</h2>
                <p><a class="btn btn-default" href="../web/site/account-code">Manage Org Code &raquo;</a></p>
            </div>

            <div class="col-lg-4">
                <h2>Users</h2>
                <p><a class="btn btn-default" href="../web/user">Manage <?php echo Yii::$app->user->identity->getClientName() ?> Users &raquo;</a></p>
            </div>

        </div>
    </div>

    <div class="body-content">
        <div class="row">

            <div class="col-lg-4">
                <h2>Templates</h2>
                <p><a class="btn btn-default" href="../web/template">Manage <?php echo Yii::$app->user->identity->getClientName() ?> Templates &raquo;</a></p>
            </div>

            <div class="col-lg-4">
                <h2>Shape/Resource</h2>
                <p><a class="btn btn-default" href="../web/momentus/shape-manager">Manage <?php echo Yii::$app->user->identity->getClientName() ?> Shapes &raquo;</a></p>
            </div>

        </div>
    </div>

</div>
