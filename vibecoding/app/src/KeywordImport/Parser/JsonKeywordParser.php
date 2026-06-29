<?php

declare(strict_types=1);

namespace App\KeywordImport\Parser;

use App\KeywordImport\Exception\InvalidKeywordFileException;

final class JsonKeywordParser
{
    /**
     * @return iterable<int, array{0:int, 1:array<string, mixed>}>
     */
    public function parse(string $path): iterable
    {
        if (!is_readable($path)) {
            throw new InvalidKeywordFileException('is not readable.', $path);
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (!is_array($decoded)) {
            throw new InvalidKeywordFileException('does not contain a JSON array.', $path);
        }

        $index = 0;

        foreach ($decoded as $item) {
            $index++;

            if (!is_array($item)) {
                throw new InvalidKeywordFileException('contains a non-object JSON row.', $path, $index);
            }

            yield [$index, $item];
        }
    }
}
