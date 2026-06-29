<?php

declare(strict_types=1);

namespace App\KeywordExport;

final class GoogleAdsExportValidator
{
    public const COLUMNS = [
        'Campaign',
        'Ad Group',
        'Keyword',
        'Final URL',
        'Headline 1',
        'Headline 2',
        'Headline 3',
        'Description 1',
        'Description 2',
        'Language',
    ];

    /**
     * @param array<int, array<string, string>> $rows
     * @return array<int, string>
     */
    public function validate(array $rows): array
    {
        $errors = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            foreach (self::COLUMNS as $column) {
                if (!array_key_exists($column, $row)) {
                    $errors[] = "Line {$line}: missing column {$column}";
                }
            }

            foreach (['Final URL', 'Language'] as $column) {
                if (($row[$column] ?? '') === '') {
                    $errors[] = "Line {$line}: {$column} is empty";
                }
            }

            foreach (['Headline 1', 'Headline 2', 'Headline 3'] as $column) {
                if (strlen($row[$column] ?? '') > 30) {
                    $errors[] = "Line {$line}: {$column} too long";
                }
            }

            foreach (['Description 1', 'Description 2'] as $column) {
                if (strlen($row[$column] ?? '') > 90) {
                    $errors[] = "Line {$line}: {$column} too long";
                }
            }
        }

        return $errors;
    }
}
