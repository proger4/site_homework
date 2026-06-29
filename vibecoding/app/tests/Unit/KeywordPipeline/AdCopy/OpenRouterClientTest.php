<?php

declare(strict_types=1);

namespace Tests\Unit\KeywordPipeline\AdCopy;

use App\KeywordPipeline\AdCopy\OpenRouterAdCopySchema;
use App\KeywordPipeline\AdCopy\OpenRouterClient;
use App\KeywordPipeline\AdCopy\OpenRouterCurlMock;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class OpenRouterClientTest extends TestCase
{
    protected function setUp(): void
    {
        OpenRouterCurlMock::reset();
    }

    public function testSendsExpectedChatJsonRequestAndReturnsDecodedMessageContent(): void
    {
        $client = new OpenRouterClient();
        $messages = [
            ['role' => 'system', 'content' => 'Generate ad copy.'],
            ['role' => 'user', 'content' => '{"keywords":[]}'],
        ];

        $response = $client->chatJson(
            'test-api-key',
            'openai/gpt-4.1-mini',
            $messages,
            OpenRouterAdCopySchema::schema()
        );

        $state = OpenRouterCurlMock::state();
        $options = $state['options'];
        $payload = json_decode((string) $options[CURLOPT_POSTFIELDS], true);

        self::assertSame('https://openrouter.ai/api/v1/chat/completions', $state['initUrl']);
        self::assertSame(true, $options[CURLOPT_POST]);
        self::assertSame(true, $options[CURLOPT_RETURNTRANSFER]);
        self::assertContains('Authorization: Bearer test-api-key', $options[CURLOPT_HTTPHEADER]);
        self::assertContains('Content-Type: application/json', $options[CURLOPT_HTTPHEADER]);
        self::assertSame('openai/gpt-4.1-mini', $payload['model']);
        self::assertSame($messages, $payload['messages']);
        self::assertSame(0.2, $payload['temperature']);
        self::assertSame('json_schema', $payload['response_format']['type']);
        self::assertSame(OpenRouterAdCopySchema::schema(), $payload['response_format']['json_schema']['schema']);
        self::assertTrue($payload['response_format']['json_schema']['strict']);
        self::assertSame('website builder', $response['ads'][0]['keyword']);
    }

    public function testRedactsOpenRouterApiKeyFromTransportErrors(): void
    {
        $secret = 'test-openrouter-secret';
        OpenRouterCurlMock::configure([
            'execResult' => false,
            'error' => 'connection failed for Authorization: Bearer ' . $secret,
        ]);

        try {
            (new OpenRouterClient())->chatJson(
                $secret,
                'openai/gpt-4.1-mini',
                [['role' => 'user', 'content' => '{}']],
                OpenRouterAdCopySchema::schema()
            );
        } catch (RuntimeException $e) {
            self::assertFalse(strpos($e->getMessage(), $secret) !== false);
            self::assertContains('[redacted-openrouter-key]', $e->getMessage());

            return;
        }

        throw new RuntimeException('Expected OpenRouter transport error.');
    }

    /**
     * @dataProvider invalidResponseProvider
     */
    public function testThrowsForInvalidResponses(array $mockState, string $expectedMessage): void
    {
        OpenRouterCurlMock::configure($mockState);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);

        (new OpenRouterClient())->chatJson(
            'test-api-key',
            'openai/gpt-4.1-mini',
            [['role' => 'user', 'content' => '{}']],
            OpenRouterAdCopySchema::schema()
        );
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>, 1: string}>
     */
    public function invalidResponseProvider(): iterable
    {
        yield 'curl failure' => [
            ['execResult' => false, 'error' => 'connection refused'],
            'OpenRouter request failed: connection refused',
        ];

        yield 'http error' => [
            ['statusCode' => 429],
            'OpenRouter HTTP error 429.',
        ];

        yield 'invalid outer json' => [
            ['body' => 'not-json'],
            'OpenRouter returned invalid JSON response.',
        ];

        yield 'empty message content' => [
            ['body' => json_encode(['choices' => [['message' => ['content' => '']]]])],
            'OpenRouter returned empty message content.',
        ];

        yield 'invalid message json' => [
            ['body' => json_encode(['choices' => [['message' => ['content' => 'not-json']]]])],
            'OpenRouter returned invalid JSON message content.',
        ];
    }
}
