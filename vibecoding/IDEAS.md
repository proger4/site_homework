# Идеи для маркетинговой автоматизации Vibecoding

## Целевая форма

- Реализовать маркетинговую платформу как Yii2-приложение внутри `vibecoding/`, когда будет утвержден минимальный scope.
- Не смешивать ее с существующей Yii1-задачей в `../php`.
- Добавить Node.js socket-контейнер только если понадобится live-прогресс импорта или realtime-уведомления в админке.

## Поток данных

1. Импортировать CSV или JSON из Google Ads, Search Console, Ahrefs organic и Ahrefs paid competitor exports.
2. Нормализовать строки в единую таблицу keywords с полями `source`, `keyword/query`, `language`, `volume`, `cpc`, `target_url`, `competitor`, `import_batch`.
3. Запускать очистку как явный pipeline: junk, bad words, brand names, duplicates, already-used keywords, forbidden keywords, volume thresholds.
4. Группировать оставшиеся keywords по `language` и `target_url`.
5. Генерировать строки Google Ads с `Campaign`, `Ad Group`, `Keyword`, `Final URL`, `Headline 1-3`, `Description 1-2`, `Language`.
6. Показывать preview перед export и отдавать Google Ads import CSV.

## Детерминированная часть

- CSV/JSON schema mapping: `keyword` и `query` приводятся к единому `keyword_text`.
- Нормализация текста: lowercase, trim, схлопывание пробелов, базовая чистка пунктуации.
- Dedupe: сравнение по нормализованному `keyword_text`, `language`, `target_url`.
- Exclusion lists: brand names, forbidden keywords, already-used keywords, junk/bad words.
- Volume filter: минимальный и максимальный пороги задаются настройками.
- Audit trail: каждая удаленная строка получает `status` и `removal_reason`, а не исчезает молча.
- Export validation: заголовки до 30 символов, описания до 90 символов, обязательные `Final URL` и `Language`, отсутствие дублей в одном ad group.

## Intelligence-часть

- Классифицировать неоднозначный junk, где нет прямого совпадения со списком запретов.
- Предлагать landing page, если `target_url` пустой или явно слабый.
- Генерировать рекламные `Headline 1-3` и `Description 1-2` на языке keyword.
- Объяснять сомнительные удаления как review suggestions, а не как автоматическое удаление.
- Предлагать группировку ad groups, когда одного `target_url` недостаточно.

Intelligence не должна принимать необратимых решений без preview: она предлагает, deterministic pipeline валидирует и фиксирует результат.

## Экраны админки

- Imports: upload файлов, статус batch, counts по источникам, ошибки парсинга.
- Keywords: все нормализованные строки с source, status, reason, language, volume, URL.
- Filters: редактируемые списки junk, forbidden, brand, already-used keywords.
- Preview: grouped campaign/ad group view.
- Export: скачиваемый Google Ads CSV и validation report.
  #vibecoding — маркетинговая автоматизация на Yii2
  Расположение: runnable app пока не поставлен. Подготовительные docs, data, tools и идеи лежат в vibecoding/.

## Критерии приемки:

CSV/JSON import.
Admin area со всеми импортированными keyword data.
Фильтры bad keyword, junk, duplicate, brand, already-used, forbidden.
Volume filter.
Grouping by language.
Генерация ad copy на языке keyword, желательно с AI fallback только для творческой части.
Preview и Google Ads CSV export.
