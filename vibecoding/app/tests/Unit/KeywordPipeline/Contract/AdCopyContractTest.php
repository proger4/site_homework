<?php

declare(strict_types=1);

namespace Tests\Unit\KeywordPipeline\Contract;

use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use PHPUnit\Framework\TestCase;

final class AdCopyContractTest extends TestCase
{
    public function testAdCopyExposesConstructorValues(): void
    {
        $rawPayload = ['headline_1' => 'Raw Headline'];

        $adCopy = new AdCopy(
            'website builder',
            'Headline One',
            'Headline Two',
            'Headline Three',
            'Description One',
            'Description Two',
            'openrouter',
            $rawPayload
        );

        self::assertSame('website builder', $adCopy->keyword());
        self::assertSame('Headline One', $adCopy->headline1());
        self::assertSame('Headline Two', $adCopy->headline2());
        self::assertSame('Headline Three', $adCopy->headline3());
        self::assertSame('Description One', $adCopy->description1());
        self::assertSame('Description Two', $adCopy->description2());
        self::assertSame('openrouter', $adCopy->generator());
        self::assertSame($rawPayload, $adCopy->rawPayload());
    }

    /**
     * @dataProvider contextProvider
     *
     * @param array<int, string> $brandTerms
     * @param array<int, string> $forbiddenTerms
     * @param array<int, string> $competitorTerms
     */
    public function testAdCopyGenerationContextExposesValues(
        ?string $apiKey,
        bool $hasOpenRouterKey,
        string $model,
        array $brandTerms,
        array $forbiddenTerms,
        array $competitorTerms
    ): void {
        $context = new AdCopyGenerationContext(
            $apiKey,
            $model,
            $brandTerms,
            $forbiddenTerms,
            $competitorTerms
        );

        self::assertSame($apiKey, $context->openRouterApiKey());
        self::assertSame($hasOpenRouterKey, $context->hasOpenRouterKey());
        self::assertSame($model, $context->model());
        self::assertSame($brandTerms, $context->brandTerms());
        self::assertSame($forbiddenTerms, $context->forbiddenTerms());
        self::assertSame($competitorTerms, $context->competitorTerms());
    }

    /**
     * @return iterable<string, array{0: ?string, 1: bool, 2: string, 3: array<int, string>, 4: array<int, string>, 5: array<int, string>}>
     */
    public function contextProvider(): iterable
    {
        yield 'defaults without key' => [
            null,
            false,
            'openai/gpt-4.1-mini',
            ['site.pro', 'sitepro'],
            ['wix', 'squarespace', 'wordpress'],
            [],
        ];

        yield 'blank key is treated as absent' => [
            '   ',
            false,
            'openai/gpt-4.1-mini',
            ['site.pro'],
            ['wix'],
            ['competitor'],
        ];

        yield 'custom non blank key and policy terms' => [
            'test-api-key',
            true,
            'anthropic/claude-3.5-sonnet',
            ['Site.pro'],
            ['free forever'],
            ['competitor.example'],
        ];
    }
}
