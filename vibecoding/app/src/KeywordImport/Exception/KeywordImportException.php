<?php

declare(strict_types=1);

namespace App\KeywordImport\Exception;

class KeywordImportException extends \RuntimeException
{
    private string $sourceFile;
    private ?int $rowNumber;

    public function __construct(
        string $message,
        string $sourceFile,
        ?int $rowNumber = null
    ) {
        $this->sourceFile = $sourceFile;
        $this->rowNumber = $rowNumber;
        $location = $this->sourceFile;

        if ($this->rowNumber !== null) {
            $location .= ':' . $this->rowNumber;
        }

        parent::__construct($location . ' ' . $message);
    }

    public function sourceFile(): string
    {
        return $this->sourceFile;
    }

    public function rowNumber(): ?int
    {
        return $this->rowNumber;
    }
}
