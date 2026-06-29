<?php

declare(strict_types=1);

namespace App\KeywordPipeline\Contract;

final class AdCopy
{
    private string $keyword;
    private string $headline1;
    private string $headline2;
    private string $headline3;
    private string $description1;
    private string $description2;
    private string $generator;
    /** @var array<string, mixed> */
    private array $rawPayload;

    /**
     * @param array<string, mixed> $rawPayload
     */
    public function __construct(
        string $keyword,
        string $headline1,
        string $headline2,
        string $headline3,
        string $description1,
        string $description2,
        string $generator,
        array $rawPayload = []
    ) {
        $this->keyword = $keyword;
        $this->headline1 = $headline1;
        $this->headline2 = $headline2;
        $this->headline3 = $headline3;
        $this->description1 = $description1;
        $this->description2 = $description2;
        $this->generator = $generator;
        $this->rawPayload = $rawPayload;
    }

    public function keyword(): string
    {
        return $this->keyword;
    }

    public function headline1(): string
    {
        return $this->headline1;
    }

    public function headline2(): string
    {
        return $this->headline2;
    }

    public function headline3(): string
    {
        return $this->headline3;
    }

    public function description1(): string
    {
        return $this->description1;
    }

    public function description2(): string
    {
        return $this->description2;
    }

    public function generator(): string
    {
        return $this->generator;
    }

    /**
     * @return array<string, mixed>
     */
    public function rawPayload(): array
    {
        return $this->rawPayload;
    }
}
