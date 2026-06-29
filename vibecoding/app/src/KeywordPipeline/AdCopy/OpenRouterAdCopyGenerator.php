<?php

declare(strict_types=1);

namespace App\KeywordPipeline\AdCopy;

use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\KeywordPipeline\Contract\AdCopyGeneratorInterface;
use App\KeywordPipeline\Contract\KeywordGroup;
use RuntimeException;

final class OpenRouterAdCopyGenerator implements AdCopyGeneratorInterface
{
    private OpenRouterClient $client;
    private AdCopyGeneratorInterface $fallback;

    public function __construct(
        OpenRouterClient $client,
        AdCopyGeneratorInterface $fallback
    ) {
        $this->client = $client;
        $this->fallback = $fallback;
    }

    /**
     * @return iterable<int, AdCopy>
     */
    public function generate(KeywordGroup $group, AdCopyGenerationContext $context): iterable
    {
        if (!$context->hasOpenRouterKey()) {
            return yield from $this->fallback->generate($group, $context);
        }

        try {
            $response = $this->client->chatJson(
                (string) $context->openRouterApiKey(),
                $context->model(),
                $this->buildMessages($group, $context),
                OpenRouterAdCopySchema::schema()
            );

            $ads = $response['ads'] ?? [];

            if (!is_array($ads)) {
                throw new RuntimeException('OpenRouter response does not contain ads array.');
            }

            foreach ($ads as $ad) {
                if (!is_array($ad)) {
                    continue;
                }

                yield new AdCopy(
                    (string) ($ad['keyword'] ?? ''),
                    (string) ($ad['headline_1'] ?? ''),
                    (string) ($ad['headline_2'] ?? ''),
                    (string) ($ad['headline_3'] ?? ''),
                    (string) ($ad['description_1'] ?? ''),
                    (string) ($ad['description_2'] ?? ''),
                    'openrouter',
                    $ad
                );
            }
        } catch (\Throwable $e) {
            return yield from $this->fallback->generate($group, $context);
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildMessages(KeywordGroup $group, AdCopyGenerationContext $context): array
    {
        $keywords = [];

        foreach ($group->rows() as $row) {
            $keywords[] = [
                'keyword' => $row->keywordText(),
                'language' => $row->language(),
                'volume' => $row->volume(),
                'cpc' => $row->cpc(),
                'target_url' => $row->targetUrl(),
                'competitor' => $row->competitor(),
            ];
        }

        return [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'You generate Google Ads ad copy for Site.pro website builder.',
                    'Return only JSON matching the provided schema.',
                    'Generate one ad per keyword.',
                    'Keep keyword unchanged.',
                    'Use the provided language.',
                    'Do not mention competitor names.',
                    'Do not invent prices, discounts, legal guarantees, or unsupported product features.',
                    'Focus on website creation, templates, no coding, fast publishing, and ease of use.',
                ]),
            ],
            [
                'role' => 'user',
                'content' => json_encode([
                    'language' => $group->language(),
                    'target_url' => $group->targetUrl(),
                    'keywords' => $keywords,
                    'brand_terms' => $context->brandTerms(),
                    'forbidden_terms' => $context->forbiddenTerms(),
                    'competitor_terms' => $context->competitorTerms(),
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
        ];
    }
}
