# CJM и гипотезы для x3 sales

## Baseline

```text
C0 = 20% visitors start registration
C1 = 3% visitors reach live website/payment/sale
```

## CJM

```text
Site.pro landing → Sign up → Builder onboarding → Template/demo selection → First edit → Preview → Publish/live website → Paid activation
```

## Потенциальные проблемы

1. Mismatch между promise на landing и registration: user кликает, но не понимает, какой результат получит.
2. Builder onboarding friction: слишком много choices до первого видимого website result.
3. Неясность publish/payment step: user не понимает, что включено и зачем нужна оплата.

## Две параллельные гипотезы

### Гипотеза A: onboarding “Start from demo result”

Если users видят готовый website draft сразу после registration, activation должна вырасти, потому что product value становится видимым до тяжелого editing.

Изменение:

- После registration сгенерировать/open demo website, похожий на `ded.site.pro`.
- Добавить один CTA: “Edit this website”.

Metric:

- C1: visitors to live website/payment/sale.

### Гипотеза B: clarification pricing/value до publish

Если publish step объясняет, что получает user, payment conversion должна вырасти, потому что uncertainty уменьшается.

Изменение:

- Добавить publish screen с checklist: domain, hosting, editing, support, public URL.
- Добавить один primary CTA и одну secondary FAQ link.

Metric:

- Publish-step-to-payment conversion.

## Оценка traffic для split testing

Для грубого A/B test нужно минимум 1 000-2 000 users на variant, чтобы получить directional learning. Для надежного измерения маленьких изменений от 3% нужно больше traffic. Если цель — большой x3 effect с 3% до 9%, меньший test может быстрее показать signal, но результат все равно нужно подтвердить на втором period.
