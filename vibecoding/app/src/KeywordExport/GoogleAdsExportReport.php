<?php

declare(strict_types=1);

namespace App\KeywordExport;

final class GoogleAdsExportReport
{
    private string $path;
    private int $rowCount;
    /** @var array<int, string> */
    private array $errors;

    /**
     * @param array<int, string> $errors
     */
    public function __construct(string $path, int $rowCount, array $errors)
    {
        $this->path = $path;
        $this->rowCount = $rowCount;
        $this->errors = $errors;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function rowCount(): int
    {
        return $this->rowCount;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
