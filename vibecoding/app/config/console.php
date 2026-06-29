<?php

declare(strict_types=1);

use App\KeywordStorage\SqliteKeywordStorage;
use App\User\AdminUserRepository;

$root = dirname(__DIR__);

return [
    'id' => 'vibecoding-keywords-console',
    'basePath' => $root,
    'runtimePath' => $root . '/runtime',
    'vendorPath' => $root . '/vendor',
    'aliases' => [
        '@App' => $root . '/src',
    ],
    'controllerNamespace' => 'App\\Command',
    'components' => [
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => SqliteKeywordStorage::defaultDsn(),
        ],
        'adminUsers' => [
            'class' => AdminUserRepository::class,
        ],
    ],
];
