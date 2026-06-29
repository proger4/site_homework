<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

interface AdCopyGeneratorInterface
{
    /**
     * @return iterable<int, AdCopy>
     */
    public function generate(KeywordGroup $group, AdCopyGenerationContext $context): iterable;
}
