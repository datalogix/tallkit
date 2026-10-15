# Date picker

A form field for a date, several dates or a range of dates: a button that shows the chosen date and opens a calendar in a popover, with optional presets, typed dates and Apply / Cancel buttons. Use it inside forms; when the calendar itself is the interface, such as a booking page, use the calendar.

## Usage

```blade
<tk:date-picker name="date" label="Date" />
```

A labelled field with a calendar icon, "Select date" as placeholder and a chevron at the end. Clicking it opens the current month in a popover, with a Clear button at the bottom. Clicking a day closes the popover and shows the date in the field, formatted for the locale (`Oct 14, 2026`). The value is kept in a hidden input named `date`, as `2026-10-14`, so it is sent with the form.

### Binding to Livewire

```blade
<tk:date-picker wire:model="date" label="Date" />
```

```php
public ?string $date = '2026-10-15';
```

The field shows the 15th, and the popover opens on October with it selected and focused. Clicking the 20th sets `$date` to `'2026-10-20'`; clicking the selected day again, or Clear, sets it to `''`. A value set on the server is shown by the field.

### Range with presets

```blade
<tk:date-picker wire:model="period" range presets label="Period" />
```

```php
public ?string $period = null;
```

The popover has a column of presets on the left (Today, Yesterday, This Week, Last 7 Days... Last Year) next to the calendar. Clicking Last 7 Days sets `$period` to `'2026-10-08/2026-10-14'` and closes the popover; that preset stays highlighted while the range matches it. Picking by hand takes two clicks: the first already writes the start alone (`'2026-10-12'`) and the popover stays open until the second. The field then shows `Oct 12 – 15, 2026`.

### Typing the date

```blade
<tk:date-picker wire:model="date" trigger="input" label="Date" />
```

```php
public ?string $date = null;
```

The button becomes a text input masked in the locale's date order (`dd/mm/yyyy` in `pt_BR`, `mm/dd/yyyy` in `en`). As soon as a whole valid date is typed, it is selected and `$date` is set; Enter closes the popover. Text that is not a valid date goes back to the current value when the user presses Enter or leaves the input. With `range` the mask holds two dates, `start – end`.

### Confirming a stay

```blade
<tk:date-picker
    wire:model="stay"
    range
    :min="now()"
    :unavailable="$bookedDates"
    months="2"
    confirm
    label="Stay"
/>
```

```php
public ?string $stay = null;

public array $bookedDates = ['2026-10-21', '2026-10-22'];
```

Two months side by side. Days before today and the booked dates are faded and cannot be clicked, and a range across a booked date is not accepted: the second click starts a new range instead. With `confirm` the popover has Cancel and Apply buttons and stays open after the range is complete; `$stay` is set only on Apply (`'2026-10-24/2026-10-26'`). Cancel, Escape or a click outside discard the choice.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string \| array \| date | `null` | The initial selection, as `Y-m-d`. With `multiple`, an array or a comma-separated string; with `range`, `start/end`, `['start' => ..., 'end' => ...]` or a `DateRange`. Dates and Carbon instances are accepted. Old input from a failed submit takes precedence. Inside Livewire use `wire:model` instead. |
| `multiple` | bool | `false` | Lets the user select several days. The popover stays open while they pick, and the field lists the dates separated by commas. |
| `range` | bool | `false` | Selects a range: the first click sets the start, the second the end. |
| `trigger` | string | button | `input` replaces the button with a text input where the date can be typed. With `multiple` the input is read-only. |
| `format` | string | `medium` | How the date is shown in the field: `full`, `long`, `medium` or `short`, as the browser's date styles. Any other value falls back to `medium` with a console warning. |
| `presets` | bool \| string | `false` | With `range`, adds a column of preset ranges. `true` shows Today, Yesterday, This Week, Last 7 Days, Last 14 Days, Last 30 Days, This Month, Last Month, This Year and Last Year. A string lists the presets to show, separated by spaces (`"today last7Days thisMonth"`); unknown names are ignored. |
| `inputs` | bool | `false` | Adds native date inputs under the calendar: one, or start and end with `range`. Not shown with `multiple`. |
| `confirm` | bool | `false` | Adds Cancel and Apply buttons: the selection is written only on Apply. |
| `clearable` | bool \| string | `null` | The Clear button at the bottom of the popover is shown unless this is `false`. A string is its text. |
| `months` | int | `1` | How many consecutive months the popover shows side by side. |
| `min` | string \| date | `null` | The earliest day that can be picked. |
| `max` | string \| date | `null` | The latest day that can be picked. |
| `unavailable` | string \| array | `null` | Days that cannot be picked, as an array or a comma-separated string. A range cannot include them. |
| `minRange` | int | `null` | With `range`, the fewest days a range may span, counting both ends. |
| `maxRange` | int | `null` | With `range`, the most days a range may span, counting both ends. |
| `today` | bool | `false` | Adds a button that moves to the current month, or selects today when it is already shown. |
| `selectableHeader` | bool | `false` | Replaces the month title with a month and a year select. Only with one month. |
| `fixedWeeks` | bool | `false` | Always shows six weeks, so the popover does not change height between months. |
| `startDay` | int | from the locale | The first day of the week, `0` (Sunday) to `6` (Saturday). |
| `openTo` | string \| date | the current month | The month shown when nothing is selected. |
| `forceOpenTo` | bool | `false` | Opens on the `openTo` month every time, even when a date is selected. |
| `weekNumbers` | bool | `false` | Adds a column with the ISO week number of each week. |
| `locale` | string | the app locale | The language of the field text, the month and weekday names and the typing mask. Also sets the first day of the week. |

Also accepts the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props). The default `placeholder` is "Select date", the default `icon` is a calendar, and `color` also colors the selected days and the Apply button. `disabled` disables the field; with `readonly` it shows the value but does not open.

`wire:model` and `x-model` bind the selection as a string: `2026-10-14`, `2026-10-01,2026-10-03` with `multiple`, or `2026-10-12/2026-10-15` with `range`; an empty selection is `''`. Inside a Livewire component the picker is bound by itself to the public property named by `name`. A property typed as `TALLKit\Livewire\DateRange` receives the range as a date range object; when it is bound through `name`, a chosen preset is kept in it too (`$period->preset()`).

## Events

`picked` is dispatched on the hidden input that holds the value, which receives the tag's attributes, and does not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `picked` | `{ value }`, the new selection | The user clicked a day, chose a preset, typed a complete date or changed a native date input. The value is the date, or `null` when the selected day was clicked again; the sorted array of dates with `multiple`; `{ start, end }` with `range`, fired only when the range is complete. With `confirm` it fires on the click, before Apply. Not fired by Clear, nor when the value changes through `wire:model` or `x-model`. |

With Alpine:

```blade
<div x-data="{ day: null }">
    <tk:date-picker name="day" @picked="day = $event.detail.value" />

    <p x-text="day"></p>
</div>
```

Calling a Livewire action:

```blade
<tk:date-picker name="period" range presets @picked="$wire.loadReport($event.detail.value)" />
```

## Customization

`class` goes to the box around the button or the input; the other attributes go to the hidden input that holds the value. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `picker:` | The element that holds the picker's state, around the field and the popover. |
| `trigger:` | The button, or the text input with `trigger="input"`. |
| `placeholder:` | The placeholder text inside the button. |
| `formatted:` | The formatted date inside the button. |
| `popover:` | The popover panel. |
| `layout:` | The element holding the presets and the calendar. |
| `presets:` | The column of presets. |
| `preset:` | Each preset button. |
| `calendar:` | The calendar. Its own prefixes work behind this one: `calendar:day:class`. See [Calendar](calendar.md#customization). |
| `inputs:` | The bar with the native date inputs. |
| `single-date:` | The native date input. |
| `start-date:` | The native start date input, with `range`. |
| `divider:` | The dash between the start and end inputs. |
| `end-date:` | The native end date input, with `range`. |
| `footer:` | The bar with the Clear, Cancel and Apply buttons. |
| `clearable:` | The Clear button. |
| `cancel:` | The Cancel button. `cancel:label` changes its text. |
| `apply:` | The Apply button. `apply:label` changes its text. |

```blade
<tk:date-picker
    name="date"
    class="max-w-60"
    trigger:class="font-semibold"
    calendar:day:class="rounded-full"
    confirm
    apply:label="Done"
/>
```

## Accessibility

The button is labelled by the field label and the shown date, and has `aria-haspopup`, `aria-expanded` and `aria-controls` pointing to the popover. With `trigger="input"` the input has `role="combobox"`. Inside the popover the calendar is a `role="grid"` per month, with each day named by its full date; see [Calendar](calendar.md#accessibility). Opening the popover moves focus to the selected day, else today.

| Key | Action |
|-----|--------|
| Enter, Space, Arrow Down, Arrow Up | On the button, opens the popover. |
| Arrow keys, Home / End, Page Up / Page Down | Inside the calendar, move focus between days and months as in the calendar. |
| Enter, Space | Selects the focused day. |
| Enter | In the text input, selects the typed date and closes the popover. |
| Escape | Closes the popover and returns focus to the field. |
| Tab | Moves through the popover's buttons; leaving the popover closes it. |

## Behavior notes

- A single date closes the popover when picked, a range when its second day is picked; with `multiple` it stays open until Escape, a click outside or Tab leaves it. With `confirm` it only closes on Apply, Cancel, Escape or a click outside.
- With `range`, the first click already writes the start alone to the bound value (`2026-10-12`); the full `start/end` follows the second click. A second click that would break `minRange`, `maxRange` or cross an unavailable day starts a new range from that day.
- Presets that fall outside `min` / `max` or break `minRange`, `maxRange` or `unavailable` are disabled.
- The popover opens on the month of the selection, then on `openTo`, then on the current month; `forceOpenTo` always uses `openTo`.
- With `trigger="input"`, clicking the input opens the popover and moves focus to the calendar; focusing the input with Tab does not open it, so the date can be typed straight away.
- The selection survives Livewire updates of the page, and a value changed on the server through `wire:model` is shown.
- A form reset brings back the value the picker started with.

## Related

- [Calendar](calendar.md): the same month grid always visible on the page, without a field or popover.
- `tk:time-picker`: a field for a time of day.
