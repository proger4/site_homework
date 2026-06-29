# Yii1 Comments Test

Runnable Yii1 comment system with Docker, SQLite, migrations, admin moderation, and a Node.js WebSocket notifier.

## Start

```bash
cp .env.example .env
make up
```

Open:

- Frontend: http://localhost:8080
- Admin: http://localhost:8080/admin/login
- WebSocket: ws://localhost:3001

Demo credentials from `.env.example`: `admin` / `admin123`.

## Makefile

```bash
make up
make down
make logs
make ps
make build
make config
make migrate
make admin-user
make seed
make shell
make smoke
make test
```

On startup, the `php` container runs migrations, ensures the admin user, and seeds demo data.
`make test` runs PHPUnit integration tests against an in-memory SQLite database.

## Notes

- Local `.env` and optional `app/protected/config/*.local.php` overrides are ignored by git.
- Copy `app/protected/config/main.local.example.php` or `console.local.example.php` only when a Yii config override is needed.
- Default DB is SQLite at `database/app.sqlite`; override it through `DB_DSN`, `DB_USERNAME`, and `DB_PASSWORD`.
- WebSocket URLs are derived from `WS_PUBLIC_HOST`, `WS_INTERNAL_HOST`, and `WS_PORT`; there are no separate browser/broadcast URL env values to drift apart.
- Runtime stack: PHP 7.4, PHP-FPM, Nginx, SQLite, Yii1, Node.js WebSocket server.
