<?php

declare(strict_types=1);

namespace App\KeywordImport\Domain;

final class KeywordImportResult
{
    /** @var array<int, KeywordImportRow> */
    private array $rows = [];

    public function add(KeywordImportRow $row): void
    {
        $this->rows[] = $row;
    }

    /**
     * @return array<int, KeywordImportRow>
     */
    public function rows(): array
    {
        return $this->rows;
    }

    public function rowCount(): int
    {
        return count($this->rows);
    }

    /**
     * @return array<string, int>
     */
    public function countsBySource(): array
    {
        $counts = [];

        foreach ($this->rows as $row) {
            $source = $row->source()->value();
            $counts[$source] = ($counts[$source] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    public function countFor(KeywordSource $source): int
    {
        return $this->countsBySource()[$source->value()] ?? 0;
    }
}
