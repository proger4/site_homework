<?php

declare(strict_types=1);

namespace App\KeywordImport\Exception;

final class MissingRequiredFieldException extends KeywordImportException
{
    public function __construct(string $field, string $sourceFile, int $rowNumber) {
        parent::__construct('missing required field "' . $field . '".', $sourceFile, $rowNumber);
    }
}
