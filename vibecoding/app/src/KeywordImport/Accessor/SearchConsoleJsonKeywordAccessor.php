<?php

declare(strict_types=1);

namespace App\KeywordImport\Accessor;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Parser\JsonKeywordParser;

final class SearchConsoleJsonKeywordAccessor extends AbstractKeywordAccessor
{
    private JsonKeywordParser $parser;

    public function __construct(JsonKeywordParser $parser)
    {
        $this->parser = $parser;
        parent::__construct(KeywordSource::searchConsole(), 'query');
    }

    /**
     * @return iterable<int, KeywordImportRow>
     */
    public function rows(string $path): iterable
    {
        foreach ($this->parser->parse($path) as [$rowNumber, $payload]) {
            yield $this->createRow($path, $rowNumber, $payload);
        }
    }
}
