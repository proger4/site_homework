### Что сделано?

### Реальный статус сдачи

#### Готово как рабочий результат

- `system/vibecoding/` — рабочая Yii2 marketing automation платформа. Makefile унифицирован с `system/php/`: запуск через ``make up``, есть README.md.
- `system/php/` — runnable Yii1-приложение. Все запускается из коробки на Makefile как ``make up`` через Docker, есть README.md.
- `homework/charting/` — готовая статическая страница `index.html` с 4 in-page views, KPI dashboard и Mixpanel events; рядом лежат `solution.md` и декомпозиция ИИ `decomposition.md`.
- `homework/illustrator/` — готовый HTML-макет `orders-upgrades.html`, исходная картинка `img.png`, ссылка на Figma в `solution.md`, декомпозиция ИИ `decomposition.md`.
- `homework/math/` — готовый расчет и финальный ответ в `solution.md`, декомпозиция ИИ `decomposition.md`.
- `homework/speed/` — готовые evidence-файлы: screenshot typing test и видео; ссылки указаны в `solution.md`, декомпозиция ИИ `decomposition.md`.
- `homework/copyrighter/` — готовы два текстовых результата: `solution_1.md` и `solution_2.md`; для каждого рядом есть декомпозиция ИИ.
- `homework/hr/` — готов ответ по процессу поиска long-term salesperson в Литве: `how_to.md`; рядом декомпозиция ИИ.
- `homework/ui/` и `homework/hypothesis/` — готовы audit/гипотезы по title page; рядом декомпозиции ИИ.

#### Подготовлено, но не реализовано как полноценно runnable-продукт

- `homework/seo/`, `homework/adaptability/`, `homework/ux/`, `homework/accounting/`, `homework/bookkeeper/`, `homework/integration/`, `homework/sales/`, `homework/research/`, `homework/roles/`, `homework/technical/` — оформлены как мысли/черновики/декомпозиции, без полноценной финальной реализации.

#### Evidence / proof

- Скриншоты и видео лежат в `homework/screenshots/`.
- AI-декомпозиции лежат в соответствующих папках `homework/`, чтобы по каждой задаче была связка: решение + мысль/статус + декомпозиция от ИИ.

### Контекст
У меня был сразу следующий звонок.
Я на автомате просто начал делать всю домашку. 
Потом понял, что ее не надо делать всю (а только часть php и vibecoding).
Те аспекты, что не делались полностью - описать черновик решения.
Снимал live скринкасты как шел процесс, но они много весят.
Некоторые видео процесса залил как отдельные ссылки:

- https://disk.yandex.ru/i/z5C8xMwhqESM4Q
- https://disk.yandex.ru/i/hSAKS1fkwtqL4w
- https://disk.yandex.ru/i/dKN5kognymzDgw
- https://disk.yandex.ru/i/sk49ttPsRyIKnA
- полировка проекта PHP: https://disk.yandex.ru/i/9ZSzNgd-9Kp1yg

По секции #growth понял, что мне надо ботать матчасть про стартапы и метрики конкретно ...

Чтобы не бросать на пол пути просто решил формально сделать ее всю.
Конечно надо держать фокус, поэтому я сразу же решил не упарываться и формально заметками закрыть такие еще не тронутые аспекты как:

Как мысли по данным аспектам:
- #seo:
  > Декомпозиция ИИ: `homework/seo/decomposition.md`
  > Задача полностью автоматизируется. Playwright / httrack / puppeter / просто robots.txt и так далее. Насчет веса ссылок - порисечить за актуальные факторы (на чем ссылка, название, наличие og атрибутов на целевой странице). 
  > Собрать граф, можно graphviz. Лучше всего выделить кластеры страниц по slug. Без общей архитектуры нет смысла в каше из страниц копаться. 
- #adaptability:
  > Декомпозиция ИИ: `homework/adaptability/decomposition.md`
  > В целом это было на этапе отклика, но у меня есть идея персонального сайта и ряда проектов, там даже есть видео одного из завершенных DIY проектов, структура и контент-план на Canvas в GPT. 
- #ux:
  > Декомпозиция ИИ: `homework/ux/decomposition.md`
  > Сразу могу подсветить боли на CJM. С мобильного сложного зарегистрироваться и редактировать сайт. Нужны стабильные UX паттерны и mobile-first. Я не помню весь процесс, но было немного больно.
- #accountant:
  > Декомпозиция ИИ: `homework/accounting/decomposition.md`
  > ничего в этом не понимаю, но сразу же перевел и составил план.
- #bookkeeper, #integration, #sales:
  > Декомпозиция ИИ: `homework/bookkeeper/decomposition.md`, `homework/integration/decomposition.md`, `homework/sales/decomposition.md`
  > Интересно, что есть понимание функционала платформы, нюансов с платежами и архитектуры интеграций, потом изучу для целостного понимая продукта.
