<?php

declare(strict_types=1);

namespace App\Tests\Unit\KeywordImport;

use App\Tests\Support\KeywordSampleFiles;
use App\Tests\TestCase;

final class KeywordSampleFilesTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public function providerKeywordSampleFiles(): array
    {
        return KeywordSampleFiles::provider();
    }

    /**
     * @dataProvider providerKeywordSampleFiles
     */
    public function testFixtureFileExistsAndIsReadable(string $path): void
    {
        $this->assertTrue(is_file($path), $path);
        $this->assertTrue(filesize($path) > 0, $path);
    }
}
