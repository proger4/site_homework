-include .env
export

VIBECODING_PORT ?= 8080

.PHONY: up down logs ps build config shell app-install composer-validate db-init user-ensure import-samples export-samples ai-preview ai-preview-docker test phpstan qa qa-security smoke smoke-docker validate-export

up:
	docker compose up -d --build

down:
	docker compose down

logs:
	docker compose logs -f --tail=120

ps:
	docker compose ps

build:
	docker compose build

config:
	docker compose config --quiet

shell:
	docker compose exec app sh

app-install:
	cd app && composer install

composer-validate:
	cd app && composer validate --strict

db-init:
	cd app && php yii keyword/db-init

user-ensure:
	cd app && php yii user/ensure

import-samples:
	cd app && php yii keyword/import-samples

export-samples:
	cd app && php yii keyword/export-samples

ai-preview:
	cd app && php yii keyword/ai-preview

ai-preview-docker:
	curl -fsSL -c app/runtime/ai-cookie.txt -d "username=$${ADMIN_LOGIN:-admin}" -d "password=$${ADMIN_PASSWORD:-admin123}" http://127.0.0.1:$(VIBECODING_PORT)/login >/dev/null
	curl -fsS -b app/runtime/ai-cookie.txt 'http://127.0.0.1:$(VIBECODING_PORT)/ai-preview?mode=template' | grep 'AI Preview'

test:
	cd app && composer test

phpstan:
	cd app && composer phpstan

qa:
	cd app && composer qa

qa-security:
	cd app && composer qa:security

smoke:
	cd app && php yii keyword/smoke

smoke-docker:
	curl -fsS http://127.0.0.1:$(VIBECODING_PORT)/health | grep '"status": "ok"'
	curl -fsS http://127.0.0.1:$(VIBECODING_PORT)/ | grep 'Upload or import samples'
	curl -fsS http://127.0.0.1:$(VIBECODING_PORT)/login | grep 'Admin Login'
	curl -fsSL -c app/runtime/smoke-cookie.txt -d "username=$${ADMIN_LOGIN:-admin}" -d "password=$${ADMIN_PASSWORD:-admin123}" http://127.0.0.1:$(VIBECODING_PORT)/login | grep 'Admin Keywords'
	curl -fsS -b app/runtime/smoke-cookie.txt http://127.0.0.1:$(VIBECODING_PORT)/upload | grep 'Import bundled sample files'
	curl -fsS -b app/runtime/smoke-cookie.txt http://127.0.0.1:$(VIBECODING_PORT)/admin/keywords | grep 'removal_reason'
	curl -fsS -b app/runtime/smoke-cookie.txt http://127.0.0.1:$(VIBECODING_PORT)/preview | grep 'Future Google Ads CSV rows'
	curl -fsS -b app/runtime/smoke-cookie.txt 'http://127.0.0.1:$(VIBECODING_PORT)/ai-preview?mode=template' | grep 'template-fallback'
	curl -fsS -b app/runtime/smoke-cookie.txt http://127.0.0.1:$(VIBECODING_PORT)/export | grep 'Campaign'
	docker compose exec -T app php app/yii keyword/smoke

validate-export:
	cd app && php yii keyword/validate-export
