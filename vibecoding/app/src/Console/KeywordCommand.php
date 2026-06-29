<?php

declare(strict_types=1);

namespace App\Console;

use App\KeywordExport\GoogleAdsCsvExporter;
use App\KeywordExport\GoogleAdsExportRow;
use App\KeywordExport\GoogleAdsExportValidator;
use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordImportResult;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Exception\KeywordImportException;
use App\KeywordImport\Exception\MissingRequiredFieldException;
use App\KeywordImport\Provider\KeywordImportProviderInterface;
use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\KeywordPipeline\Contract\AdCopyGeneratorInterface;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\KeywordGroupBuilder;
use App\KeywordStorage\SqliteKeywordStorage;

final class KeywordCommand
{
    private string $appRoot;
    private KeywordImportProviderInterface $provider;
    private GoogleAdsCsvExporter $exporter;
    private AdCopyGeneratorInterface $templateAdCopyGenerator;
    private AdCopyGeneratorInterface $aiAdCopyGenerator;
    private KeywordGroupBuilder $groupBuilder;
    private \Closure $storageFactory;

    /**
     * @param \Closure(): SqliteKeywordStorage $storageFactory
     */
    public function __construct(
        string $appRoot,
        KeywordImportProviderInterface $provider,
        GoogleAdsCsvExporter $exporter,
        AdCopyGeneratorInterface $templateAdCopyGenerator,
        AdCopyGeneratorInterface $aiAdCopyGenerator,
        KeywordGroupBuilder $groupBuilder,
        \Closure $storageFactory
    ) {
        $this->appRoot = $appRoot;
        $this->provider = $provider;
        $this->exporter = $exporter;
        $this->templateAdCopyGenerator = $templateAdCopyGenerator;
        $this->aiAdCopyGenerator = $aiAdCopyGenerator;
        $this->groupBuilder = $groupBuilder;
        $this->storageFactory = $storageFactory;
    }

    public function importSamples(): int
    {
        $result = $this->readSampleRows();
        $this->printResult($result);

        $stored = $this->storage()->replaceRows($result->rows());
        echo 'Rows stored in SQLite: ' . $stored . "\n";

        return 0;
    }

    public function initDatabase(): int
    {
        $storage = $this->storage();
        $storage->initialize();

        echo 'SQLite database ready: ' . SqliteKeywordStorage::defaultDsn() . "\n";
        echo 'Rows stored: ' . $storage->rowCount() . "\n";

        return 0;
    }

    public function exportSamples(?string $targetPath): int
    {
        $context = new AdCopyGenerationContext();
        $exportRows = [];

        foreach ($this->activeKeywordGroups() as $group) {
            foreach ($this->copiesByKeyword($this->templateAdCopyGenerator->generate($group, $context)) as $keyword => $copy) {
                foreach ($group->rows() as $row) {
                    if ($row->keywordText() === $keyword) {
                        $exportRows[] = GoogleAdsExportRow::fromNormalizedKeywordRowAndAdCopy($row, $copy);
                    }
                }
            }
        }

        $path = $targetPath ?? $this->appRoot . '/runtime/export/google_ads_import.csv';
        $report = $this->exporter->export($exportRows, $path);

        echo 'Export written: ' . $report->path() . "\n";
        echo 'Rows exported: ' . $report->rowCount() . "\n";

        if ($report->hasErrors()) {
            echo "Validation errors:\n";
            foreach ($report->errors() as $error) {
                echo '  - ' . $error . "\n";
            }

            return 1;
        }

        echo "Export looks valid.\n";

        return 0;
    }

    /**
     * @param array<int, string> $args
     */
    public function aiPreview(array $args): int
    {
        $options = $this->parseOptions($args);
        $apiKey = $options['apiKey'] ?? $options['api-key'] ?? getenv('OPENROUTER_API_KEY') ?: null;
        $model = $options['model'] ?? getenv('OPENROUTER_MODEL') ?: 'openai/gpt-4.1-mini';

        $context = new AdCopyGenerationContext(
            is_string($apiKey) && $apiKey !== '' ? $apiKey : null,
            (string) $model
        );
        echo "generator | language | keyword | headline_1 | description_1\n";
        echo "----------|----------|---------|------------|--------------\n";

        foreach ($this->activeKeywordGroups() as $group) {
            foreach ($this->aiAdCopyGenerator->generate($group, $context) as $copy) {
                echo implode(' | ', [
                    $copy->generator(),
                    $group->language(),
                    $copy->keyword(),
                    $copy->headline1(),
                    $copy->description1(),
                ]) . "\n";
            }
        }

        return 0;
    }

    public function validateExport(?string $targetPath): int
    {
        $path = $targetPath ?? $this->appRoot . '/runtime/export/google_ads_import.csv';

        if (!is_file($path)) {
            fwrite(STDERR, 'Export file not found: ' . $path . "\n");

            return 1;
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            fwrite(STDERR, 'Unable to open export file: ' . $path . "\n");

            return 1;
        }

        $header = fgetcsv($handle, 0, ',', '"', '\\');
        $missing = array_values(array_diff(GoogleAdsExportValidator::COLUMNS, is_array($header) ? $header : []));
        $rows = [];

        if ($missing !== []) {
            fclose($handle);
            echo 'Missing columns: ' . implode(', ', $missing) . "\n";

            return 1;
        }

        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }

            $rows[] = array_combine(GoogleAdsExportValidator::COLUMNS, array_pad($data, count(GoogleAdsExportValidator::COLUMNS), ''));
        }

        fclose($handle);

        $errors = (new GoogleAdsExportValidator())->validate($rows);

        if ($errors !== []) {
            echo implode("\n", $errors) . "\n";

            return 1;
        }

        echo "Export looks valid.\n";

        return 0;
    }

    public function smoke(): int
    {
        $result = $this->readSampleRows();
        $rows = $result->rows();

        $this->assertSame(8, count($rows), 'sample row count');

        foreach ($rows as $row) {
            if (!$row instanceof KeywordImportRow) {
                throw new \RuntimeException('Provider returned a non-KeywordImportRow item.');
            }

            $this->assertNotEmpty($row->keywordText(), 'keywordText');
            $this->assertNotEmpty($row->originalKeyword(), 'originalKeyword');
            $this->assertNotEmpty($row->language(), 'language');
            $this->assertNotEmpty($row->targetUrl(), 'targetUrl');
            $this->assertNotEmpty($row->sourceFile(), 'sourceFile');
            $this->assertTrue($row->rowNumber() > 0, 'rowNumber');
            $this->assertTrue($row->rawPayload() !== [], 'rawPayload');
        }

        $this->assertSame(2, $result->countFor(KeywordSource::googleAds()), 'google_ads count');
        $this->assertSame(4, $result->countFor(KeywordSource::searchConsole()), 'search_console count');
        $this->assertSame(1, $result->countFor(KeywordSource::ahrefsOrganic()), 'ahrefs_organic count');
        $this->assertSame(1, $result->countFor(KeywordSource::ahrefsPaid()), 'ahrefs_paid count');

        $stored = $this->storage()->replaceRows($rows);
        $this->assertSame(8, $stored, 'stored row count');
        $this->assertSame(8, $this->storage()->rowCount(), 'SQLite row count');

        $this->assertImportErrorContract();

        $targetPath = $this->appRoot . '/runtime/export/google_ads_import.csv';
        $exportCode = $this->exportSamples($targetPath);

        if ($exportCode !== 0) {
            return $exportCode;
        }

        echo "Smoke passed.\n";

        return 0;
    }

    private function readSampleRows(): KeywordImportResult
    {
        $result = new KeywordImportResult();

        foreach ($this->sampleFiles() as $path) {
            foreach ($this->provider->rows($path) as $row) {
                $result->add($row);
            }
        }

        return $result;
    }

    private function storage(): SqliteKeywordStorage
    {
        $storage = ($this->storageFactory)();

        if (!$storage instanceof SqliteKeywordStorage) {
            throw new \RuntimeException('Storage factory must return a SqliteKeywordStorage instance.');
        }

        return $storage;
    }

    /**
     * @return array<int, KeywordGroup>
     */
    private function activeKeywordGroups(): array
    {
        return $this->groupBuilder->build($this->readSampleRows()->rows());
    }

    /**
     * @param iterable<int, AdCopy> $copies
     * @return array<string, AdCopy>
     */
    private function copiesByKeyword(iterable $copies): array
    {
        $result = [];

        foreach ($copies as $copy) {
            $result[$copy->keyword()] = $copy;
        }

        return $result;
    }

    /**
     * @param array<int, string> $args
     * @return array<string, string>
     */
    private function parseOptions(array $args): array
    {
        $options = [];

        for ($i = 0, $count = count($args); $i < $count; $i++) {
            $arg = $args[$i];

            if (strpos($arg, '--') !== 0) {
                continue;
            }

            $option = substr($arg, 2);

            if (strpos($option, '=') !== false) {
                [$name, $value] = explode('=', $option, 2);
                $options[$name] = $value;

                continue;
            }

            $next = $args[$i + 1] ?? '';

            if ($next !== '' && strpos($next, '--') !== 0) {
                $options[$option] = $next;
                $i++;
            } else {
                $options[$option] = '1';
            }
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    private function sampleFiles(): array
    {
        $dir = $this->appRoot . '/tests/fixtures/keyword-samples';

        return [
            $dir . '/google_ads_keywords.csv',
            $dir . '/search_console_queries.csv',
            $dir . '/search_console_queries.json',
            $dir . '/ahrefs_organic_keywords.csv',
            $dir . '/ahrefs_paid_keywords.csv',
        ];
    }

    private function printResult(KeywordImportResult $result): void
    {
        echo 'Imported rows: ' . $result->rowCount() . "\n";

        foreach ($result->countsBySource() as $source => $count) {
            echo '- ' . $source . ': ' . $count . "\n";
        }
    }

    private function assertImportErrorContract(): void
    {
        $dir = $this->appRoot . '/runtime/tmp';

        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create runtime tmp directory.');
        }

        $path = $dir . '/missing_required_google_ads.csv';
        file_put_contents($path, "keyword,language,cpc,target_url\nbroken,en,1.0,https://example.test/\n");

        try {
            foreach ($this->provider->rows($path, KeywordSource::googleAds()) as $ignored) {
            }
        } catch (MissingRequiredFieldException $e) {
            $this->assertTrue($e instanceof KeywordImportException, 'import exception inheritance');

            return;
        }

        throw new \RuntimeException('Missing required field did not raise an import error.');
    }

    private function assertSame(int $expected, int $actual, string $label): void
    {
        if ($expected !== $actual) {
            throw new \RuntimeException("Expected {$label} {$expected}, got {$actual}.");
        }
    }

    private function assertNotEmpty(string $value, string $label): void
    {
        if ($value === '') {
            throw new \RuntimeException("Expected non-empty {$label}.");
        }
    }

    private function assertTrue(bool $value, string $label): void
    {
        if (!$value) {
            throw new \RuntimeException("Assertion failed: {$label}.");
        }
    }
}
