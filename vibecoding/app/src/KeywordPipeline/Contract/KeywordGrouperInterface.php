<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

interface KeywordGrouperInterface
{
    /**
     * @param iterable<int, NormalizedKeywordRow> $rows
     * @return iterable<int, KeywordGroup>
     */
    public function group(iterable $rows): iterable;
}
