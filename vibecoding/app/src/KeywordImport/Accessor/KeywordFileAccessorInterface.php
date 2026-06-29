<?php

declare(strict_types=1);

namespace App\KeywordImport\Accessor;

use App\KeywordImport\Domain\KeywordImportRow;

interface KeywordFileAccessorInterface
{
    /**
     * @return iterable<int, KeywordImportRow>
     */
    public function rows(string $path): iterable;
}
