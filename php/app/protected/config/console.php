<?php

$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');
$dbDsn = getenv('DB_DSN') ?: 'sqlite:' . dirname(__DIR__, 3) . '/database/app.sqlite';

$config = [
    'basePath' => dirname(__DIR__),
    'name' => 'Yii1 Comments Console',
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
        'userRepository' => [
            'class' => 'UserRepository',
        ],
    ],
    'commandMap' => [
        'migrate' => [
            'class' => 'system.cli.commands.MigrateCommand',
            'migrationPath' => 'application.migrations',
            'migrationTable' => 'yii_migration',
        ],
    ],
];

$localConfig = __DIR__ . '/console.local.php';

if (is_file($localConfig)) {
    $config = CMap::mergeArray($config, require $localConfig);
}

return $config;
