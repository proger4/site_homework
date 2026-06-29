<?php

declare(strict_types=1);

namespace PHPUnit\Framework;

abstract class TestCase
{
    private ?string $expectedException = null;
    private string $expectedExceptionMessage = '';

    public static function assertSame($expected, $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw new \RuntimeException(self::message($message, 'Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true)));
        }
    }

    public static function assertTrue(bool $actual, string $message = ''): void
    {
        if (!$actual) {
            throw new \RuntimeException(self::message($message, 'Expected true.'));
        }
    }

    public static function assertFalse(bool $actual, string $message = ''): void
    {
        if ($actual) {
            throw new \RuntimeException(self::message($message, 'Expected false.'));
        }
    }

    public static function assertCount(int $expected, array $actual, string $message = ''): void
    {
        self::assertSame($expected, count($actual), $message);
    }

    public static function assertContains($needle, $haystack, string $message = ''): void
    {
        $found = is_array($haystack)
            ? in_array($needle, $haystack, true)
            : strpos((string) $haystack, (string) $needle) !== false;

        if (!$found) {
            throw new \RuntimeException(self::message($message, 'Expected haystack to contain ' . var_export($needle, true) . '.'));
        }
    }

    public function expectException(string $class): void
    {
        $this->expectedException = $class;
    }

    public function expectExceptionMessage(string $message): void
    {
        $this->expectedExceptionMessage = $message;
    }

    public function __resetExpectation(): void
    {
        $this->expectedException = null;
        $this->expectedExceptionMessage = '';
    }

    public function __verifyExpectation(?\Throwable $throwable): void
    {
        if ($this->expectedException === null) {
            if ($throwable !== null) {
                throw $throwable;
            }

            return;
        }

        if ($throwable === null) {
            throw new \RuntimeException('Expected exception ' . $this->expectedException . ', got none.');
        }

        if (!$throwable instanceof $this->expectedException) {
            throw new \RuntimeException('Expected exception ' . $this->expectedException . ', got ' . get_class($throwable) . '.');
        }

        if ($this->expectedExceptionMessage !== '' && strpos($throwable->getMessage(), $this->expectedExceptionMessage) === false) {
            throw new \RuntimeException(
                'Expected exception message containing ' . var_export($this->expectedExceptionMessage, true)
                . ', got ' . var_export($throwable->getMessage(), true) . '.'
            );
        }
    }

    private static function message(string $message, string $fallback): string
    {
        return $message === '' ? $fallback : $message . ': ' . $fallback;
    }
}
