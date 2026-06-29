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

            $reviewReason = $this->reviewReason($normalizedRow);
            $landingPageSuggestion = $this->landingPageSuggestion($normalizedRow);
            $adGroupSuggestion = $this->adGroupSuggestion($normalizedRow);

            $processed[] = $reviewReason === null
                ? ProcessedKeywordRow::active($normalizedRow, $landingPageSuggestion, $adGroupSuggestion)
                : ProcessedKeywordRow::review($normalizedRow, $reviewReason, $landingPageSuggestion, $adGroupSuggestion);
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

    private function reviewReason(NormalizedKeywordRow $row): ?string
    {
        $keyword = $row->normalizedKeyword();

        if (preg_match('/\b(free|cheap|download|template|templates)\b/u', $keyword) === 1) {
            return 'review_possible_junk_intent';
        }

        if ($this->isWeakTargetUrl($row->targetUrl())) {
            return 'review_weak_target_url';
        }

        return null;
    }

    private function landingPageSuggestion(NormalizedKeywordRow $row): ?string
    {
        if (!$this->isWeakTargetUrl($row->targetUrl())) {
            return null;
        }

        if ($row->language() === 'lt') {
            return 'https://site.pro/lt/';
        }

        if (strpos($row->normalizedKeyword(), 'white label') !== false) {
            return 'https://site.pro/White-Label/';
        }

        if ($this->hasWord($row->normalizedKeyword(), 'ai')) {
            return 'https://site.pro/AI-Website-Builder/';
        }

        return 'https://site.pro/Website-Builder/';
    }

    private function adGroupSuggestion(NormalizedKeywordRow $row): ?string
    {
        $keyword = $row->normalizedKeyword();

        if (strpos($keyword, 'white label') !== false) {
            return $row->language() . ' / white label';
        }

        if ($this->hasWord($keyword, 'ai')) {
            return $row->language() . ' / ai website builder';
        }

        if (strpos($keyword, 'template') !== false || strpos($keyword, 'templates') !== false) {
            return $row->language() . ' / templates';
        }

        return $row->language() . ' / website builder';
    }

    private function isWeakTargetUrl(string $targetUrl): bool
    {
        $path = trim((string) parse_url($targetUrl, PHP_URL_PATH), '/');

        return $targetUrl === '' || $path === '';
    }

    private function hasWord(string $haystack, string $word): bool
    {
        return preg_match('/\b' . preg_quote($word, '/') . '\b/u', $haystack) === 1;
    }
}
