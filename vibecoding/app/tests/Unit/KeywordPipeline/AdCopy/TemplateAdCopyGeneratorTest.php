<?php

declare(strict_types=1);

namespace Tests\Unit\KeywordPipeline\AdCopy;

use App\KeywordPipeline\AdCopy\TemplateAdCopyGenerator;
use App\KeywordPipeline\Contract\AdCopyGenerationContext;
use PHPUnit\Framework\TestCase;
use Tests\Support\KeywordPipelineFixtures;

final class TemplateAdCopyGeneratorTest extends TestCase
{
    /**
     * @dataProvider copyProvider
     *
     * @param array<string, string> $expected
     */
    public function testGeneratesExpectedTemplateCopy(string $language, string $keyword, array $expected): void
    {
        $generator = new TemplateAdCopyGenerator();

        $ads = iterator_to_array($generator->generate(
            KeywordPipelineFixtures::group($language, $keyword),
            new AdCopyGenerationContext()
        ));

        self::assertCount(1, $ads);
        self::assertSame($keyword, $ads[0]->keyword());
        self::assertSame($expected['headline1'], $ads[0]->headline1());
        self::assertSame($expected['headline2'], $ads[0]->headline2());
        self::assertSame($expected['headline3'], $ads[0]->headline3());
        self::assertSame($expected['description1'], $ads[0]->description1());
        self::assertSame($expected['description2'], $ads[0]->description2());
        self::assertSame('template', $ads[0]->generator());
        self::assertSame([], $ads[0]->rawPayload());
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: array<string, string>}>
     */
    public function copyProvider(): iterable
    {
        yield 'english default copy' => [
            'en',
            'website builder',
            [
                'headline1' => 'Website Builder',
                'headline2' => 'Create A Website Fast',
                'headline3' => 'No Coding Needed',
                'description1' => 'Build a professional website with simple Site.pro tools.',
                'description2' => 'Choose a template, edit content and publish your site online.',
            ],
        ];

        yield 'lithuanian localized copy' => [
            'lt',
            'svetainiu kurimas',
            [
                'headline1' => 'Svetainiu Kurimas',
                'headline2' => 'Sukurkite svetaine',
                'headline3' => 'Be programavimo',
                'description1' => 'Kurkite svetaine greitai su paprastu Site.pro irankiu.',
                'description2' => 'Pasirinkite sablona, redaguokite ir publikuokite internetu.',
            ],
        ];

        yield 'headline one is capped at thirty chars' => [
            'en',
            'very long website builder keyword phrase',
            [
                'headline1' => 'Very Long Website Builder Keyw',
                'headline2' => 'Create A Website Fast',
                'headline3' => 'No Coding Needed',
                'description1' => 'Build a professional website with simple Site.pro tools.',
                'description2' => 'Choose a template, edit content and publish your site online.',
            ],
        ];
    }
}
