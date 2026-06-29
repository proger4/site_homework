<?php

declare(strict_types=1);

namespace App\KeywordImport\Accessor;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Parser\CsvKeywordParser;

final class AhrefsPaidKeywordAccessor extends AbstractKeywordAccessor
{
    private CsvKeywordParser $parser;

    public function __construct(CsvKeywordParser $parser)
    {
        $this->parser = $parser;
        parent::__construct(KeywordSource::ahrefsPaid(), 'keyword');
    }

    /**
     * @return iterable<int, KeywordImportRow>
     */
    public function rows(string $path): iterable
    {
        foreach ($this->parser->parse($path) as [$rowNumber, $payload]) {
            yield $this->createRow($path, $rowNumber, $payload, $this->optionalString($payload, 'competitor'));
        }
    }
}
