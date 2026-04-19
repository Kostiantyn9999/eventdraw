<?php

/* @var $this yii\web\View */
/* @var $isSubscribed bool */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $isSubscribed ? 'Subscribed Successfully!' : 'Unsubscribed!';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isSubscribed) : ?>
        <div class="alert alert-success">
            Congratulations, you have successfully subscribed to our emails :)
        </div>
    <?php else : ?>
        <div class="alert alert-danger">
            You have successfully unsubscribed to our emails, we are sad to see you go :(
        </div>
    <?php endif; ?>

</div>
