# Vibecoding App

Yii2 web/console application for keyword import, cleanup, preview, AI fallback, and Google Ads CSV export.

## Local Commands

```bash
composer validate --strict
composer qa
composer test
composer phpstan
composer qa:security
php yii keyword/smoke
php yii keyword/validate-export
```

Expected `composer qa` chain:

- Composer strict validation passes.
- PHPStan reports no errors.
- Unit tests pass.
- CLI smoke imports 8 sample rows and exports 4 CSV rows.
- Export validation prints `Export looks valid.`
- Security checklist confirms no real-looking OpenRouter key outside local env files, no configured key in generated database/export/log artifacts, and no source/config/doc/test file over 500 lines.

## Console Flow

```bash
php yii keyword/db-init
php yii user/ensure
php yii keyword/import-samples
php yii keyword/export-samples
php yii keyword/ai-preview
php yii keyword/validate-export
```

The default export is:

```text
runtime/export/google_ads_import.csv
```

The default SQLite database comes from `DB_DSN` and falls back to:

```text
../database/app.sqlite
```

## Web Flow

Run from the repository root:

```bash
make up
make smoke-docker
```

Open http://127.0.0.1:8080 and log in with `admin` / `admin123` unless `.env` overrides `ADMIN_LOGIN` and `ADMIN_PASSWORD`.

Smoke path:

- `/upload`: import bundled samples or upload a keyword file.
- `/admin/keywords`: inspect imported rows, statuses, and removal reasons.
- `/preview`: inspect Google Ads rows before export.
- `/ai-preview?mode=template`: verify deterministic AI fallback without a key.
- `/export`: download CSV.
- `/health`: verify `status: ok`.

## OpenRouter

OpenRouter generation is optional. Put `OPENROUTER_API_KEY` and `OPENROUTER_MODEL` in `../.env` to use it. Without a key, or when OpenRouter fails, the app returns template-generated rows. Real keys should stay in `.env`; QA scans generated artifacts and source-like files for accidental key leaks.
