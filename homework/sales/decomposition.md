# Запрос на Bitcoin accounting

## Позиция

Я бы не одобрял product investment на 30 000 EUR ради разового client payment на 3 000 EUR, если эта feature заранее не является стратегически важной и переиспользуемой для подтвержденного сегмента.

## Логика решения

1. Запросить доказательства будущего demand: число потенциальных clients, expected revenue, timeline и commitment level.
2. Разделить запрос на paid discovery и implementation.
3. Предложить меньший первый этап: analysis, technical specification, data model changes и risk estimation.
4. Делать полноценную поддержку 8-decimal precision только при prepayment, annual commitment или нескольких signed clients.
5. Не давать client стандартную цену после custom accounting feature, если она не становится public product feature.

## Короткое письмо

Здравствуйте,

Спасибо за объяснение требования по Bitcoin accounting. Мы понимаем необходимость 8-decimal precision, но это не маленькая integration: она влияет на accounting logic, precision, testing, reporting и long-term support.

Budget 3 000 EUR не покрывает полный implementation risk. Мы можем предложить первый paid analysis stage, где подтвердим scope, technical changes, timeline и commercial model. После этого можно идти дальше, если implementation покрыт большим budget, annual commitment или подтвержденным demand от нескольких clients.

Такой подход защищает обе стороны: вы получаете понятный plan до увеличения budget, а мы не строим accounting feature, которая может оказаться коммерчески неустойчивой.
