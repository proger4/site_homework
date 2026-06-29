<?php

declare(strict_types=1);

use App\KeywordStorage\SqliteKeywordStorage;
use App\User\AdminIdentity;
use App\User\AdminUserRepository;
use yii\web\JsonResponseFormatter;
use yii\web\Response;

$root = dirname(__DIR__);

return [
    'id' => 'vibecoding-keywords-web',
    'basePath' => $root,
    'runtimePath' => $root . '/runtime',
    'vendorPath' => $root . '/vendor',
    'controllerNamespace' => 'App\\Controller',
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'vibecoding-keyword-mvp-local-key',
            'enableCsrfValidation' => false,
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => SqliteKeywordStorage::defaultDsn(),
        ],
        'adminUsers' => [
            'class' => AdminUserRepository::class,
        ],
        'user' => [
            'identityClass' => AdminIdentity::class,
            'loginUrl' => ['/auth/login'],
        ],
        'response' => [
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => true,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'health' => 'site/health',
                'login' => 'auth/login',
                'logout' => 'auth/logout',
                'upload' => 'keyword/upload',
                'admin/keywords' => 'keyword/admin',
                'preview' => 'keyword/preview',
                'ai-preview' => 'keyword/ai-preview',
                'export' => 'keyword/export',
            ],
        ],
        'log' => [
            'targets' => [],
        ],
    ],
];
