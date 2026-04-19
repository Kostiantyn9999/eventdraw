<?php

/* @var $this yii\web\View */

use common\models\UserTemplates;
use yii\helpers\Html;
use yii\web\View;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

$this->title = 'Guest Allocation link info';

$request = Yii::$app->request;
$linkID = $request->post('linkid');


$linkName = '';
$linkExpiryDate = '';
$linkPassword = '';
$linkXML = '';
$linkNotes = '';

if ($linkID)
{
$linkInfo = \common\models\GuestAllocation::findLink($linkID);
if ($linkInfo)
  {
      $linkName = $linkInfo->allocationname;
      $linkExpiryDate = $linkInfo->expirydate;
      $linkPassword = $linkInfo->password;
      $linkXML = $linkInfo->xmlcode;
      $linkNotes = $linkInfo->comments;
  }
}



?>

<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=5,IE=9"/><![endif]-->
<!DOCTYPE html>
<html>
<head>
    <title>Guest Allocation link info</title>


    <div id="data-linkname" class='hidden'>
        <?= $linkName ?>
    </div>
    <div id="data-expirydate" class='hidden'>
        <?= $linkExpiryDate ?>
    </div>
    <div id="data-password" class='hidden'>
        <?= $linkPassword ?>
    </div>
    <div id="data-linkxml" class='hidden'>
        <?= $linkXML ?>
    </div>
    <div id="data-linknotes" class='hidden'>
        <?= $linkNotes ?>
    </div>
