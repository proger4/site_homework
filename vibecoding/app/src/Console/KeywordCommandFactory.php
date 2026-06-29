<?php

declare(strict_types=1);

namespace App\Console;

use App\KeywordExport\GoogleAdsCsvExporter;
use App\KeywordExport\GoogleAdsExportValidator;
use App\KeywordImport\Provider\SampleKeywordImportProvider;
use App\KeywordPipeline\AdCopy\OpenRouterAdCopyGenerator;
use App\KeywordPipeline\AdCopy\OpenRouterClient;
use App\KeywordPipeline\AdCopy\TemplateAdCopyGenerator;
use App\KeywordPipeline\KeywordGroupBuilder;
use App\KeywordStorage\SqliteKeywordStorage;

final class KeywordCommandFactory
{
    private string $appRoot;

    public function __construct(string $appRoot)
    {
        $this->appRoot = $appRoot;
    }

    public function create(): KeywordCommand
    {
        $templateGenerator = new TemplateAdCopyGenerator();
        $storage = null;

        return new KeywordCommand(
            $this->appRoot,
            SampleKeywordImportProvider::createDefault(),
            new GoogleAdsCsvExporter(new GoogleAdsExportValidator()),
            $templateGenerator,
            new OpenRouterAdCopyGenerator(new OpenRouterClient(), $templateGenerator),
            new KeywordGroupBuilder(),
            static function () use (&$storage): SqliteKeywordStorage {
                return $storage ??= SqliteKeywordStorage::fromEnvironment();
            }
        );
    }
}
