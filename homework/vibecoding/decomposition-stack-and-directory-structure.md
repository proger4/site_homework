# Стек и структура директорий

## Стек

### Существующий PHP backend

- PHP 7.4 для совместимости с Yii1.
- Yii1 для тестовой задачи с комментариями.
- MariaDB 10.11 для persistence.
- Ratchet WebSocket server для real-time обновлений админки.
- Composer для PHP dependencies.

### Frontend

- Server-rendered PHP/Yii views.
- Plain JavaScript для WebSocket updates.
- Без SPA-зависимости в Yii1-задаче, чтобы scope оставался простым для проверки.

### Vibecoding / Automation

- Python utility scripts для генерации sample CSV/JSON и export validation.
- Запланированное Yii2-приложение для marketing automation.
- Запланированный Node.js container для socket updates, если это понадобится в detailed checklist.

### Infrastructure

- Docker Compose.
- Apache PHP container.
- MariaDB container.
- Dedicated WebSocket container.

## Структура директорий

```text
.
├── php/
│   ├── app/
│   ├── console/
│   ├── public/
│   ├── sql/
│   ├── tests/
│   ├── Dockerfile
│   ├── docker-compose.yml
│   └── README.md
├── vibecoding/
│   ├── data/
│   ├── tools/python/
│   ├── AGENTS.md
│   ├── IDEAS.md
│   ├── Makefile
│   └── README.md
├── homework/
│   ├── charting/
│   ├── math/
│   ├── speed/
│   └── vibecoding/
└── README.md
```
