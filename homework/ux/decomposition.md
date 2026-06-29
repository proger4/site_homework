# CJM: invoice после регистрации

## Flow

```text
Landing → Registration → Email confirmation → First login → Company setup → Invoice creation → Client/product entry → Preview → Save/send/download
```

## CJM table

| Step | Цель пользователя | Action | Risk | Visual solution |
|---|---|---|---|---|
| Landing | Понять продукт | Читает offer | Непонятно, доступно ли invoicing | Добавить прямой CTA “Create invoice” |
| Registration | Создать account | Заполняет form | Слишком много fields | Использовать progressive setup |
| First login | Понять следующий шаг | Видит dashboard | Пустой state сбивает с толку | Показать invoice creation wizard |
| Company setup | Добавить legal data | Вводит company info | Обязательные fields появляются поздно | Добавить checklist progress |
| Invoice creation | Создать document | Добавляет client/items | Form выглядит сложной | Разбить на blocks с preview |
| Preview | Проверить invoice | Проверяет output | Путаница с language/VAT | Добавить language/VAT toggles рядом с preview |
| Send/download | Завершить задачу | Downloads или sends | CTA непонятен | Primary button: “Download invoice” |

## 1-2 conversion problems и visual fixes

1. Проблема: после registration users могут не понимать следующий шаг. Исправление: dashboard empty state с одним primary action: “Create your first invoice”.
2. Проблема: invoice form может казаться тяжелой. Исправление: two-column layout — form слева, live invoice preview справа.
