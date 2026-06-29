<?php

declare(strict_types=1);

namespace App\KeywordImport\Provider;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;

interface KeywordImportProviderInterface
{
    /**
     * @return iterable<int, KeywordImportRow>
     */
    public function rows(string $path, ?KeywordSource $source = null): iterable;
}
