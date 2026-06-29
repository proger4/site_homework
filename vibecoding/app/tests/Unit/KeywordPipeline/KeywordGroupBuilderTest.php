<?php

declare(strict_types=1);

namespace App\Tests\Unit\KeywordPipeline;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordPipeline\Contract\ProcessedKeywordRow;
use App\KeywordPipeline\KeywordGroupBuilder;
use App\Tests\TestCase;

final class KeywordGroupBuilderTest extends TestCase
{
    public function testDeterministicCleanupStatusesAndReasons(): void
    {
        $builder = new KeywordGroupBuilder();
        $rows = $builder->process([
            $this->row(KeywordSource::googleAds(), 'website builder'),
            $this->row(KeywordSource::searchConsole(), 'website builder'),
            $this->row(KeywordSource::searchConsole(), 'casino builder'),
            $this->row(KeywordSource::searchConsole(), 'free download builder'),
            $this->row(KeywordSource::searchConsole(), 'site.pro builder'),
            $this->row(KeywordSource::searchConsole(), 'wordpress builder'),
            $this->row(KeywordSource::searchConsole(), 'tiny volume', 20),
            $this->row(KeywordSource::searchConsole(), 'AI Website Builder'),
            $this->row(KeywordSource::searchConsole(), 'ai website builder'),
            $this->row(KeywordSource::searchConsole(), 'free website builder'),
        ]);

        self::assertSame('already_used_keyword', $rows[0]->removalReason());
        self::assertSame('already_used_keyword', $rows[1]->removalReason());
        self::assertSame('bad_keyword', $rows[2]->removalReason());
        self::assertSame('junk_keyword', $rows[3]->removalReason());
        self::assertSame('brand_keyword', $rows[4]->removalReason());
        self::assertSame('forbidden_keyword', $rows[5]->removalReason());
        self::assertSame('low_volume', $rows[6]->removalReason());
        self::assertSame(ProcessedKeywordRow::STATUS_ACTIVE, $rows[7]->status());
        self::assertSame(ProcessedKeywordRow::STATUS_MERGED_DUPLICATE, $rows[8]->status());
        self::assertSame(ProcessedKeywordRow::STATUS_REVIEW, $rows[9]->status());
        self::assertSame('review_possible_junk_intent', $rows[9]->removalReason());
    }

    public function testWeakTargetUrlStaysActiveWithLandingSuggestion(): void
    {
        $processed = (new KeywordGroupBuilder())->process([
            $this->row(KeywordSource::searchConsole(), 'white label builder', 900, 'https://site.pro/'),
        ]);

        self::assertSame(ProcessedKeywordRow::STATUS_REVIEW, $processed[0]->status());
        self::assertSame('review_weak_target_url', $processed[0]->removalReason());
        self::assertSame('https://site.pro/White-Label/', $processed[0]->landingPageSuggestion());
        self::assertSame('en / white label', $processed[0]->adGroupSuggestion());
    }

    private function row(
        KeywordSource $source,
        string $keyword,
        int $volume = 500,
        string $targetUrl = 'https://site.pro/Website-Builder/'
    ): KeywordImportRow {
        return new KeywordImportRow(
            $source,
            $keyword,
            $keyword,
            'en',
            $volume,
            1.25,
            $targetUrl,
            null,
            'fixture.csv',
            1,
            ['keyword' => $keyword]
        );
    }
}
