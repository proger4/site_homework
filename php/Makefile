.PHONY: up down logs ps build smoke test phpstan qa migrate admin-user seed shell config

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

migrate:
	docker compose exec php php app/protected/yiic migrate --interactive=0

admin-user:
	docker compose exec php php app/protected/yiic adminuser ensure

seed:
	docker compose exec php php app/protected/yiic seed

shell:
	docker compose exec php sh

smoke:
	curl -s http://127.0.0.1:8080/ | grep 'Comments'
	curl -s -i -X POST http://127.0.0.1:8080/comment/create -H 'Content-Type: application/x-www-form-urlencoded' --data 'Comment%5Bname%5D=Smoke&Comment%5Bmessage%5D=Hello+from+php+Makefile+smoke' | grep '302 Found'
	curl -s http://127.0.0.1:8080/ | grep 'Hello from php Makefile smoke'

test:
	composer test

phpstan:
	composer phpstan

qa:
	composer qa
