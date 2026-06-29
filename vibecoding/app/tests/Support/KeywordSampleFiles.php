<?php

declare(strict_types=1);

namespace App\Tests\Support;

final class KeywordSampleFiles
{
    private const FIXTURE_DIR = __DIR__ . '/../fixtures/keyword-samples';

    /**
     * @return array<string, array{0: string}>
     */
    public static function provider(): array
    {
        return [
            'google ads csv' => [self::FIXTURE_DIR . '/google_ads_keywords.csv'],
            'search console csv' => [self::FIXTURE_DIR . '/search_console_queries.csv'],
            'search console json' => [self::FIXTURE_DIR . '/search_console_queries.json'],
            'ahrefs organic csv' => [self::FIXTURE_DIR . '/ahrefs_organic_keywords.csv'],
            'ahrefs paid csv' => [self::FIXTURE_DIR . '/ahrefs_paid_keywords.csv'],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function paths(): array
    {
        return array_map(
            static fn (array $dataset): string => $dataset[0],
            array_values(self::provider())
        );
    }
}
