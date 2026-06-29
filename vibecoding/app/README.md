# Vibecoding Keyword Shell

Minimal Yii2-oriented shell for the `#vibecoding` keyword import/export task.

The core import/export code is plain PHP under `src/`, so the smoke commands work even before Composer downloads Yii2. Yii2 can be installed later with `make install` without changing the import contracts. Import commands also persist rows to SQLite.

## Commands

```bash
php yii keyword/db-init
php yii keyword/import-samples
php yii keyword/export-samples
php yii keyword/ai-preview
php yii keyword/validate-export
php tests/run.php
php yii keyword/smoke
```

From this directory:

```bash
make smoke
make test
make validate-export
```

The default export is written to:

```text
runtime/export/google_ads_import.csv
```

The default SQLite database is read from `DB_DSN` and falls back to:

```text
../database/app.sqlite
```

## Acceptance Flow

```bash
php yii keyword/import-samples
php yii keyword/export-samples
php yii keyword/validate-export runtime/export/google_ads_import.csv
php yii keyword/ai-preview
```

OpenRouter AI is optional:

```bash
php yii keyword/ai-preview --apiKey=sk-or-... --model=openai/gpt-4.1-mini
```

If no key is passed, the command reads `OPENROUTER_API_KEY` from `../.env`. If no key is available or OpenRouter fails, the app returns template-generated rows.

Expected sample counts:

- `google_ads`: 2 rows
- `search_console`: 4 rows
- `ahrefs_organic`: 1 row
- `ahrefs_paid`: 1 row

## Scope

Implemented now:

- CSV/JSON import through typed providers/accessors.
- `iterable<KeywordImportRow>` as the public import contract.
- Source detection from filename or explicit `KeywordSource`.
- Raw payload, row number, source file, and import errors.
- Google Ads CSV export and validation-friendly smoke command.
- Normalization/filter/grouping/ad-copy preview path.
- Optional OpenRouter ad copy generation with template fallback.

Not implemented in this shell:

- Admin UI.
- Database persistence.
- Editable filter lists.
