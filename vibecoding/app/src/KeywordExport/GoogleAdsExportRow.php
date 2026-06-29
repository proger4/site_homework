<?php

declare(strict_types=1);

namespace App\KeywordExport;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\NormalizedKeywordRow;

final class GoogleAdsExportRow
{
    private string $campaign;
    private string $adGroup;
    private string $keyword;
    private string $finalUrl;
    private string $headline1;
    private string $headline2;
    private string $headline3;
    private string $description1;
    private string $description2;
    private string $language;

    public function __construct(
        string $campaign,
        string $adGroup,
        string $keyword,
        string $finalUrl,
        string $headline1,
        string $headline2,
        string $headline3,
        string $description1,
        string $description2,
        string $language
    ) {
        $this->campaign = $campaign;
        $this->adGroup = $adGroup;
        $this->keyword = $keyword;
        $this->finalUrl = $finalUrl;
        $this->headline1 = $headline1;
        $this->headline2 = $headline2;
        $this->headline3 = $headline3;
        $this->description1 = $description1;
        $this->description2 = $description2;
        $this->language = $language;
    }

    public static function fromKeywordImportRow(KeywordImportRow $row): self
    {
        $keyword = $row->keywordText();
        $language = $row->language();

        return new self(
            'Vibecoding ' . strtoupper($language),
            self::shorten(self::adGroupFromUrl($row->targetUrl()), 60),
            $keyword,
            $row->targetUrl(),
            self::shorten(ucwords($keyword), 30),
            'Build Your Website',
            'Try Site.pro',
            'Create a website with Site.pro.',
            'Fast setup for ' . $language . ' keywords.',
            $language
        );
    }

    public static function fromNormalizedKeywordRowAndAdCopy(NormalizedKeywordRow $row, AdCopy $copy): self
    {
        return new self(
            strtoupper($row->language()) . ' - ' . self::titleFromUrl($row->targetUrl()),
            self::shorten(ucwords($row->keywordText()), 60),
            $row->keywordText(),
            $row->targetUrl(),
            self::shorten($copy->headline1(), 30),
            self::shorten($copy->headline2(), 30),
            self::shorten($copy->headline3(), 30),
            self::shorten($copy->description1(), 90),
            self::shorten($copy->description2(), 90),
            $row->language()
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'Campaign' => $this->campaign,
            'Ad Group' => $this->adGroup,
            'Keyword' => $this->keyword,
            'Final URL' => $this->finalUrl,
            'Headline 1' => $this->headline1,
            'Headline 2' => $this->headline2,
            'Headline 3' => $this->headline3,
            'Description 1' => $this->description1,
            'Description 2' => $this->description2,
            'Language' => $this->language,
        ];
    }

    private static function adGroupFromUrl(string $url): string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $path = trim(str_replace(['-', '_'], ' ', $path), '/ ');

        return $path === '' ? 'Homepage' : ucwords($path);
    }

    private static function shorten(string $value, int $limit): string
    {
        if (strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(substr($value, 0, $limit));
    }

    private static function titleFromUrl(string $url): string
    {
        if (strpos($url, 'AI-Website-Builder') !== false) {
            return 'AI Website Builder';
        }

        if (strpos($url, 'White-Label') !== false) {
            return 'White Label';
        }

        if (strpos($url, '/lt/') !== false) {
            return 'Website Builder LT';
        }

        return 'Website Builder';
    }
}
