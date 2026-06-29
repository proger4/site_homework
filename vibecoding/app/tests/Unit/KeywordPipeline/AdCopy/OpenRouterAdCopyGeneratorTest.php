<?php

declare(strict_types=1);

namespace Tests\Unit\KeywordPipeline\AdCopy;

use App\KeywordPipeline\AdCopy\OpenRouterAdCopyGenerator;
use App\KeywordPipeline\AdCopy\OpenRouterClient;
use App\KeywordPipeline\AdCopy\OpenRouterCurlMock;
use App\KeywordPipeline\AdCopy\TemplateAdCopyGenerator;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use PHPUnit\Framework\TestCase;
use Tests\Support\KeywordPipelineFixtures;

final class OpenRouterAdCopyGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        OpenRouterCurlMock::reset();
    }

    public function testFallsBackToTemplateGeneratorWhenContextHasNoOpenRouterKey(): void
    {
        OpenRouterCurlMock::configure(['execResult' => false, 'error' => 'network should not be called']);

        $ads = iterator_to_array($this->generator()->generate(
            KeywordPipelineFixtures::group('en', 'website builder'),
            new AdCopyGenerationContext(null)
        ));

        self::assertCount(1, $ads);
        self::assertSame('template', $ads[0]->generator());
        self::assertSame('Website Builder', $ads[0]->headline1());
    }

    /**
     * @dataProvider failingClientProvider
     *
     * @param array<string, mixed> $clientState
     */
    public function testFallsBackToTemplateGeneratorWhenOpenRouterCallFails(array $clientState): void
    {
        OpenRouterCurlMock::configure($clientState);

        $ads = iterator_to_array($this->generator()->generate(
            KeywordPipelineFixtures::group('lt', 'svetainiu kurimas'),
            new AdCopyGenerationContext('test-api-key')
        ));

        self::assertCount(1, $ads);
        self::assertSame('template', $ads[0]->generator());
        self::assertSame('Sukurkite svetaine', $ads[0]->headline2());
    }

    public function testMapsOpenRouterAdsAndPreservesRawPayload(): void
    {
        $rawAd = [
            'keyword' => 'website builder',
            'headline_1' => 'AI Headline',
            'headline_2' => 'Fast Websites',
            'headline_3' => 'No Coding',
            'description_1' => 'Create a polished site with templates.',
            'description_2' => 'Edit content and publish online.',
        ];
        OpenRouterCurlMock::configure([
            'body' => json_encode([
                'choices' => [
                    ['message' => ['content' => json_encode(['ads' => [$rawAd, 'ignored']])]],
                ],
            ]),
        ]);

        $ads = iterator_to_array($this->generator()->generate(
            KeywordPipelineFixtures::group('en', 'website builder'),
            new AdCopyGenerationContext('test-api-key')
        ));

        self::assertCount(1, $ads);
        self::assertSame('website builder', $ads[0]->keyword());
        self::assertSame('AI Headline', $ads[0]->headline1());
        self::assertSame('Fast Websites', $ads[0]->headline2());
        self::assertSame('No Coding', $ads[0]->headline3());
        self::assertSame('Create a polished site with templates.', $ads[0]->description1());
        self::assertSame('Edit content and publish online.', $ads[0]->description2());
        self::assertSame('openrouter', $ads[0]->generator());
        self::assertSame($rawAd, $ads[0]->rawPayload());
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>}>
     */
    public function failingClientProvider(): iterable
    {
        yield 'curl transport failure' => [
            ['execResult' => false, 'error' => 'connection refused'],
        ];

        yield 'non array ads payload' => [
            [
                'body' => json_encode([
                    'choices' => [
                        ['message' => ['content' => json_encode(['ads' => 'invalid'])]],
                    ],
                ]),
            ],
        ];
    }

    private function generator(): OpenRouterAdCopyGenerator
    {
        return new OpenRouterAdCopyGenerator(new OpenRouterClient(), new TemplateAdCopyGenerator());
    }
}
