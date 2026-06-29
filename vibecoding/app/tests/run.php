<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$declaredBefore = get_declared_classes();

foreach (testFiles(__DIR__ . '/Unit') as $file) {
    require $file;
}

$testClasses = array_values(array_filter(
    array_diff(get_declared_classes(), $declaredBefore),
    static fn (string $class): bool => substr($class, -4) === 'Test'
));

$passed = 0;
$failed = 0;

foreach ($testClasses as $class) {
    $test = new $class();
    $reflection = new ReflectionClass($class);

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (strpos($method->getName(), 'test') !== 0) {
            continue;
        }

        $provider = providerFor($method);
        $datasets = $provider !== null ? $test->{$provider}() : ['default' => []];

        foreach ($datasets as $name => $arguments) {
            if (method_exists($test, '__resetExpectation')) {
                $test->__resetExpectation();
            }

            try {
                callSetUp($test, $reflection);
                $test->{$method->getName()}(...array_values($arguments));

                if (method_exists($test, '__verifyExpectation')) {
                    $test->__verifyExpectation(null);
                }

                $passed++;
                echo '.';
            } catch (Throwable $e) {
                try {
                    if (method_exists($test, '__verifyExpectation')) {
                        $test->__verifyExpectation($e);
                        $passed++;
                        echo '.';

                        continue;
                    }

                    throw $e;
                } catch (Throwable $failure) {
                    $failed++;
                    echo "F\n";
                    echo $class . '::' . $method->getName() . ' [' . $name . "]\n";
                    echo $failure->getMessage() . "\n";
                }
            }
        }
    }
}

echo "\nPassed: {$passed}; Failed: {$failed}\n";

exit($failed === 0 ? 0 : 1);

function providerFor(ReflectionMethod $method): ?string
{
    $doc = $method->getDocComment();

    if (!is_string($doc)) {
        return null;
    }

    if (preg_match('/@dataProvider\s+([A-Za-z0-9_]+)/', $doc, $matches) !== 1) {
        return null;
    }

    return $matches[1];
}

function callSetUp(object $test, ReflectionClass $reflection): void
{
    if (!$reflection->hasMethod('setUp')) {
        return;
    }

    $setUp = $reflection->getMethod('setUp');
    if (PHP_VERSION_ID < 80100) {
        $setUp->setAccessible(true);
    }
    $setUp->invoke($test);
}

/**
 * @return array<int, string>
 */
function testFiles(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        if (substr($file->getFilename(), -8) === 'Test.php') {
            $files[] = $file->getPathname();
        }
    }

    sort($files);

    return $files;
}
