<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

use App\KeywordImport\Domain\KeywordImportRow;

final class ProcessedKeywordRow
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXCLUDED = 'excluded';
    public const STATUS_MERGED_DUPLICATE = 'merged_duplicate';
    public const STATUS_REVIEW = 'review_suggestion';

    private NormalizedKeywordRow $normalizedRow;
    private string $status;
    private ?string $removalReason;
    private ?string $landingPageSuggestion;
    private ?string $adGroupSuggestion;

    public function __construct(
        NormalizedKeywordRow $normalizedRow,
        string $status,
        ?string $removalReason = null,
        ?string $landingPageSuggestion = null,
        ?string $adGroupSuggestion = null
    ) {
        $this->normalizedRow = $normalizedRow;
        $this->status = $status;
        $this->removalReason = $removalReason;
        $this->landingPageSuggestion = $landingPageSuggestion;
        $this->adGroupSuggestion = $adGroupSuggestion;
    }

    public static function active(
        NormalizedKeywordRow $row,
        ?string $landingPageSuggestion = null,
        ?string $adGroupSuggestion = null
    ): self
    {
        return new self($row, self::STATUS_ACTIVE, null, $landingPageSuggestion, $adGroupSuggestion);
    }

    public static function excluded(NormalizedKeywordRow $row, string $reason): self
    {
        return new self($row, self::STATUS_EXCLUDED, $reason);
    }

    public static function mergedDuplicate(NormalizedKeywordRow $row): self
    {
        return new self($row, self::STATUS_MERGED_DUPLICATE, 'duplicate_keyword');
    }

    public static function review(
        NormalizedKeywordRow $row,
        string $reason,
        ?string $landingPageSuggestion = null,
        ?string $adGroupSuggestion = null
    ): self
    {
        return new self($row, self::STATUS_REVIEW, $reason, $landingPageSuggestion, $adGroupSuggestion);
    }

    public function normalizedRow(): NormalizedKeywordRow
    {
        return $this->normalizedRow;
    }

    public function importRow(): KeywordImportRow
    {
        return $this->normalizedRow->importRow();
    }

    public function normalizedKeyword(): string
    {
        return $this->normalizedRow->normalizedKeyword();
    }

    public function status(): string
    {
        return $this->status;
    }

    public function removalReason(): ?string
    {
        return $this->removalReason;
    }

    public function landingPageSuggestion(): ?string
    {
        return $this->landingPageSuggestion;
    }

    public function adGroupSuggestion(): ?string
    {
        return $this->adGroupSuggestion;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE || $this->status === self::STATUS_REVIEW;
    }
}
