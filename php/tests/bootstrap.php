<?php

defined('YII_DEBUG') || define('YII_DEBUG', false);
defined('YII_ENABLE_EXCEPTION_HANDLER') || define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') || define('YII_ENABLE_ERROR_HANDLER', false);

$root = dirname(__DIR__);
$runtimePath = sys_get_temp_dir() . '/yii1-comments-tests-runtime';

if (!is_dir($runtimePath)) {
    mkdir($runtimePath, 0777, true);
}

require_once $root . '/vendor/autoload.php';
require_once $root . '/vendor/yiisoft/yii/framework/yii.php';

Yii::createWebApplication([
    'basePath' => $root . '/app/protected',
    'name' => 'Yii1 Comments Tests',
    'runtimePath' => $runtimePath,
    'import' => [
        'application.models.*',
        'application.components.*',
    ],
    'components' => [
        'db' => [
            'connectionString' => 'sqlite::memory:',
            'emulatePrepare' => true,
        ],
        'user' => [
            'class' => 'CWebUser',
            'allowAutoLogin' => false,
        ],
        'userRepository' => [
            'class' => 'UserRepository',
        ],
        'webSocketNotifier' => [
            'class' => 'WebSocketNotifier',
            'host' => '127.0.0.1',
            'port' => '1',
        ],
    ],
]);

require_once $root . '/app/protected/migrations/m250000_000001_create_comments_table.php';
require_once $root . '/app/protected/migrations/m250000_000002_create_users_table.php';

(new m250000_000001_create_comments_table())->up();
(new m250000_000002_create_users_table())->up();
