<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

interface KeywordFilterInterface
{
    public function decide(NormalizedKeywordRow $row): KeywordFilterDecision;
}
