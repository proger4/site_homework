<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

use App\KeywordImport\Domain\KeywordImportRow;

final class ProcessedKeywordRow
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXCLUDED = 'excluded';
    public const STATUS_MERGED_DUPLICATE = 'merged_duplicate';

    private NormalizedKeywordRow $normalizedRow;
    private string $status;
    private ?string $removalReason;

    public function __construct(
        NormalizedKeywordRow $normalizedRow,
        string $status,
        ?string $removalReason = null
    ) {
        $this->normalizedRow = $normalizedRow;
        $this->status = $status;
        $this->removalReason = $removalReason;
    }

    public static function active(NormalizedKeywordRow $row): self
    {
        return new self($row, self::STATUS_ACTIVE);
    }

    public static function excluded(NormalizedKeywordRow $row, string $reason): self
    {
        return new self($row, self::STATUS_EXCLUDED, $reason);
    }

    public static function mergedDuplicate(NormalizedKeywordRow $row): self
    {
        return new self($row, self::STATUS_MERGED_DUPLICATE, 'duplicate_keyword');
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

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
