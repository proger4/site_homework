# Vibecoding

Dockerized Yii2 keyword import/export/ad-copy MVP for the `#vibecoding` task.

## URLs And Access

After `make up`:

- App: http://127.0.0.1:8080
- Health: http://127.0.0.1:8080/health
- Login: http://127.0.0.1:8080/login
- Upload/import: http://127.0.0.1:8080/upload
- Admin: http://127.0.0.1:8080/admin/keywords
- Preview: http://127.0.0.1:8080/preview
- AI fallback preview: http://127.0.0.1:8080/ai-preview?mode=template
- Export CSV: http://127.0.0.1:8080/export

Default local access is `admin` / `admin123`. Override it in `.env` with `ADMIN_LOGIN` and `ADMIN_PASSWORD`.

## Quick Start

```bash
cp .env.example .env
make up
make smoke-docker
```

`make up` builds the PHP 8.3 image, initializes `database/app.sqlite`, ensures the admin user, imports the bundled keyword fixtures, and starts PHP's built-in web server on `VIBECODING_PORT` (`8080` by default).

## QA Commands

```bash
make composer-validate
make qa
make smoke
make smoke-docker
make validate-export
```

Expected results:

- `make composer-validate`: `./composer.json is valid`.
- `make qa`: strict Composer validation, PHPStan, unit tests, CLI smoke, export validation, and security checklist pass.
- `make smoke`: imports 8 sample rows, exports 4 Google Ads rows, prints `Smoke passed.`
- `make smoke-docker`: checks health, login, upload/import page, admin table, preview, template AI fallback, CSV export, and container CLI smoke.
- `make validate-export`: prints `Export looks valid.`

`composer qa` inside `app/` runs the same local QA chain as `make qa`.

## Smoke Scenario

1. Upload/import: log in and open `/upload`; use "Import bundled sample files" or upload a CSV/JSON fixture.
2. Admin: open `/admin/keywords`; verify rows, `status`, `normalized_keyword`, and `removal_reason`.
3. Preview: open `/preview`; verify future Google Ads CSV rows before download.
4. AI fallback: open `/ai-preview?mode=template`; it must work without an OpenRouter key.
5. Export: open `/export` or run `make export-samples`; the CSV is written to `app/runtime/export/google_ads_import.csv`.

## AI And Secrets

OpenRouter is optional. Without `OPENROUTER_API_KEY`, the app uses deterministic template fallback for web preview, export, and smoke.

To enable real OpenRouter calls, put the key in `.env`:

```dotenv
OPENROUTER_API_KEY=
OPENROUTER_MODEL=openai/gpt-4.1-mini
```

Do not pass real keys inline in shared commands. `composer qa:security` checks for real-looking OpenRouter keys in source/config/docs/tests and scans generated database/export/log artifacts for the configured key. The app must not render or export the key in HTML, SQLite, logs, or CSV.

## Useful Commands

```bash
make app-install
make db-init
make user-ensure
make import-samples
make export-samples
make ai-preview
make ai-preview-docker
make test
make phpstan
make qa-security
make logs
make ps
make down
```

## File Size Rule

`composer qa:security` enforces a 500-line limit for source/config/doc/test files. Generated and ignored directories are excluded: `.git`, `app/vendor`, `app/var`, `app/runtime`, `database`, and `generated`.
