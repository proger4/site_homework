# Agents

This repository starts from a very small amount of project context. The agents below keep the test task focused, runnable, and easy to submit.

## Delivery Scoper

**Prompt:** "Разбери задание, выдели обязательный минимум, артефакты сдачи, чеклист Done, риски и самый короткий путь."

**What it does:**

- Cuts noise from the full assignment.
- Extracts the required minimum for the PHP/Yii1 test.
- Defines submission artifacts: repository link, run instructions, URLs, screenshots if needed.
- Keeps the scope from expanding into a product platform.

**Done checklist:**

- Yii1 app exists.
- Docker run path exists.
- Comment form works without login.
- Admin can login, edit, delete.
- Admin list receives new comments in real time.
- README explains how to run and submit.

## Yii/Docker Builder

**Prompt:** "Собери runnable Yii-проект в Docker по чеклисту, без лишней архитектуры, с админкой, CRUD, realtime и README."

**What it does:**

- Builds the Yii1 directory structure.
- Adds `docker-compose.yml`, Docker files, SQLite migration, and Node.js WebSocket service.
- Implements comments model, public form, admin CRUD.
- Adds a small WebSocket server for admin updates.
- Keeps dependencies minimal and visible in `composer.json`.

**Owned files:**

- `app/`
- `public/`
- `docker/`
- `websocket/`
- `docker-compose.yml`
- `app/protected/migrations/`
- `README.md`

## Submission QA

**Prompt:** "Проверь, что результат можно сдать: запуск, URL, скрины, README, acceptance criteria, лимиты текста, финальное письмо."

**What it does:**

- Verifies the repository can be launched from a clean checkout.
- Checks that public and admin URLs are documented.
- Confirms admin credentials are present.
- Checks acceptance criteria against the original assignment.
- Prepares a short final message for the reviewer.

**Reviewer note template:**

```text
Hello!

Here is the repository with the Yii1 commenting system:
<repo-url>

Run:
cd system/php
docker compose up --build

URLs:
Front-end: http://localhost:8080/
Admin: http://localhost:8080/admin/login
Login: admin
Password: admin123

Thank you!
```
