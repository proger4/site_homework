<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require __DIR__ . '/../vendor/yiisoft/yii/framework/yii.php';

Yii::createWebApplication(__DIR__ . '/../app/protected/config/main.php')->run();
