.PHONY: install composer-validate db-init import-samples export-samples ai-preview test phpstan qa qa-security smoke validate-export

install:
	composer install

composer-validate:
	composer validate --strict

db-init:
	php yii keyword/db-init

import-samples:
	php yii keyword/import-samples

export-samples:
	php yii keyword/export-samples

ai-preview:
	php yii keyword/ai-preview

test:
	composer test

phpstan:
	composer phpstan

qa:
	composer qa

qa-security:
	composer qa:security

smoke:
	php yii keyword/smoke

validate-export:
	php yii keyword/validate-export
