<?php

$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');
$dbDsn = getenv('DB_DSN') ?: 'sqlite:' . dirname(__DIR__, 3) . '/database/app.sqlite';

$config = [
    'basePath' => dirname(__DIR__),
    'name' => 'Yii1 Comments',
    'defaultController' => 'site',
    'runtimePath' => dirname(__DIR__, 3) . '/runtime',
    'import' => [
        'application.models.*',
        'application.components.*',
    ],
    'components' => [
        'db' => [
            'connectionString' => $dbDsn,
            'username' => $dbUsername === false ? null : $dbUsername,
            'password' => $dbPassword === false ? null : $dbPassword,
            'emulatePrepare' => true,
        ],
        'webSocketNotifier' => [
            'class' => 'WebSocketNotifier',
            'host' => getenv('WS_INTERNAL_HOST') ?: '127.0.0.1',
            'port' => getenv('WS_PORT') ?: '3001',
        ],
        'userRepository' => [
            'class' => 'UserRepository',
        ],
        'user' => [
            'allowAutoLogin' => false,
            'loginUrl' => ['admin/login'],
        ],
        'assetManager' => [
            'basePath' => dirname(__DIR__, 3) . '/public/assets',
            'baseUrl' => '/assets',
        ],
        'urlManager' => [
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'comment/create' => 'comment/create',
                'comment/delete/<id:\d+>' => 'comment/delete',
                'admin/login' => 'admin/login',
                'admin/logout' => 'admin/logout',
                'admin/comments' => 'admin/comments',
                'admin/comments/update/<id:\d+>' => 'admin/update',
                'admin/comments/delete/<id:\d+>' => 'admin/delete',
                'admin' => 'admin/comments',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
];

$localConfig = __DIR__ . '/main.local.php';

if (is_file($localConfig)) {
    $config = CMap::mergeArray($config, require $localConfig);
}

return $config;
