<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\Contract\NormalizedKeywordRow;

final class KeywordRows
{
    public static function row(
        string $keyword,
        string $language = 'en',
        string $targetUrl = 'https://site.pro/Website-Builder/',
        int $volume = 1000,
        float $cpc = 1.5,
        ?string $competitor = null
    ): NormalizedKeywordRow {
        $importRow = new KeywordImportRow(
            KeywordSource::searchConsole(),
            $keyword,
            $keyword,
            $language,
            $volume,
            $cpc,
            $targetUrl,
            $competitor,
            'test.csv',
            2,
            ['keyword' => $keyword]
        );

        return new NormalizedKeywordRow($importRow, mb_strtolower($keyword, 'UTF-8'));
    }

    /**
     * @param array<int, NormalizedKeywordRow> $rows
     */
    public static function group(array $rows): KeywordGroup
    {
        $first = $rows[0];

        return new KeywordGroup($first->language(), $first->targetUrl(), $rows);
    }
}
