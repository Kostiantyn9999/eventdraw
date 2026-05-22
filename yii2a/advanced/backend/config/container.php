<?php

use yii\di\Container;

$container = new Container;

// register a class name as is. This can be skipped.
$container->set('backend\services\users\UserSettingsService');
$container->set('backend\services\users\UserTemplatesService');
$container->set('backend\services\users\UserStencilsService');
