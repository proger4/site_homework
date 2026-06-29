<?php

declare(strict_types=1);

return [
    'id' => 'vibecoding-keywords-web',
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'controllerNamespace' => 'App\\Controller',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'vibecoding-keyword-mvp-local-key',
            'enableCsrfValidation' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'health' => 'site/health',
                'upload' => 'keyword/upload',
                'admin/keywords' => 'keyword/admin',
                'preview' => 'keyword/preview',
                'ai-preview' => 'keyword/ai-preview',
                'export' => 'keyword/export',
            ],
        ],
    ],
];
