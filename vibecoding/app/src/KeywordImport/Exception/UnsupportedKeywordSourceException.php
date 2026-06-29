<?php

declare(strict_types=1);

namespace App\KeywordImport\Exception;

final class UnsupportedKeywordSourceException extends KeywordImportException
{
    public function __construct(string $sourceFile) {
        parent::__construct('has no registered keyword source accessor.', $sourceFile);
    }
}
