<?php

declare(strict_types=1);

namespace App\KeywordPipeline\AdCopy;

use App\KeywordPipeline\Contract\AdCopy;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use App\KeywordPipeline\Contract\AdCopyGeneratorInterface;
use App\KeywordPipeline\Contract\KeywordGroup;

final class TemplateAdCopyGenerator implements AdCopyGeneratorInterface
{
    /**
     * @return iterable<int, AdCopy>
     */
    public function generate(KeywordGroup $group, AdCopyGenerationContext $context): iterable
    {
        foreach ($group->rows() as $row) {
            $keyword = $row->keywordText();

            if ($row->language() === 'lt') {
                yield new AdCopy(
                    $keyword,
                    $this->shorten($this->title($keyword), 30),
                    'Sukurkite svetaine',
                    'Be programavimo',
                    'Kurkite svetaine greitai su paprastu Site.pro irankiu.',
                    'Pasirinkite sablona, redaguokite ir publikuokite internetu.',
                    'template'
                );

                continue;
            }

            yield new AdCopy(
                $keyword,
                $this->shorten($this->title($keyword), 30),
                'Create A Website Fast',
                'No Coding Needed',
                'Build a professional website with simple Site.pro tools.',
                'Choose a template, edit content and publish your site online.',
                'template'
            );
        }
    }

    private function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    private function shorten(string $value, int $limit): string
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8'));
    }
}
