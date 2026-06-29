<?php

declare(strict_types=1);

namespace App\KeywordPipeline\AdCopy;

final class OpenRouterCurlMock
{
    /** @var array<string, mixed> */
    private static array $state = [];

    public static function reset(): void
    {
        self::$state = [
            'initUrl' => null,
            'options' => [],
            'body' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'ads' => [
                                    [
                                        'keyword' => 'website builder',
                                        'headline_1' => 'Website Builder',
                                        'headline_2' => 'Create Sites Fast',
                                        'headline_3' => 'No Coding',
                                        'description_1' => 'Build a website with templates.',
                                        'description_2' => 'Publish your site online.',
                                    ],
                                ],
                            ]),
                        ],
                    ],
                ],
            ]),
            'execResult' => null,
            'error' => '',
            'statusCode' => 200,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function state(): array
    {
        return self::$state;
    }

    /**
     * @param array<string, mixed> $changes
     */
    public static function configure(array $changes): void
    {
        self::$state = array_replace(self::$state, $changes);
    }

    public static function setInitUrl(string $url): void
    {
        self::$state['initUrl'] = $url;
    }

    /**
     * @param array<int|string, mixed> $options
     */
    public static function setOptions(array $options): void
    {
        self::$state['options'] = $options;
    }

    public static function body()
    {
        return self::$state['execResult'] ?? self::$state['body'];
    }

    public static function error(): string
    {
        return (string) self::$state['error'];
    }

    public static function statusCode(): int
    {
        return (int) self::$state['statusCode'];
    }
}

OpenRouterCurlMock::reset();

if (!function_exists(__NAMESPACE__ . '\\curl_init')) {
    function curl_init(string $url)
    {
        OpenRouterCurlMock::setInitUrl($url);

        return (object) ['url' => $url];
    }
}

if (!function_exists(__NAMESPACE__ . '\\curl_setopt_array')) {
    /**
     * @param mixed $handle
     * @param array<int|string, mixed> $options
     */
    function curl_setopt_array($handle, array $options): bool
    {
        OpenRouterCurlMock::setOptions($options);

        return true;
    }
}

if (!function_exists(__NAMESPACE__ . '\\curl_exec')) {
    /**
     * @param mixed $handle
     */
    function curl_exec($handle)
    {
        return OpenRouterCurlMock::body();
    }
}

if (!function_exists(__NAMESPACE__ . '\\curl_error')) {
    /**
     * @param mixed $handle
     */
    function curl_error($handle): string
    {
        return OpenRouterCurlMock::error();
    }
}

if (!function_exists(__NAMESPACE__ . '\\curl_getinfo')) {
    /**
     * @param mixed $handle
     * @param int $option
     */
    function curl_getinfo($handle, int $option): int
    {
        return OpenRouterCurlMock::statusCode();
    }
}

if (!function_exists(__NAMESPACE__ . '\\curl_close')) {
    /**
     * @param mixed $handle
     */
    function curl_close($handle): void
    {
    }
}
