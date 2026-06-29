<?php

declare(strict_types=1);

use App\KeywordStorage\SqliteKeywordStorage;
use App\User\AdminUserRepository;

return [
    'id' => 'vibecoding-keywords-console',
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'vendorPath' => dirname(__DIR__) . '/vendor',
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
