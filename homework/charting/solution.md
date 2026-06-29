# Charting Homework Solution

## Source

- Figma design: https://www.figma.com/design/lzlGH5lQc1gono8atSUGYk/Untitled?node-id=1-18&m=dev
- Implemented file: `homework/charting/index.html`
- AI decomposition: `homework/charting/decomposition.md`

## How to Run

Open `homework/charting/index.html` in a browser.

The page is a self-contained static app. It does not require the Yii/PHP app, Docker, npm, or a build step.

## Implemented Pages

The app has 4 in-page views:

1. Title page.
   - Includes the required buttons: `Start demo` and `View portfolio`.
2. Portfolio/work page.
3. About/process page with KPI dashboard.
4. Contact/CTA page.

The visual style follows the provided Figma frame: Quicksand typography, white dashboard background, blue active navigation, black pill counters, green status badges, and bordered dashboard/table blocks.

## Mixpanel Events

Mixpanel token used in the static app:

```text
3d30334bf82fc18b6202f9da5334ca4c
```

### Page Views

Event name:

```text
page_view
```

Properties:

```text
page_slug
page_title
referrer
viewport_type
session_id
```

The event fires on initial load and every in-page navigation change.

### Buttons

Event name:

```text
button_click
```

Properties:

```text
page_slug
button_id
button_label
button_position
session_id
```

The event fires for the title page buttons, navigation buttons, portfolio CTAs, and contact CTAs.

## KPI Dashboard

| KPI | Formula | Graph |
|---|---|---|
| Page views by page | Count `page_view` grouped by `page_slug` | Bar chart |
| CTA click-through rate | CTA `button_click` events / title `page_view` events | Line chart |
| Portfolio engagement | Portfolio `page_view` events / title `page_view` events | Horizontal bar chart |
| Contact intent rate | Contact CTA `button_click` events / total sessions | Line chart |
| Mobile share | Mobile sessions / all sessions | Donut chart |
| Drop-off by page | Page N visitors - next page visitors | Funnel chart |

## Access Sharing Checklist

- [ ] Open the Mixpanel project that uses token `3d30334bf82fc18b6202f9da5334ca4c`.
- [ ] Create or open the dashboard with the KPI widgets listed above.
- [ ] Invite the task provider email as a collaborator in Mixpanel.
- [ ] Share the dashboard link with the task provider.
- [ ] Do not invent the email address if it is only available in the assignment platform.

## Verification Notes

- All 4 pages are reachable from the top or secondary navigation.
- The title page has at least 2 working buttons.
- The app logs every Mixpanel event to the browser console for quick local verification.
- If the Mixpanel CDN is available, events are sent to the real project.
- The layout is responsive for desktop, tablet, and mobile widths.
