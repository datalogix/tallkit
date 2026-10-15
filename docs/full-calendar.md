# Full Calendar

An event calendar drawn by [FullCalendar](https://fullcalendar.io) 7, with month, week and day views, configured with the library's own options. It loads the library on demand in the app's language, follows the light and dark color scheme and redraws when Livewire sends new options. For picking a date in a form, use the calendar or date picker instead.

## Usage

```blade
<tk:full-calendar :options="[
    'initialView' => 'dayGridMonth',
    'events' => [
        ['title' => 'Kick-off', 'start' => '2026-10-12'],
        ['title' => 'Review', 'start' => '2026-10-20T14:00:00', 'end' => '2026-10-20T15:30:00'],
    ],
]" />
```

A spinner shows while the library loads, then a month grid with the two events on their days, the toolbar with previous and next buttons and the month name in the app's language. `options` is what FullCalendar takes in `new FullCalendar.Calendar(element, options)`, as a PHP array.

### Events from Livewire

```blade
<tk:full-calendar :options="['initialView' => 'timeGridWeek', 'events' => $events]" />
```

```php
public array $events = [
    ['title' => 'Kick-off', 'start' => '2026-10-12'],
];

public function addReview(): void
{
    $this->events[] = ['title' => 'Review', 'start' => '2026-10-20T14:00:00'];
}
```

After `addReview()` the calendar is drawn again with the new event, staying on the week and view the user was looking at.

### Events from a URL

```blade
<tk:full-calendar :options="['events' => '/calendar/feed']" />
```

FullCalendar requests the URL with `start` and `end` query parameters for the range on screen, and again when the user moves to another month or week.

### Callbacks written in JavaScript

```blade
<tk:full-calendar
    :options="['events' => $events]"
    data-options="{ eventClick: (info) => $wire.open(info.event.id), dateClick: (info) => $wire.create(info.dateStr) }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry: here clicking an event calls `open()` with its id, and clicking a day calls `create()` with the date.

## Props

### `tk:full-calendar`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `null` | The FullCalendar options: `initialView`, `events`, `headerToolbar`, `initialDate`... |
| `locale` | string | the app's locale | The language of the calendar (`pt_BR`, `en`, `es`...). Its translation file is loaded with the library. |
| `theme` | string | `monarch` | The FullCalendar 7 theme to load. |
| `palette` | string | `blue` | The color palette of the theme. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

## Events

`rendered` is dispatched on the calendar element, the `<div>` the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:full-calendar` | `rendered` | `{ fullCalendar }`, the FullCalendar instance | The calendar was drawn: the first time and on each update of the options. |
| `tk:full-calendar` | `completed` | | The library is loaded; the calendar is drawn right after. |
| `tk:full-calendar` | `failed` | | The library could not be loaded, or FullCalendar threw while drawing. |

With Alpine:

```blade
<tk:full-calendar :options="['events' => $events]" @rendered="calendar = $event.detail.fullCalendar" />
```

Calling a Livewire action:

```blade
<tk:full-calendar :options="['events' => $events]" @rendered="$wire.calendarReady()" />
```

## Customization

Classes and attributes on the tag go to the calendar `<div>`, which is `w-full h-full` by default. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:full-calendar` | The wrapper around the calendar, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:full-calendar` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:full-calendar` | The error message. |

```blade
<tk:full-calendar :options="['events' => $events, 'height' => 'auto']" class="max-w-4xl" loadable:class="rounded-lg border p-4" />
```

The height follows FullCalendar's `height` and `aspectRatio` options.

## Accessibility

FullCalendar renders the grid with its own roles and labels, and its toolbar buttons are real buttons with accessible names in the calendar's language. The spinner and the error message are `role="status"` regions.

## Behavior notes

- The library, the theme and its palette are loaded from `https://cdn.jsdelivr.net/npm/fullcalendar@7` by the first calendar on the page. When `window.FullCalendar` already exists, as when the app bundles it, nothing is requested.
- The locale file is loaded for any language other than English. A regional code with no file of its own (`pt-xx`) falls back to the language (`pt`); a language with no file leaves the calendar in English.
- The color scheme follows the `dark` class of `<html>`: the wrapper's `data-color-scheme` switches between `light` and `dark`, which the themes use, without redrawing.
- A Livewire update that changes `options` destroys the calendar and draws a new one, keeping the date and the view on screen. `getCalendar()`, callable from Alpine expressions on the tag, returns the current instance.
- `data-options` is merged over `options` at the top level: a key present in both is taken whole from `data-options`.

## Related

- [Calendar](calendar.md): a month grid for picking dates in a form.
- [Date picker](date-picker.md): a date field with a calendar in a popover.
