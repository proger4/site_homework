<?php

declare(strict_types=1);

namespace App\KeywordRuntime;

use App\KeywordExport\GoogleAdsCsvExporter;
use App\KeywordExport\GoogleAdsExportReport;
use App\KeywordExport\GoogleAdsExportRow;
use App\KeywordExport\GoogleAdsExportValidator;
use App\KeywordImport\Domain\KeywordImportResult;
use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Provider\KeywordImportProviderInterface;
use App\KeywordImport\Provider\SampleKeywordImportProvider;
use App\KeywordPipeline\AdCopy\OpenRouterAdCopyGenerator;
use App\KeywordPipeline\AdCopy\OpenRouterClient;
use App\KeywordPipeline\AdCopy\TemplateAdCopyGenerator;
use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\KeywordPipeline\Contract\AdCopyGeneratorInterface;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\KeywordGroupBuilder;
use App\KeywordStorage\SqliteKeywordStorage;

final class KeywordRuntime
{
    private string $appRoot;
    private KeywordImportProviderInterface $provider;
    private SqliteKeywordStorage $storage;
    private KeywordGroupBuilder $groupBuilder;
    private GoogleAdsCsvExporter $exporter;
    private AdCopyGeneratorInterface $templateGenerator;
    private AdCopyGeneratorInterface $aiGenerator;

    public function __construct(
        string $appRoot,
        KeywordImportProviderInterface $provider,
        SqliteKeywordStorage $storage,
        KeywordGroupBuilder $groupBuilder,
        GoogleAdsCsvExporter $exporter,
        AdCopyGeneratorInterface $templateGenerator,
        AdCopyGeneratorInterface $aiGenerator
    ) {
        $this->appRoot = $appRoot;
        $this->provider = $provider;
        $this->storage = $storage;
        $this->groupBuilder = $groupBuilder;
        $this->exporter = $exporter;
        $this->templateGenerator = $templateGenerator;
        $this->aiGenerator = $aiGenerator;
    }

    public static function createDefault(string $appRoot): self
    {
        $templateGenerator = new TemplateAdCopyGenerator();

        return new self(
            $appRoot,
            SampleKeywordImportProvider::createDefault(),
            SqliteKeywordStorage::fromEnvironment(),
            new KeywordGroupBuilder(),
            new GoogleAdsCsvExporter(new GoogleAdsExportValidator()),
            $templateGenerator,
            new OpenRouterAdCopyGenerator(new OpenRouterClient(), $templateGenerator)
        );
    }

    public function storage(): SqliteKeywordStorage
    {
        return $this->storage;
    }

    public function exportPath(): string
    {
        return $this->appRoot . '/runtime/export/google_ads_import.csv';
    }

    public function importSamples(): KeywordImportResult
    {
        return $this->replaceRowsFromFiles($this->sampleFiles(), null);
    }

    public function importUploadedFile(string $path, KeywordSource $source): KeywordImportResult
    {
        return $this->replaceRowsFromFiles([$path], $source);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminRows(): array
    {
        return $this->storage->allRows();
    }

    /**
     * @return array<int, KeywordGroup>
     */
    public function groups(): array
    {
        return $this->groupBuilder->groupsFromNormalizedRows($this->storage->activeNormalizedRows());
    }

    /**
     * @return array<int, GoogleAdsExportRow>
     */
    public function previewRows(bool $useAi, ?string $apiKey = null, ?string $model = null): array
    {
        $generator = $useAi ? $this->aiGenerator : $this->templateGenerator;
        $context = new AdCopyGenerationContext(
            is_string($apiKey) && trim($apiKey) !== '' ? trim($apiKey) : null,
            is_string($model) && trim($model) !== '' ? trim($model) : 'openai/gpt-4.1-mini'
        );
        $rows = [];

        foreach ($this->groups() as $group) {
            $copies = $this->copiesByKeyword($generator->generate($group, $context));
            $fallbackCopies = $this->copiesByKeyword($this->templateGenerator->generate($group, new AdCopyGenerationContext()));

            foreach ($group->rows() as $row) {
                $copy = $copies[$row->keywordText()] ?? $fallbackCopies[$row->keywordText()] ?? null;

                if ($copy === null) {
                    continue;
                }

                $rows[] = GoogleAdsExportRow::fromNormalizedKeywordRowAndAdCopy($row, $copy);
            }
        }

        return $rows;
    }

    public function exportCsv(?string $path = null): GoogleAdsExportReport
    {
        return $this->exporter->export($this->previewRows(false), $path ?? $this->exportPath());
    }

    public function hasOpenRouterKey(): bool
    {
        $key = getenv('OPENROUTER_API_KEY');

        return is_string($key) && trim($key) !== '';
    }

    /**
     * @return array<int, string>
     */
    public function sampleFiles(): array
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

    /**
     * @param array<int, string> $paths
     */
    private function replaceRowsFromFiles(array $paths, ?KeywordSource $source): KeywordImportResult
    {
        $result = new KeywordImportResult();

        foreach ($paths as $path) {
            foreach ($this->provider->rows($path, $source) as $row) {
                $result->add($row);
            }
        }

        $this->storage->replaceProcessedRows($this->groupBuilder->process($result->rows()));

        return $result;
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
}
