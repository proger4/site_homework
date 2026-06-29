<?php

declare(strict_types=1);

namespace App\Tests\Unit\KeywordPipeline\Contract;

use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\Tests\TestCase;

final class AiContractTest extends TestCase
{
    public function testAdCopyReturnsExactConstructorOutput(): void
    {
        $copy = new AdCopy(
            'ai website builder',
            'Headline One',
            'Headline Two',
            'Headline Three',
            'Description One',
            'Description Two',
            'openrouter',
            ['source' => 'raw']
        );

        $this->assertSame('ai website builder', $copy->keyword());
        $this->assertSame('Headline One', $copy->headline1());
        $this->assertSame('Headline Two', $copy->headline2());
        $this->assertSame('Headline Three', $copy->headline3());
        $this->assertSame('Description One', $copy->description1());
        $this->assertSame('Description Two', $copy->description2());
        $this->assertSame('openrouter', $copy->generator());
        $this->assertSame(['source' => 'raw'], $copy->rawPayload());
    }

    /**
     * @return array<string, array{0: ?string, 1: bool}>
     */
    public function providerApiKeys(): array
    {
        return [
            'null key' => [null, false],
            'empty key' => ['', false],
            'blank key' => ['   ', false],
            'real key' => ['sk-or-test', true],
        ];
    }

    /**
     * @dataProvider providerApiKeys
     */
    public function testAdCopyGenerationContextOutputs(?string $apiKey, bool $hasKey): void
    {
        $context = new AdCopyGenerationContext($apiKey);

        $this->assertSame($apiKey, $context->openRouterApiKey());
        $this->assertSame('openai/gpt-4.1-mini', $context->model());
        $this->assertSame(['site.pro', 'sitepro'], $context->brandTerms());
        $this->assertSame(['wix', 'squarespace', 'wordpress'], $context->forbiddenTerms());
        $this->assertSame([], $context->competitorTerms());
        $this->assertSame($hasKey, $context->hasOpenRouterKey());
    }
}
