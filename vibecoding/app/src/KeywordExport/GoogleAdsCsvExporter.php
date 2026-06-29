<?php

declare(strict_types=1);

namespace App\KeywordExport;

final class GoogleAdsCsvExporter
{
    private GoogleAdsExportValidator $validator;

    public function __construct(?GoogleAdsExportValidator $validator = null)
    {
        $this->validator = $validator ?? new GoogleAdsExportValidator();
    }

    /**
     * @param iterable<int, GoogleAdsExportRow> $rows
     */
    public function export(iterable $rows, string $targetPath): GoogleAdsExportReport
    {
        $payload = [];

        foreach ($rows as $row) {
            if (!$row instanceof GoogleAdsExportRow) {
                throw new \InvalidArgumentException('GoogleAdsCsvExporter accepts only GoogleAdsExportRow items.');
            }

            $payload[] = $row->toArray();
        }

        $errors = $this->validator->validate($payload);
        $dir = dirname($targetPath);

        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create export directory: ' . $dir);
        }

        $handle = fopen($targetPath, 'wb');

        if ($handle === false) {
            throw new \RuntimeException('Unable to open export path: ' . $targetPath);
        }

        fputcsv($handle, GoogleAdsExportValidator::COLUMNS, ',', '"', '\\');

        foreach ($payload as $row) {
            fputcsv(
                $handle,
                array_map(static fn (string $column): string => $row[$column], GoogleAdsExportValidator::COLUMNS),
                ',',
                '"',
                '\\'
            );
        }

        fclose($handle);

        return new GoogleAdsExportReport($targetPath, count($payload), $errors);
    }
}
