<?php

declare(strict_types=1);

foreach ([dirname(__DIR__) . '/.env', __DIR__ . '/.env'] as $envPath) {
    if (!is_file($envPath)) {
        continue;
    }

    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($name !== '' && getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

$vendorAutoload = __DIR__ . '/vendor/autoload.php';

if (is_file($vendorAutoload)) {
    require $vendorAutoload;

    $yii = __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

    if (!class_exists(\Yii::class, false) && is_file($yii)) {
        require $yii;
    }

    return;
}

throw new \RuntimeException('Composer dependencies are missing. Run composer install in vibecoding/app.');
