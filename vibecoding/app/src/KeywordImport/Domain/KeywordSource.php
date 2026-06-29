<?php

declare(strict_types=1);

namespace App\KeywordImport\Domain;

final class KeywordSource
{
    public const GOOGLE_ADS = 'google_ads';
    public const SEARCH_CONSOLE = 'search_console';
    public const AHREFS_ORGANIC = 'ahrefs_organic';
    public const AHREFS_PAID = 'ahrefs_paid';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;

        if (!in_array($value, self::values(), true)) {
            throw new \InvalidArgumentException('Unsupported keyword source: ' . $value);
        }
    }

    public static function googleAds(): self
    {
        return new self(self::GOOGLE_ADS);
    }

    public static function searchConsole(): self
    {
        return new self(self::SEARCH_CONSOLE);
    }

    public static function ahrefsOrganic(): self
    {
        return new self(self::AHREFS_ORGANIC);
    }

    public static function ahrefsPaid(): self
    {
        return new self(self::AHREFS_PAID);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return [
            self::GOOGLE_ADS,
            self::SEARCH_CONSOLE,
            self::AHREFS_ORGANIC,
            self::AHREFS_PAID,
        ];
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $source): bool
    {
        return $this->value === $source->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
