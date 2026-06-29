<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';
if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    require __DIR__ . '/PHPUnit/Framework/TestCase.php';
}
require __DIR__ . '/Support/OpenRouterCurlMock.php';

spl_autoload_register(static function (string $class): void {
    foreach ([
        'Tests\\' => __DIR__ . '/',
        'App\\Tests\\' => __DIR__ . '/',
    ] as $prefix => $basePath) {
        if (strpos($class, $prefix) !== 0) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $path = $basePath . str_replace('\\', '/', $relative) . '.php';

        if (is_file($path)) {
            require $path;
        }
    }
});
