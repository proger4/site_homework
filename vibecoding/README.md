# Vibecoding

Workspace for the `#vibecoding` task: keyword fixtures and a runnable Dockerized keyword import/export/ad-copy app.

## Contents

- `app/tests/fixtures/keyword-samples/` — Google Ads, Search Console, and Ahrefs fixture exports used by the keyword commands.
- `app/` — minimal Yii2-oriented keyword import/export shell plus a small HTTP status surface with SQLite persistence.
- `docker-compose.yml` — local PHP runtime with SQLite and an exposed HTTP port, mirroring the environment flow used in `../php`.
- `IDEAS.md` — implementation direction.
- `../homework/vibecoding/` — decomposition and source material.

## Commands

Docker start:

```bash
cp .env.example .env
make up
make smoke-docker
```

Open: http://0.0.0.0:8080
Health: http://0.0.0.0:8080/health
AI preview: http://0.0.0.0:8080/ai-preview
Fast offline AI fallback preview: http://0.0.0.0:8080/ai-preview?mode=template

`make up` builds the PHP image, initializes `database/app.sqlite`, imports the keyword fixtures into SQLite, and starts PHP's built-in web server on `VIBECODING_PORT` (`8080` by default). The same port is used inside and outside Docker.

The default SQLite database is `database/app.sqlite`; override it through `DB_DSN`, `DB_USERNAME`, and `DB_PASSWORD`.
AI works out of the box through deterministic template fallback. To use real OpenRouter generation, put `OPENROUTER_API_KEY` and `OPENROUTER_MODEL` into `.env`, then run `make up` again. The same configuration is used by the web `/ai-preview` page and the CLI preview command. For a fast offline web check, use `/ai-preview?mode=template`.

```bash
make ai-preview
make ai-preview-docker
cd app && php yii keyword/ai-preview --apiKey=sk-or-... --model=openai/gpt-4.1-mini
```

Without an OpenRouter key the app uses deterministic template ad copy, so web preview, export, and smoke still work offline.

Keyword import/export shell on the host:

```bash
make db-init
make import-samples
make export-samples
make ai-preview
make ai-preview-docker
make test
make smoke
make smoke-docker
make validate-export
```

The app can also be called directly:

```bash
cd app
php yii keyword/import-samples
php yii keyword/export-samples
php yii keyword/ai-preview
php yii keyword/smoke
```

The default export is written to `app/runtime/export/google_ads_import.csv`.

```bash
make up
make down
make logs
make ps
make build
make config
make shell
```

`make validate-export` is pure PHP and validates the generated Google Ads CSV columns and text lengths.
`make test` runs unit/data-provider checks for the AI contracts and ad-copy generators without network calls.
