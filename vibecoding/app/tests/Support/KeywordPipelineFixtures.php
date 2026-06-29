<?php

declare(strict_types=1);

namespace Tests\Support;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\Contract\NormalizedKeywordRow;

final class KeywordPipelineFixtures
{
    public static function group(string $language, string $keyword = 'website builder'): KeywordGroup
    {
        return new KeywordGroup($language, 'https://site.pro/', [
            self::row($language, $keyword),
        ]);
    }

    public static function row(string $language, string $keyword): NormalizedKeywordRow
    {
        return new NormalizedKeywordRow(
            new KeywordImportRow(
                KeywordSource::googleAds(),
                $keyword,
                $keyword,
                $language,
                120,
                1.25,
                'https://site.pro/',
                'competitor.example',
                'fixture.csv',
                7,
                ['source' => 'fixture']
            ),
            mb_strtolower($keyword, 'UTF-8')
        );
    }
}
