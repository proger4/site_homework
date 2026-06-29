<?php

declare(strict_types=1);

namespace App\KeywordImport\Parser;

use App\KeywordImport\Exception\InvalidKeywordFileException;

final class CsvKeywordParser
{
    /**
     * @return iterable<int, array{0:int, 1:array<string, mixed>}>
     */
    public function parse(string $path): iterable
    {
        if (!is_readable($path)) {
            throw new InvalidKeywordFileException('is not readable.', $path);
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidKeywordFileException('could not be opened.', $path);
        }

        $headers = fgetcsv($handle, 0, ',', '"', '\\');

        if ($headers === false || $headers === [null]) {
            fclose($handle);
            throw new InvalidKeywordFileException('has no CSV header.', $path);
        }

        $headers = array_map(static fn ($header): string => trim((string) $header), $headers);
        $line = 1;

        while (($values = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $line++;

            if ($values === [null]) {
                continue;
            }

            $payload = [];

            foreach ($headers as $index => $header) {
                $payload[$header] = $values[$index] ?? '';
            }

            yield [$line, $payload];
        }

        fclose($handle);
    }
}
