<?php

declare(strict_types=1);

namespace App\KeywordImport\Provider;

use App\KeywordImport\Accessor\KeywordFileAccessorInterface;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordImport\Exception\UnsupportedKeywordSourceException;

final class KeywordImportProviderRegistry
{
    /** @var array<string, KeywordFileAccessorInterface> */
    private array $byFilename;
    /** @var array<string, KeywordFileAccessorInterface> */
    private array $bySourceAndExtension;

    /**
     * @param array<string, KeywordFileAccessorInterface> $byFilename
     * @param array<string, KeywordFileAccessorInterface> $bySourceAndExtension
     */
    public function __construct(array $byFilename, array $bySourceAndExtension)
    {
        $this->byFilename = $byFilename;
        $this->bySourceAndExtension = $bySourceAndExtension;
    }

    public function accessorFor(string $path, ?KeywordSource $source = null): KeywordFileAccessorInterface
    {
        if ($source !== null) {
            $key = $source->value() . ':' . strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (isset($this->bySourceAndExtension[$key])) {
                return $this->bySourceAndExtension[$key];
            }
        }

        $filename = basename($path);

        if (isset($this->byFilename[$filename])) {
            return $this->byFilename[$filename];
        }

        throw new UnsupportedKeywordSourceException($path);
    }
}
