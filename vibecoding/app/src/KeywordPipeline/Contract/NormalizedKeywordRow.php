<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

use App\KeywordImport\Domain\KeywordImportRow;

final class NormalizedKeywordRow
{
    private KeywordImportRow $importRow;
    private string $normalizedKeyword;

    public function __construct(KeywordImportRow $importRow, string $normalizedKeyword)
    {
        $this->importRow = $importRow;
        $this->normalizedKeyword = $normalizedKeyword;
    }

    public function importRow(): KeywordImportRow
    {
        return $this->importRow;
    }

    public function normalizedKeyword(): string
    {
        return $this->normalizedKeyword;
    }

    public function keywordText(): string
    {
        return $this->importRow->keywordText();
    }

    public function language(): string
    {
        return $this->importRow->language();
    }

    public function volume(): int
    {
        return $this->importRow->volume();
    }

    public function cpc(): float
    {
        return $this->importRow->cpc();
    }

    public function targetUrl(): string
    {
        return $this->importRow->targetUrl();
    }

    public function competitor(): ?string
    {
        return $this->importRow->competitor();
    }
}
