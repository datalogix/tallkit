# Calendar

A month grid that is always visible on the page, for picking one date, several dates or a range of dates. Use it when the calendar itself is the interface, such as a booking page; for a date field that opens a calendar in a popover, use the date picker.

## Usage

```blade
<tk:calendar name="date" />
```

The current month with buttons for the previous and next month. Clicking a day selects it, and clicking it again clears the selection. The value is kept in a hidden input named `date`, as `2026-10-14`, so it is sent with the form.

### Binding to Livewire

```blade
<tk:calendar wire:model="date" />
```

```php
public ?string $date = '2026-10-15';
```

The calendar opens on the month of the bound date, with it selected. Clicking the 20th sets `$date` to `'2026-10-20'`; a change made to `$date` on the server is shown by the calendar.

### Range

```blade
<tk:calendar wire:model="stay" range min-range="2" max-range="14" />
```

```php
public ?string $stay = null;
```

The first click marks the start, and moving the mouse paints the range up to the hovered day. The second click closes it, in either order: clicking the 15th and then the 12th sets `$stay` to `'2026-10-12/2026-10-15'`. While the range is open, the days that would make it shorter than `min-range` or longer than `max-range` are disabled. A property typed as `TALLKit\Livewire\DateRange` receives the same selection as a date range object.

### Several dates

```blade
<tk:calendar wire:model="days" multiple />
```

```php
public ?string $days = '2026-10-01,2026-10-03';
```

Each click adds or removes a day. The dates are kept in order and sent as one comma-separated string: clicking the 2nd sets `$days` to `'2026-10-01,2026-10-02,2026-10-03'`.

### Blocking dates from data

```blade
<tk:calendar
    wire:model="stay"
    range
    :min="now()"
    :unavailable="$bookedDates"
    months="2"
    today
/>
```

```php
public ?string $stay = null;

public array $bookedDates = ['2026-10-21', '2026-10-22'];
```

Two months side by side, stacked on small screens. Days before today and the booked dates are faded and struck through, and cannot be clicked. A range that would include a booked date is not accepted: the second click starts a new range from that day instead. The Today button moves back to the current month and, once it is visible, selects today.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string \| array \| date | `null` | The initial selection, as `Y-m-d`. With `multiple`, an array or a comma-separated string; with `range`, `start/end`, `['start' => ..., 'end' => ...]` or a `DateRange`. Dates and Carbon instances are accepted. Old input from a failed submit takes precedence. Inside Livewire use `wire:model` instead. |
| `multiple` | bool | `false` | Lets the user select several days. |
| `range` | bool | `false` | Selects a range: the first click sets the start, the second the end. |
| `months` | int | `1` | How many consecutive months are shown side by side. |
| `min` | string \| date | `null` | The earliest day that can be picked. |
| `max` | string \| date | `null` | The latest day that can be picked. |
| `unavailable` | string \| array | `null` | Days that cannot be picked, as an array or a comma-separated string. A range cannot include them. |
| `minRange` | int | `null` | With `range`, the fewest days a range may span, counting both ends. |
| `maxRange` | int | `null` | With `range`, the most days a range may span, counting both ends. |
| `static` | bool | `false` | Shows the selection without letting the user change it or move to other months. |
| `navigation` | bool | `true` | `false` removes the previous and next month buttons and keeps the user on the months first shown. |
| `today` | bool | `false` | Adds a button that moves to the current month, or selects today when it is already shown. |
| `selectableHeader` | bool | `false` | Replaces the month title with a month and a year select. Only with one month and navigation on. |
| `fixedWeeks` | bool | `false` | Always shows six weeks, so the height does not change between months. |
| `startDay` | int | from the locale | The first day of the week, `0` (Sunday) to `6` (Saturday). |
| `openTo` | string \| date | the current month | The month shown when nothing is selected. |
| `weekNumbers` | bool | `false` | Adds a column with the ISO week number of each week. |
| `locale` | string | the app locale | The language of the month and weekday names and of the day labels read by screen readers. Also sets the first day of the week. |
| `id` | string | from the name | The id of the root element. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |
| `color` | string | the accent color | The color of the selected days and of the range. |
| `standalone` | bool | `true` | `false` renders the grid alone, without its state, hidden input or frame. `tk:date-picker` uses it to place the calendar inside its own popover. |

`wire:model` and `x-model` bind the selection as a string: `2026-10-14`, `2026-10-01,2026-10-03` with `multiple`, or `2026-10-12/2026-10-15` with `range`. A `name` gives the hidden input its name, and inside a Livewire component the calendar is bound by itself to the public property with that name; `wire-model="false"` turns that off. `invalid` and `bag` work as in the [field props](shared-props.md#field-props): with a validation error the border turns red.

## Events

Events are dispatched on the component's own element and do not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `picked` | `{ value }`, the new selection | A click or a key changed the selection. The value is the date, or `null` when the selected day was clicked again; the sorted array of dates with `multiple`; `{ start, end }` with `range`, fired only when the range is complete. |

With Alpine:

```blade
<div x-data="{ day: null }">
    <tk:calendar @picked="day = $event.detail.value" />

    <p x-text="day"></p>
</div>
```

Calling a Livewire action:

```blade
<tk:calendar range @picked="$wire.loadAvailability($event.detail.value)" />
```

## Customization

Classes and attributes on the tag go to the root element. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `hidden:` | The hidden input that holds the value. |
| `header:` | The bar with the title and the buttons. |
| `month-label:` | The month title. |
| `month-select:` | The month select of `selectableHeader`. |
| `year-select:` | The year select of `selectableHeader`. |
| `today:` | The Today button. |
| `nav-prev:` | The previous month button. |
| `nav-next:` | The next month button. |
| `months:` | The element holding all the months. |
| `month:` | Each month grid. |
| `weekdays:` | The row of weekday names. |
| `weekday:` | Each weekday name. |
| `week:` | Each week row. |
| `week-number:` | Each week number cell. |
| `day:` | Each day button. |

```blade
<tk:calendar
    class="w-full"
    day:class="rounded-full"
    nav-prev:tooltip="Back"
    nav-next:tooltip="Forward"
/>
```

## Accessibility

Each month is a `role="grid"` named after the month and year (`October 2026`), with the weekday names as column headers and, with `weekNumbers`, the week number as row header. Each day is a button with `role="gridcell"`, its full date as accessible name (`Wednesday, October 14, 2026`), `aria-selected` and, on today, `aria-current="date"`. Only one day is in the tab order: the last one focused, else the first selected, else today, else the first day that can be picked. The navigation buttons are named by their tooltips.

| Key | Action |
|-----|--------|
| Arrow Left / Arrow Right | Moves focus to the previous / next day. Reversed in right-to-left pages. |
| Arrow Up / Arrow Down | Moves focus to the same weekday in the previous / next week. |
| Home / End | Moves focus to the first / last day of the week. |
| Page Up / Page Down | Moves focus to the same day in the previous / next month. |
| Shift + Page Up / Shift + Page Down | Moves focus to the same day in the previous / next year. |
| Enter, Space | Selects the focused day. |

Disabled days are skipped: focus goes on to the next day that can be picked in that direction, and stays where it is when there is none. Moving focus past the months shown brings the next ones into view, unless `navigation` is `false`.

## Behavior notes

- The selection is read and written as `Y-m-d` text. With `multiple` it is sent as one comma-separated string even when the bound Livewire property starts as an array, so the property must accept a string (an `array` property fails with a type error on the next request).
- With `range`, the first click already writes the start alone to the bound value (`2026-10-15`); the full `start/end` follows the second click.
- A second click that would break `minRange`, `maxRange` or cross an unavailable day starts a new range from the clicked day instead.
- The days of the neighbouring months that fill the first and last week can be clicked and are selected, but the calendar stays on the current month.
- The calendar opens on the month of the selection, then on `openTo`, then on the current month. A value set later through `wire:model` or `x-model` updates the selection but does not move to its month.
- The year select lists from the `min` year, or 100 years back, to the `max` year, or 100 years ahead.
- A form reset brings back the value the calendar started with.
- `static` keeps the selection visible: the days are disabled, but neither faded nor struck through.

## Related

- [Date picker](date-picker.md): a date or range field that opens this calendar in a popover, with presets and typed dates. Use it inside forms, where a full grid would take too much room.
- `tk:full-calendar`: an event calendar built on the FullCalendar library, for showing scheduled events rather than picking dates.
