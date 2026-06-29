# Граница системы marketing automation

## Что уже есть в проекте

Входные файлы для исходной задачи уже лежат в `data/keyword-samples/`:

| Файл | Источник | Формат | Контракт |
|---|---|---|---|
| `google_ads_keywords.csv` | Site.pro keywords, уже используемые в Google Ads | CSV | `keyword`, `language`, `volume`, `cpc`, `target_url` |
| `search_console_queries.csv` | Site.pro queries из Search Console | CSV | `query`, `language`, `volume`, `cpc`, `target_url` |
| `search_console_queries.json` | Site.pro queries из Search Console | JSON | те же поля, что в CSV |
| `ahrefs_organic_keywords.csv` | Site.pro organic keywords из Ahrefs | CSV | `keyword`, `language`, `volume`, `cpc`, `target_url` |
| `ahrefs_paid_keywords.csv` | competitor paid keywords из Ahrefs | CSV | `competitor`, `keyword`, `language`, `volume`, `cpc`, `target_url` |

Есть также `tools/python/validate_exports.py`: он фиксирует минимальный контракт Google Ads export с колонками `Campaign`, `Ad Group`, `Keyword`, `Final URL`, `Headline 1`, `Headline 2`, `Headline 3`, `Description 1`, `Description 2`, `Language`.

## Детерминированная часть без гадания

Эта часть должна работать одинаково при каждом запуске и строиться на контрактах файлов:

1. Upload CSV/JSON и создание `import_batch`.
2. Распознавание источника по имени файла или выбранному source type.
3. Приведение `keyword` и `query` к единому `keyword_text`.
4. Проверка обязательных полей: `keyword_text`, `language`, `volume`, `cpc`, `target_url`.
5. Нормализация `keyword_text`: lowercase, trim, схлопывание пробелов, базовая чистка пунктуации.
6. Сохранение исходной строки, нормализованной строки, source, batch и ошибок парсинга.
7. Удаление дублей по ключу `normalized_keyword + language + target_url`.
8. Удаление brand names по редактируемому списку, например `site.pro`, `site pro`.
9. Удаление already-used keywords из `google_ads_keywords.csv`.
10. Удаление forbidden и junk по редактируемым спискам.
11. Volume filter по настройкам min/max.
12. Grouping by `language` и `target_url`.
13. Preview будущих `Campaign` и `Ad Group`.
14. Export в Google Ads CSV.
15. Validation report по обязательным колонкам, длинам headline/description, пустым URL/language и дублям.

Удаленные строки нельзя терять: у каждой должен быть `status` и `removal_reason`, чтобы admin area объясняла результат.

## Где нужна intelligence

Intelligence нужна только там, где контрактов входных файлов недостаточно:

1. Junk classification для неоднозначных keyword, которые не совпали со списком forbidden/junk.
2. Рекомендация `target_url`, если URL отсутствует, слишком общий или конфликтует с intent keyword.
3. Генерация `Headline 1-3` и `Description 1-2` на языке `language`.
4. Мягкая кластеризация ad groups по intent, если группировки `language + target_url` мало.
5. Объяснение сомнительных решений человеку в preview.

Правило: intelligence предлагает, deterministic pipeline проверяет. Автоматически удалять или экспортировать AI-решения без preview нельзя.

## Минимальная задача на реализацию

Сделать Yii2 app в `vibecoding/` с четырьмя страницами:

1. `/upload` — upload CSV/JSON, выбор source type, список batches.
2. `/admin/keywords` — таблица всех строк с source, original keyword/query, normalized keyword, language, volume, cpc, target_url, status, removal_reason.
3. `/preview` — сгруппированный preview по language и target_url, будущие ad groups и generated ads.
4. `/export/google-ads` — скачивание CSV и validation report.

Для первого MVP intelligence можно заменить deterministic templates для ads, но интерфейс должен явно показывать, где позже будет AI generation.

## Задачка на подумать

Нужно решить, кто имеет право принимать спорные решения:

1. Brand list: удаляем только `site.pro/site pro` или все вариации бренда, включая misspellings?
2. Junk threshold: keyword с низким volume удалять автоматически или отправлять в review?
3. Landing URL: доверяем `target_url` из файла или intelligence может предложить другой URL?
4. AI ads: разрешаем сразу попадать в export или только после ручного approve?
5. Competitor paid keywords: использовать как источник новых идей или помечать их отдельным risk flag?

Без этих решений можно сделать import, admin, deterministic filters, preview и export. Но нельзя честно утверждать, что система “умно” готовит Google Ads без человеческого approve.
