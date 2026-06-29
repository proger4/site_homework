<?php

declare(strict_types=1);

namespace App\KeywordPipeline\AdCopy;

final class OpenRouterAdCopySchema
{
    /**
     * @return array<string, mixed>
     */
    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'ads' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'keyword' => ['type' => 'string'],
                            'headline_1' => ['type' => 'string'],
                            'headline_2' => ['type' => 'string'],
                            'headline_3' => ['type' => 'string'],
                            'description_1' => ['type' => 'string'],
                            'description_2' => ['type' => 'string'],
                        ],
                        'required' => [
                            'keyword',
                            'headline_1',
                            'headline_2',
                            'headline_3',
                            'description_1',
                            'description_2',
                        ],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['ads'],
            'additionalProperties' => false,
        ];
    }
}
