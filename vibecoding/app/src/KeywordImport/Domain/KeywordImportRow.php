<?php

declare(strict_types=1);

namespace App\KeywordImport\Domain;

final class KeywordImportRow
{
    private KeywordSource $source;
    private string $keywordText;
    private string $originalKeyword;
    private string $language;
    private int $volume;
    private float $cpc;
    private string $targetUrl;
    private ?string $competitor;
    private string $sourceFile;
    private int $rowNumber;
    /** @var array<string, mixed> */
    private array $rawPayload;

    /**
     * @param array<string, mixed> $rawPayload
     */
    public function __construct(
        KeywordSource $source,
        string $keywordText,
        string $originalKeyword,
        string $language,
        int $volume,
        float $cpc,
        string $targetUrl,
        ?string $competitor,
        string $sourceFile,
        int $rowNumber,
        array $rawPayload
    ) {
        $this->source = $source;
        $this->keywordText = $keywordText;
        $this->originalKeyword = $originalKeyword;
        $this->language = $language;
        $this->volume = $volume;
        $this->cpc = $cpc;
        $this->targetUrl = $targetUrl;
        $this->competitor = $competitor;
        $this->sourceFile = $sourceFile;
        $this->rowNumber = $rowNumber;
        $this->rawPayload = $rawPayload;
    }

    public function source(): KeywordSource
    {
        return $this->source;
    }

    public function keywordText(): string
    {
        return $this->keywordText;
    }

    public function originalKeyword(): string
    {
        return $this->originalKeyword;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function volume(): int
    {
        return $this->volume;
    }

    public function cpc(): float
    {
        return $this->cpc;
    }

    public function targetUrl(): string
    {
        return $this->targetUrl;
    }

    public function competitor(): ?string
    {
        return $this->competitor;
    }

    public function sourceFile(): string
    {
        return $this->sourceFile;
    }

    public function rowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * @return array<string, mixed>
     */
    public function rawPayload(): array
    {
        return $this->rawPayload;
    }
}
