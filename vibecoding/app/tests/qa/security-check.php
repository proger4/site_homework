<?php

declare(strict_types=1);

$appRoot = dirname(__DIR__, 2);
$repoRoot = dirname($appRoot);
$maxLines = 500;
$failures = [];

$sourceFiles = collectFiles($repoRoot, [
    '.git',
    'app/vendor',
    'app/var',
    'app/runtime',
    'database',
    'generated',
]);

foreach ($sourceFiles as $file) {
    $relative = relativePath($repoRoot, $file);

    if (in_array($relative, ['.env', '.env.local'], true)) {
        continue;
    }

    $contents = (string) file_get_contents($file);

    if (!isBinary($contents)) {
        $lineCount = lineCount($contents);

        if ($lineCount > $maxLines) {
            $failures[] = "{$relative} has {$lineCount} lines; limit is {$maxLines}.";
        }
    }

    foreach (secretFindings($contents, configuredOpenRouterKey()) as $finding) {
        $failures[] = "{$relative} contains {$finding}.";
    }
}

$artifactRoots = [
    $repoRoot . '/database',
    $appRoot . '/runtime/export',
    $appRoot . '/runtime/logs',
];

foreach ($artifactRoots as $artifactRoot) {
    if (!is_dir($artifactRoot)) {
        continue;
    }

    foreach (collectFiles($artifactRoot, []) as $file) {
        $contents = (string) file_get_contents($file);

        foreach (secretFindings($contents, configuredOpenRouterKey()) as $finding) {
            $failures[] = relativePath($repoRoot, $file) . " contains {$finding}.";
        }
    }
}

if ($failures !== []) {
    fwrite(STDERR, "QA security checklist failed:\n");

    foreach ($failures as $failure) {
        fwrite(STDERR, ' - ' . $failure . "\n");
    }

    exit(1);
}

echo "QA security checklist passed.\n";
echo "- Source/config/doc/test files are at or below {$maxLines} lines.\n";
echo "- No real-looking OpenRouter API key was found outside local env files.\n";
echo "- Generated database/export/log artifacts contain no configured OpenRouter API key.\n";

/**
 * @param array<int, string> $excludedDirs
 * @return array<int, string>
 */
function collectFiles(string $root, array $excludedDirs): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $file) use ($root, $excludedDirs): bool {
                if (!$file->isDir()) {
                    return true;
                }

                $relative = relativePath($root, $file->getPathname());

                return !in_array($relative, $excludedDirs, true);
            }
        )
    );

    foreach ($iterator as $file) {
        if ($file instanceof SplFileInfo && $file->isFile()) {
            $files[] = $file->getPathname();
        }
    }

    sort($files);

    return $files;
}

function relativePath(string $root, string $path): string
{
    $root = rtrim(str_replace('\\', '/', $root), '/');
    $path = str_replace('\\', '/', $path);

    if (strpos($path, $root . '/') === 0) {
        return substr($path, strlen($root) + 1);
    }

    return $path;
}

function lineCount(string $contents): int
{
    if ($contents === '') {
        return 0;
    }

    $lines = substr_count($contents, "\n");

    return substr($contents, -1) === "\n" ? $lines : $lines + 1;
}

function isBinary(string $contents): bool
{
    return strpos($contents, "\0") !== false;
}

/**
 * @return array<int, string>
 */
function secretFindings(string $contents, ?string $configuredKey): array
{
    $findings = [];

    if ($configuredKey !== null && $configuredKey !== '' && strpos($contents, $configuredKey) !== false) {
        $findings[] = 'configured OpenRouter API key';
    }

    if (preg_match('/\bsk-or-(?:v1-)?[A-Za-z0-9][A-Za-z0-9_-]{24,}\b/', $contents) === 1) {
        $findings[] = 'real-looking OpenRouter API key pattern';
    }

    return array_values(array_unique($findings));
}

function configuredOpenRouterKey(): ?string
{
    $value = getenv('OPENROUTER_API_KEY');

    if (!is_string($value) || trim($value) === '') {
        return null;
    }

    return trim($value);
}
