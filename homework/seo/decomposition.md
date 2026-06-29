# Шаблон аудита backlink profile Site.pro

> Перед финальной сдачей нужен export из Ahrefs/Semrush/Google Search Console.

## Входные данные

- Backlinks export.
- Referring domains export.
- Broken backlinks export.
- Best by links pages.
- Структура сайта с subdomains и language directories.

## 3 broken links

| Broken source URL | Target URL | Referring domain | Исправление |
|---|---|---|---|
| уточнить | уточнить | уточнить | Восстановить страницу или поставить 301 redirect |
| уточнить | уточнить | уточнить | Восстановить страницу или поставить 301 redirect |
| уточнить | уточнить | уточнить | Восстановить страницу или поставить 301 redirect |

## 3 ссылки, которые уменьшают link juice

| URL | Проблема | Почему снижает ценность | Исправление |
|---|---|---|---|
| уточнить | Слишком много internal/external links | Размывает authority | Убрать лишние ссылки |
| уточнить | Redirect chain | Тратит crawl/link equity | Заменить на final URL |
| уточнить | Неверный language/canonical target | Отправляет ценность в слабый duplicate | Исправить canonical/internal link |

## 3 быстрых предложения

1. Заменить internal links с redirect chain на final canonical URLs.
2. Добавить 301 redirects для broken pages, которые еще получают backlinks.
3. Объединить duplicate language/subdomain pages через корректные canonical/hreflang.

## Одностраничная структура сайта, максимум 20-30 items

```text
site.pro
├── /
├── /Website-Builder/
├── /AI-Website-Builder/
├── /White-Label/
├── /Hosting-Provider/
├── /Plugins/
├── /Pricing/
├── /Demo/
├── /Support/
├── /Blog/
├── /en/
├── /lt/
├── /ru/
├── /es/
├── builder.site.pro
├── api.site.pro
├── help.site.pro
└── partners.site.pro
```

## Главная SEO-проблема структуры

Потенциальная проблема: language/subdomain duplication может дробить authority, если canonicals, hreflang и internal linking настроены непоследовательно. Аудит должен проверить, конкурируют ли одинаковые commercial pages между собой на разных языках и subdomains.
