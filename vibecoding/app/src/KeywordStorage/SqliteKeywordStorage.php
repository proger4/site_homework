<?php

declare(strict_types=1);

namespace App\KeywordStorage;

use App\KeywordImport\Domain\KeywordImportRow;
use App\KeywordImport\Domain\KeywordSource;
use App\KeywordPipeline\Contract\NormalizedKeywordRow;
use App\KeywordPipeline\Contract\ProcessedKeywordRow;
use App\KeywordPipeline\KeywordGroupBuilder;

final class SqliteKeywordStorage
{
    private \PDO $pdo;

    public function __construct(?string $dsn = null, ?string $username = null, ?string $password = null)
    {
        $dsn = $dsn ?: self::defaultDsn();
        $this->ensureSqliteDirectory($dsn);

        $this->pdo = new \PDO(
            $dsn,
            $username ?: null,
            $password ?: null,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function defaultDsn(): string
    {
        $envDsn = getenv('DB_DSN');

        if (is_string($envDsn) && $envDsn !== '') {
            return self::normalizeDsn($envDsn);
        }

        return 'sqlite:' . dirname(__DIR__, 3) . '/database/app.sqlite';
    }

    public static function fromEnvironment(): self
    {
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        return new self(
            self::defaultDsn(),
            $username === false ? null : $username,
            $password === false ? null : $password
        );
    }

    public function initialize(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS keyword_import_rows (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                source TEXT NOT NULL,
                keyword_text TEXT NOT NULL,
                original_keyword TEXT NOT NULL,
                language TEXT NOT NULL,
                volume INTEGER NOT NULL,
                cpc REAL NOT NULL,
                target_url TEXT NOT NULL,
                competitor TEXT,
                source_file TEXT NOT NULL,
                row_number INTEGER NOT NULL,
                raw_payload TEXT NOT NULL,
                normalized_keyword TEXT NOT NULL DEFAULT \'\',
                status TEXT NOT NULL DEFAULT \'active\',
                removal_reason TEXT,
                landing_page_suggestion TEXT,
                ad_group_suggestion TEXT,
                imported_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->ensureColumn('normalized_keyword', 'TEXT NOT NULL DEFAULT \'\'');
        $this->ensureColumn('status', 'TEXT NOT NULL DEFAULT \'active\'');
        $this->ensureColumn('removal_reason', 'TEXT');
        $this->ensureColumn('landing_page_suggestion', 'TEXT');
        $this->ensureColumn('ad_group_suggestion', 'TEXT');

        $this->pdo->exec(
            'CREATE UNIQUE INDEX IF NOT EXISTS idx_keyword_import_rows_source_file_row
                ON keyword_import_rows (source, source_file, row_number)'
        );
    }

    /**
     * @param array<int, KeywordImportRow> $rows
     */
    public function replaceRows(array $rows): int
    {
        return $this->replaceProcessedRows((new KeywordGroupBuilder())->process($rows));
    }

    /**
     * @param array<int, ProcessedKeywordRow> $rows
     */
    public function replaceProcessedRows(array $rows): int
    {
        $this->initialize();

        $this->pdo->beginTransaction();

        try {
            $this->pdo->exec('DELETE FROM keyword_import_rows');

            $statement = $this->pdo->prepare(
                'INSERT INTO keyword_import_rows (
                    source,
                    keyword_text,
                    original_keyword,
                    language,
                    volume,
                    cpc,
                    target_url,
                    competitor,
                    source_file,
                    row_number,
                    raw_payload,
                    normalized_keyword,
                    status,
                    removal_reason,
                    landing_page_suggestion,
                    ad_group_suggestion
                ) VALUES (
                    :source,
                    :keyword_text,
                    :original_keyword,
                    :language,
                    :volume,
                    :cpc,
                    :target_url,
                    :competitor,
                    :source_file,
                    :row_number,
                    :raw_payload,
                    :normalized_keyword,
                    :status,
                    :removal_reason,
                    :landing_page_suggestion,
                    :ad_group_suggestion
                )'
            );

            foreach ($rows as $processedRow) {
                $row = $processedRow->importRow();
                $statement->execute([
                    ':source' => $row->source()->value(),
                    ':keyword_text' => $row->keywordText(),
                    ':original_keyword' => $row->originalKeyword(),
                    ':language' => $row->language(),
                    ':volume' => $row->volume(),
                    ':cpc' => $row->cpc(),
                    ':target_url' => $row->targetUrl(),
                    ':competitor' => $row->competitor(),
                    ':source_file' => $row->sourceFile(),
                    ':row_number' => $row->rowNumber(),
                    ':raw_payload' => json_encode($row->rawPayload(), JSON_THROW_ON_ERROR),
                    ':normalized_keyword' => $processedRow->normalizedKeyword(),
                    ':status' => $processedRow->status(),
                    ':removal_reason' => $processedRow->removalReason(),
                    ':landing_page_suggestion' => $processedRow->landingPageSuggestion(),
                    ':ad_group_suggestion' => $processedRow->adGroupSuggestion(),
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }

        return count($rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allRows(): array
    {
        $this->initialize();

        return $this->pdo
            ->query('SELECT * FROM keyword_import_rows ORDER BY id ASC')
            ->fetchAll();
    }

    /**
     * @return array<int, KeywordImportRow>
     */
    public function importRows(): array
    {
        $rows = [];

        foreach ($this->allRows() as $row) {
            $rows[] = $this->importRowFromDatabaseRow($row);
        }

        return $rows;
    }

    /**
     * @return array<int, NormalizedKeywordRow>
     */
    public function activeNormalizedRows(): array
    {
        $rows = [];

        foreach ($this->allRows() as $row) {
            if (($row['status'] ?? '') !== ProcessedKeywordRow::STATUS_ACTIVE) {
                continue;
            }

            $normalizedKeyword = (string) ($row['normalized_keyword'] ?? '');
            $rows[] = new NormalizedKeywordRow(
                $this->importRowFromDatabaseRow($row),
                $normalizedKeyword === '' ? $this->normalizeKeyword((string) $row['keyword_text']) : $normalizedKeyword
            );
        }

        return $rows;
    }

    public function rowCount(): int
    {
        $this->initialize();

        return (int) $this->pdo->query('SELECT COUNT(*) FROM keyword_import_rows')->fetchColumn();
    }

    private function ensureColumn(string $name, string $definition): void
    {
        $statement = $this->pdo->query('PRAGMA table_info(keyword_import_rows)');
        $columns = $statement === false ? [] : $statement->fetchAll();

        foreach ($columns as $column) {
            if (($column['name'] ?? '') === $name) {
                return;
            }
        }

        $this->pdo->exec('ALTER TABLE keyword_import_rows ADD COLUMN ' . $name . ' ' . $definition);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function importRowFromDatabaseRow(array $row): KeywordImportRow
    {
        $rawPayload = json_decode((string) ($row['raw_payload'] ?? '{}'), true);

        return new KeywordImportRow(
            KeywordSource::fromString((string) $row['source']),
            (string) $row['keyword_text'],
            (string) $row['original_keyword'],
            (string) $row['language'],
            (int) $row['volume'],
            (float) $row['cpc'],
            (string) $row['target_url'],
            ($row['competitor'] ?? null) === null ? null : (string) $row['competitor'],
            (string) $row['source_file'],
            (int) $row['row_number'],
            is_array($rawPayload) ? $rawPayload : []
        );
    }

    private function normalizeKeyword(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', mb_strtolower($value, 'UTF-8')));
    }

    private function ensureSqliteDirectory(string $dsn): void
    {
        if (strpos($dsn, 'sqlite:') !== 0 || $dsn === 'sqlite::memory:') {
            return;
        }

        $path = substr($dsn, strlen('sqlite:'));
        $dir = dirname($path);

        if ($dir === '' || $dir === '.' || is_dir($dir)) {
            return;
        }

        if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create SQLite directory: ' . $dir);
        }
    }

    private static function normalizeDsn(string $dsn): string
    {
        if (strpos($dsn, 'sqlite:/app/database/') !== 0 || is_dir('/app')) {
            return $dsn;
        }

        return 'sqlite:' . dirname(__DIR__, 3) . '/database/' . basename(substr($dsn, strlen('sqlite:/app/database/')));
    }
}
