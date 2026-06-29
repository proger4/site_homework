<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

use App\KeywordImport\Domain\KeywordImportRow;

interface KeywordNormalizerInterface
{
    public function normalize(KeywordImportRow $row): NormalizedKeywordRow;
}
