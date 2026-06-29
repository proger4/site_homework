<?php

declare(strict_types=1);

namespace App\KeywordImport\Provider;

use App\KeywordImport\Accessor\AhrefsOrganicKeywordAccessor;
use App\KeywordImport\Accessor\AhrefsPaidKeywordAccessor;
use App\KeywordImport\Accessor\GoogleAdsKeywordAccessor;
use App\KeywordImport\Accessor\SearchConsoleCsvKeywordAccessor;
use App\KeywordImport\Accessor\SearchConsoleJsonKeywordAccessor;
use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Parser\CsvKeywordParser;
use App\KeywordImport\Parser\JsonKeywordParser;

final class SampleKeywordImportProvider implements KeywordImportProviderInterface
{
    private KeywordImportProviderRegistry $registry;

    public function __construct(KeywordImportProviderRegistry $registry)
    {
        $this->registry = $registry;
    }

    public static function createDefault(): self
    {
        $csv = new CsvKeywordParser();
        $json = new JsonKeywordParser();

        $googleAds = new GoogleAdsKeywordAccessor($csv);
        $searchConsoleCsv = new SearchConsoleCsvKeywordAccessor($csv);
        $searchConsoleJson = new SearchConsoleJsonKeywordAccessor($json);
        $ahrefsOrganic = new AhrefsOrganicKeywordAccessor($csv);
        $ahrefsPaid = new AhrefsPaidKeywordAccessor($csv);

        return new self(new KeywordImportProviderRegistry(
            [
                'google_ads_keywords.csv' => $googleAds,
                'search_console_queries.csv' => $searchConsoleCsv,
                'search_console_queries.json' => $searchConsoleJson,
                'ahrefs_organic_keywords.csv' => $ahrefsOrganic,
                'ahrefs_paid_keywords.csv' => $ahrefsPaid,
            ],
            [
                KeywordSource::GOOGLE_ADS . ':csv' => $googleAds,
                KeywordSource::SEARCH_CONSOLE . ':csv' => $searchConsoleCsv,
                KeywordSource::SEARCH_CONSOLE . ':json' => $searchConsoleJson,
                KeywordSource::AHREFS_ORGANIC . ':csv' => $ahrefsOrganic,
                KeywordSource::AHREFS_PAID . ':csv' => $ahrefsPaid,
            ]
        ));
    }

    /**
     * @return iterable<int, KeywordImportRow>
     */
    public function rows(string $path, ?KeywordSource $source = null): iterable
    {
        $accessor = $this->registry->accessorFor($path, $source);

        foreach ($accessor->rows($path) as $row) {
            yield $row;
        }
    }
}
