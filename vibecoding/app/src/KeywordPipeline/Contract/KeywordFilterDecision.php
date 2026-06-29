<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

final class KeywordFilterDecision
{
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REMOVED = 'removed';
    public const STATUS_REVIEW = 'review';

    private string $status;
    private ?string $removalReason;

    public function __construct(string $status, ?string $removalReason = null)
    {
        $this->status = $status;
        $this->removalReason = $removalReason;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function removalReason(): ?string
    {
        return $this->removalReason;
    }
}
