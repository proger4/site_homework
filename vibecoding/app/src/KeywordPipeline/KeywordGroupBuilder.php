<?php

declare(strict_types=1);

namespace App\KeywordPipeline;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordPipeline\Contract\KeywordGroup;
use App\KeywordPipeline\Contract\NormalizedKeywordRow;
use App\KeywordPipeline\Contract\ProcessedKeywordRow;

final class KeywordGroupBuilder
{
    /**
     * @var array<int, string>
     */
    private $brandTerms;

    /**
     * @var array<int, string>
     */
    private $forbiddenTerms;

    /**
     * @var array<int, string>
     */
    private array $junkTerms;

    /**
     * @var array<int, string>
     */
    private array $badTerms;

    private int $minimumVolume;

    /**
     * @param array<int, string> $brandTerms
     * @param array<int, string> $forbiddenTerms
     * @param array<int, string> $junkTerms
     * @param array<int, string> $badTerms
     */
    public function __construct(
        array $brandTerms = ['site.pro', 'sitepro'],
        array $forbiddenTerms = ['wix', 'squarespace', 'wordpress'],
        int $minimumVolume = 100,
        array $junkTerms = ['free download', 'torrent', 'nulled', 'crack', 'coupon'],
        array $badTerms = ['casino', 'porn', 'adult', 'xxx']
    ) {
        $this->brandTerms = $brandTerms;
        $this->forbiddenTerms = $forbiddenTerms;
        $this->minimumVolume = $minimumVolume;
        $this->junkTerms = $junkTerms;
        $this->badTerms = $badTerms;
    }

    /**
     * @param iterable<int, KeywordImportRow> $rows
     * @return array<int, KeywordGroup>
     */
    public function build(iterable $rows): array
    {
        return $this->groupsFromProcessedRows($this->process($rows));
    }

    /**
     * @param iterable<int, KeywordImportRow> $rows
     * @return array<int, ProcessedKeywordRow>
     */
    public function process(iterable $rows): array
    {
        $usedKeywords = [];
        $inputRows = [];
        $processed = [];
        $seen = [];

        foreach ($rows as $row) {
            $inputRows[] = $row;
            if ($row->source()->equals(KeywordSource::googleAds())) {
                $usedKeywords[$this->normalizeKeyword($row->keywordText())] = true;
            }
        }

        foreach ($inputRows as $row) {
            $normalized = $this->normalizeKeyword($row->keywordText());
            $normalizedRow = new NormalizedKeywordRow($row, $normalized);

            if ($row->source()->equals(KeywordSource::googleAds())) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'already_used_keyword');

                continue;
            }

            if ($normalized === '') {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'junk_keyword');

                continue;
            }

            if ($row->volume() < $this->minimumVolume) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'low_volume');

                continue;
            }

            if ($this->containsAny($normalized, $this->badTerms)) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'bad_keyword');

                continue;
            }

            if ($this->containsAny($normalized, $this->junkTerms)) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'junk_keyword');

                continue;
            }

            if ($this->containsAny($normalized, $this->brandTerms)) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'brand_keyword');

                continue;
            }

            if ($this->containsAny($normalized, $this->forbiddenTerms)) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'forbidden_keyword');

                continue;
            }

            if (isset($usedKeywords[$normalized])) {
                $processed[] = ProcessedKeywordRow::excluded($normalizedRow, 'already_used_keyword');

                continue;
            }

            $dedupeKey = $normalized . '|' . $row->language() . '|' . $row->targetUrl();

            if (isset($seen[$dedupeKey])) {
                $processed[] = ProcessedKeywordRow::mergedDuplicate($normalizedRow);

                continue;
            }

            $seen[$dedupeKey] = true;
            $processed[] = ProcessedKeywordRow::active($normalizedRow);
        }

        return $processed;
    }

    /**
     * @param array<int, ProcessedKeywordRow> $rows
     * @return array<int, KeywordGroup>
     */
    public function groupsFromProcessedRows(array $rows): array
    {
        $active = [];

        foreach ($rows as $row) {
            if ($row->isActive()) {
                $active[] = $row->normalizedRow();
            }
        }

        return $this->groupRows($active);
    }

    /**
     * @param array<int, NormalizedKeywordRow> $rows
     * @return array<int, KeywordGroup>
     */
    public function groupsFromNormalizedRows(array $rows): array
    {
        return $this->groupRows($rows);
    }

    /**
     * @param array<int, NormalizedKeywordRow> $rows
     * @return array<int, KeywordGroup>
     */
    private function groupRows(array $rows): array
    {
        $groups = [];

        foreach ($rows as $row) {
            $groupKey = $row->language() . '|' . $row->targetUrl();
            $groups[$groupKey][] = $row;
        }

        $result = [];

        foreach ($groups as $groupRows) {
            $first = $groupRows[0];
            $result[] = new KeywordGroup($first->language(), $first->targetUrl(), $groupRows);
        }

        return $result;
    }

    private function normalizeKeyword(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', mb_strtolower($value, 'UTF-8')));
    }

    /**
     * @param array<int, string> $needles
     */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
