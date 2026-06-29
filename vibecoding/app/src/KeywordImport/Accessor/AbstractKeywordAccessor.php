<?php

declare(strict_types=1);

namespace App\KeywordImport\Accessor;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Exception\MissingRequiredFieldException;

abstract class AbstractKeywordAccessor implements KeywordFileAccessorInterface
{
    private KeywordSource $source;
    private string $keywordField;

    public function __construct(KeywordSource $source, string $keywordField)
    {
        $this->source = $source;
        $this->keywordField = $keywordField;
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function createRow(string $path, int $rowNumber, array $payload, ?string $competitor = null): KeywordImportRow
    {
        $keyword = $this->requiredString($payload, $this->keywordField, $path, $rowNumber);
        $language = $this->requiredString($payload, 'language', $path, $rowNumber);
        $targetUrl = $this->requiredString($payload, 'target_url', $path, $rowNumber);
        $volume = $this->requiredInt($payload, 'volume', $path, $rowNumber);
        $cpc = $this->requiredFloat($payload, 'cpc', $path, $rowNumber);

        return new KeywordImportRow(
            $this->source,
            $keyword,
            $keyword,
            $language,
            $volume,
            $cpc,
            $targetUrl,
            $competitor,
            $path,
            $rowNumber,
            $payload
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function optionalString(array $payload, string $field): ?string
    {
        if (!array_key_exists($field, $payload)) {
            return null;
        }

        $value = trim((string) $payload[$field]);

        return $value === '' ? null : $value;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requiredString(array $payload, string $field, string $path, int $rowNumber): string
    {
        if (!array_key_exists($field, $payload) || trim((string) $payload[$field]) === '') {
            throw new MissingRequiredFieldException($field, $path, $rowNumber);
        }

        return trim((string) $payload[$field]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requiredInt(array $payload, string $field, string $path, int $rowNumber): int
    {
        $value = $this->requiredString($payload, $field, $path, $rowNumber);

        if (!is_numeric($value)) {
            throw new MissingRequiredFieldException($field, $path, $rowNumber);
        }

        return (int) $value;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requiredFloat(array $payload, string $field, string $path, int $rowNumber): float
    {
        $value = $this->requiredString($payload, $field, $path, $rowNumber);

        if (!is_numeric($value)) {
            throw new MissingRequiredFieldException($field, $path, $rowNumber);
        }

        return (float) $value;
    }
}
