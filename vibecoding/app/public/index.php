<?php

declare(strict_types=1);

defined('YII_DEBUG') || define('YII_DEBUG', getenv('YII_DEBUG') === '1');
defined('YII_ENV') || define('YII_ENV', getenv('YII_ENV') ?: 'prod');

require dirname(__DIR__) . '/bootstrap.php';

$config = require dirname(__DIR__) . '/config/web.php';

(new yii\web\Application($config))->run();
