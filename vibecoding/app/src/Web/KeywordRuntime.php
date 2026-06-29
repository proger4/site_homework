<?php

declare(strict_types=1);

namespace App\Web;

use App\KeywordExport\GoogleAdsCsvExporter;
use App\KeywordExport\GoogleAdsExportReport;
use App\KeywordExport\GoogleAdsExportRow;
use App\KeywordImport\Domain\KeywordImportResult;
use App\KeywordImport\Provider\SampleKeywordImportProvider;
use App\KeywordPipeline\AdCopy\OpenRouterAdCopyGenerator;
use App\KeywordPipeline\AdCopy\OpenRouterClient;
use App\KeywordPipeline\AdCopy\TemplateAdCopyGenerator;
use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\KeywordGroupBuilder;
use App\KeywordStorage\SqliteKeywordStorage;

final class KeywordRuntime
{
    private ?SqliteKeywordStorage $storage = null;
    private ?KeywordImportResult $sampleRows = null;
    private ?KeywordGroupBuilder $groupBuilder = null;

    /**
     * @return array<string, bool|int|string>
     */
    public function status(bool $forceTemplate): array
    {
        return [
            'status' => 'ok',
            'database' => SqliteKeywordStorage::defaultDsn(),
            'keyword_import_rows' => $this->storage()->rowCount(),
            'export_exists' => is_file($this->exportPath()),
            'ai_mode' => $this->hasOpenRouterKey($forceTemplate) ? 'openrouter' : 'template-fallback',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function keywordRows(): array
    {
        return $this->storage()->allRows();
    }

    /**
     * @return array<int, array{language: string, target_url: string, keywords: array<int, string>}>
     */
    public function previewGroups(): array
    {
        $result = [];

        foreach ($this->activeGroups(false) as $group) {
            $keywords = [];

            foreach ($group->rows() as $row) {
                $keywords[] = $row->keywordText();
            }

            $result[] = [
                'language' => $group->language(),
                'target_url' => $group->targetUrl(),
                'keywords' => $keywords,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function aiCopies(bool $forceTemplate): array
    {
        $context = new AdCopyGenerationContext(
            $this->hasOpenRouterKey($forceTemplate) ? (string) getenv('OPENROUTER_API_KEY') : null,
            (string) (getenv('OPENROUTER_MODEL') ?: 'openai/gpt-4.1-mini')
        );
        $templateGenerator = new TemplateAdCopyGenerator();
        $generator = new OpenRouterAdCopyGenerator(new OpenRouterClient(), $templateGenerator);
        $copies = [];

        foreach ($this->activeGroups(true) as $group) {
            foreach ($generator->generate($group, $context) as $copy) {
                $copies[] = [
                    'generator' => $copy->generator(),
                    'language' => $group->language(),
                    'keyword' => $copy->keyword(),
                    'headline_1' => $copy->headline1(),
                    'headline_2' => $copy->headline2(),
                    'headline_3' => $copy->headline3(),
                    'description_1' => $copy->description1(),
                ];
            }
        }

        return $copies;
    }

    public function exportGoogleAdsCsv(): GoogleAdsExportReport
    {
        $exportRows = [];
        $context = new AdCopyGenerationContext();
        $generator = new TemplateAdCopyGenerator();

        foreach ($this->activeGroups(true) as $group) {
            foreach ($this->copiesByKeyword($generator->generate($group, $context)) as $keyword => $copy) {
                foreach ($group->rows() as $row) {
                    if ($row->keywordText() === $keyword) {
                        $exportRows[] = GoogleAdsExportRow::fromNormalizedKeywordRowAndAdCopy($row, $copy);
                    }
                }
            }
        }

        return (new GoogleAdsCsvExporter())->export($exportRows, $this->exportPath());
    }

    private function storage(): SqliteKeywordStorage
    {
        return $this->storage ??= SqliteKeywordStorage::fromEnvironment();
    }

    /**
     * @return array<int, KeywordGroup>
     */
    private function activeGroups(bool $allowSampleFallback): array
    {
        $rows = $this->storage()->activeNormalizedRows();

        if ($rows !== []) {
            return $this->groupBuilder()->groupsFromNormalizedRows($rows);
        }

        if (!$allowSampleFallback) {
            return [];
        }

        return $this->groupBuilder()->build($this->sampleRows()->rows());
    }

    private function exportPath(): string
    {
        return \Yii::getAlias('@app') . '/runtime/export/google_ads_import.csv';
    }

    private function hasOpenRouterKey(bool $forceTemplate): bool
    {
        return !$forceTemplate
            && is_string(getenv('OPENROUTER_API_KEY'))
            && trim((string) getenv('OPENROUTER_API_KEY')) !== '';
    }

    private function sampleRows(): KeywordImportResult
    {
        if ($this->sampleRows !== null) {
            return $this->sampleRows;
        }

        $provider = SampleKeywordImportProvider::createDefault();
        $result = new KeywordImportResult();
        $dir = \Yii::getAlias('@app') . '/tests/fixtures/keyword-samples';

        foreach ([
            $dir . '/google_ads_keywords.csv',
            $dir . '/search_console_queries.csv',
            $dir . '/search_console_queries.json',
            $dir . '/ahrefs_organic_keywords.csv',
            $dir . '/ahrefs_paid_keywords.csv',
        ] as $path) {
            foreach ($provider->rows($path) as $row) {
                $result->add($row);
            }
        }

        return $this->sampleRows = $result;
    }

    private function groupBuilder(): KeywordGroupBuilder
    {
        return $this->groupBuilder ??= new KeywordGroupBuilder();
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
