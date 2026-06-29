<?php

declare(strict_types=1);

namespace App\Console;

final class Application
{
    private string $appRoot;
    private ?KeywordCommandFactory $commandFactory;

    public function __construct(string $appRoot, ?KeywordCommandFactory $commandFactory = null)
    {
        $this->appRoot = $appRoot;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @param array<int, string> $argv
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? 'help';
        $keywordCommand = $this->commandFactory()->create();

        try {
            switch ($command) {
                case 'keyword/import-samples':
                    return $keywordCommand->importSamples();
                case 'keyword/db-init':
                    return $keywordCommand->initDatabase();
                case 'keyword/export-samples':
                    return $keywordCommand->exportSamples($argv[2] ?? null);
                case 'keyword/ai-preview':
                    return $keywordCommand->aiPreview(array_slice($argv, 2));
                case 'keyword/validate-export':
                    return $keywordCommand->validateExport($argv[2] ?? null);
                case 'keyword/smoke':
                    return $keywordCommand->smoke();
                case 'help':
                default:
                    $this->printHelp($command);

                    return $command === 'help' ? 0 : 2;
            }
        } catch (\Throwable $e) {
            fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");

            return 1;
        }
    }

    private function printHelp(string $command): void
    {
        if ($command !== 'help') {
            echo "Unknown command: {$command}\n\n";
        }

        echo "Available commands:\n";
        echo "  keyword/db-init\n";
        echo "  keyword/import-samples\n";
        echo "  keyword/export-samples [targetPath]\n";
        echo "  keyword/ai-preview [--apiKey=...] [--model=openai/gpt-4.1-mini]\n";
        echo "  keyword/validate-export [targetPath]\n";
        echo "  keyword/smoke\n";
    }

    private function commandFactory(): KeywordCommandFactory
    {
        return $this->commandFactory ??= new KeywordCommandFactory($this->appRoot);
    }
}
