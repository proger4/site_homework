# План Mixpanel events и KPI dashboard

## Страницы

Вторая задача `#illustrator` должна содержать минимум 4 страницы:

1. Title page — минимум 2 кнопки: `Start demo`, `View portfolio`.
2. Portfolio/work page.
3. About/process page.
4. Contact/CTA page.

## События

### Page views

```text
page_view
- page_slug
- page_title
- referrer
- viewport_type
- user_id/session_id
```

### Buttons

```text
button_click
- page_slug
- button_id
- button_label
- button_position
- user_id/session_id
```

## KPI и графики

| KPI | Формула | График |
|---|---|---|
| Page views by page | count(page_view), grouped by page_slug | Bar chart |
| CTA click-through rate | button_click по CTA / title page_view | Line chart |
| Portfolio engagement | portfolio page_view / title page_view | Funnel |
| Contact intent rate | contact CTA clicks / total sessions | Line chart |
| Mobile share | mobile sessions / all sessions | Donut или stacked bar |
| Drop-off by page | visitors на page N - visitors на следующей page | Funnel |

## Чеклист для sharing access

- [ ] Создать Mixpanel project.
- [ ] Добавить collaborator email от автора задания.
- [ ] Поделиться dashboard link.
- [ ] Добавить screenshots в repository evidence folder.
