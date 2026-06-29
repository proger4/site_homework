<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

final class AdCopyGenerationContext
{
    private ?string $openRouterApiKey;
    private string $model;
    /** @var array<int, string> */
    private array $brandTerms;
    /** @var array<int, string> */
    private array $forbiddenTerms;
    /** @var array<int, string> */
    private array $competitorTerms;

    /**
     * @param array<int, string> $brandTerms
     * @param array<int, string> $forbiddenTerms
     * @param array<int, string> $competitorTerms
     */
    public function __construct(
        ?string $openRouterApiKey = null,
        string $model = 'openai/gpt-4.1-mini',
        array $brandTerms = ['site.pro', 'sitepro'],
        array $forbiddenTerms = ['wix', 'squarespace', 'wordpress'],
        array $competitorTerms = []
    ) {
        $this->openRouterApiKey = $openRouterApiKey;
        $this->model = $model;
        $this->brandTerms = $brandTerms;
        $this->forbiddenTerms = $forbiddenTerms;
        $this->competitorTerms = $competitorTerms;
    }

    public function openRouterApiKey(): ?string
    {
        return $this->openRouterApiKey;
    }

    public function model(): string
    {
        return $this->model;
    }

    /**
     * @return array<int, string>
     */
    public function brandTerms(): array
    {
        return $this->brandTerms;
    }

    /**
     * @return array<int, string>
     */
    public function forbiddenTerms(): array
    {
        return $this->forbiddenTerms;
    }

    /**
     * @return array<int, string>
     */
    public function competitorTerms(): array
    {
        return $this->competitorTerms;
    }

    public function hasOpenRouterKey(): bool
    {
        return is_string($this->openRouterApiKey) && trim($this->openRouterApiKey) !== '';
    }
}
