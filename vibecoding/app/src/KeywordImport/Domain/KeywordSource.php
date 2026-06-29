<?php

declare(strict_types=1);

namespace App\KeywordImport\Domain;

enum KeywordSource: string
{
    public const GOOGLE_ADS = 'google_ads';
    public const SEARCH_CONSOLE = 'search_console';
    public const AHREFS_ORGANIC = 'ahrefs_organic';
    public const AHREFS_PAID = 'ahrefs_paid';

    case GoogleAds = self::GOOGLE_ADS;
    case SearchConsole = self::SEARCH_CONSOLE;
    case AhrefsOrganic = self::AHREFS_ORGANIC;
    case AhrefsPaid = self::AHREFS_PAID;

    public static function googleAds(): self
    {
        return self::GoogleAds;
    }

    public static function searchConsole(): self
    {
        return self::SearchConsole;
    }

    public static function ahrefsOrganic(): self
    {
        return self::AhrefsOrganic;
    }

    public static function ahrefsPaid(): self
    {
        return self::AhrefsPaid;
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new \InvalidArgumentException('Unsupported keyword source: ' . $value);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $source): string => $source->value,
            self::cases()
        );
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $source): bool
    {
        return $this === $source;
    }
}
