<?php

declare(strict_types=1);

namespace App\KeywordPipeline\AdCopy;

use RuntimeException;

class OpenRouterClient
{
    /**
     * @param array<int, array<string, string>> $messages
     * @param array<string, mixed> $jsonSchema
     * @return array<string, mixed>
     */
    public function chatJson(string $apiKey, string $model, array $messages, array $jsonSchema): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP curl extension is required for OpenRouter requests.');
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.2,
            'provider' => [
                'require_parameters' => true,
                'allow_fallbacks' => true,
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'ad_copy_rows',
                    'strict' => true,
                    'schema' => $jsonSchema,
                ],
            ],
        ];

        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');

        if ($ch === false) {
            throw new RuntimeException('Unable to initialize OpenRouter request.');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'HTTP-Referer: http://localhost',
                'X-OpenRouter-Title: Site.pro Vibecoding Test',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);

        $body = curl_exec($ch);

        if ($body === false) {
            $error = $this->redactSecrets(curl_error($ch), [$apiKey]);
            $this->close($ch);

            throw new RuntimeException('OpenRouter request failed: ' . $error);
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->close($ch);

        if ($statusCode >= 400) {
            throw new RuntimeException('OpenRouter HTTP error ' . $statusCode . '.');
        }

        $decoded = json_decode((string) $body, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('OpenRouter returned invalid JSON response.');
        }

        $content = $decoded['choices'][0]['message']['content'] ?? null;

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('OpenRouter returned empty message content.');
        }

        $messageJson = json_decode($content, true);

        if (!is_array($messageJson)) {
            throw new RuntimeException('OpenRouter returned invalid JSON message content.');
        }

        return $messageJson;
    }

    /**
     * @param resource|object $ch
     */
    private function close($ch): void
    {
        if (PHP_VERSION_ID < 80000) {
            curl_close($ch);
        }
    }

    /**
     * @param array<int, string> $secrets
     */
    private function redactSecrets(string $message, array $secrets): string
    {
        foreach ($secrets as $secret) {
            if ($secret !== '') {
                $message = str_replace($secret, '[redacted-openrouter-key]', $message);
            }
        }

        return (string) preg_replace(
            '/\bsk-or-(?:v1-)?[A-Za-z0-9][A-Za-z0-9_-]{24,}\b/',
            '[redacted-openrouter-key]',
            $message
        );
    }
}
