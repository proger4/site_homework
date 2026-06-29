<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

final class KeywordGroup
{
    private string $language;
    private string $targetUrl;
    /** @var array<int, NormalizedKeywordRow> */
    private array $rows;

    /**
     * @param array<int, NormalizedKeywordRow> $rows
     */
    public function __construct(
        string $language,
        string $targetUrl,
        array $rows
    ) {
        $this->language = $language;
        $this->targetUrl = $targetUrl;
        $this->rows = $rows;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function targetUrl(): string
    {
        return $this->targetUrl;
    }

    /**
     * @return array<int, NormalizedKeywordRow>
     */
    public function rows(): array
    {
        return $this->rows;
    }
}
