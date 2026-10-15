# Time picker

A time field: a button that opens a list of times in steps (every 30 minutes by default), or a text input where the time can also be typed. The value is `HH:MM`, shown in the app's locale (`14:30` or `2:30 PM`).

## Usage

```blade
<tk:time-picker wire:model="startsAt" label="Starts at" />
```

```php
public ?string $startsAt = '09:00';
```

A button showing "09:00" with a chevron. Clicking it opens a list from 00:00 to 23:30, scrolled to the chosen time; picking one closes the list and sets `$startsAt`, e.g. `'14:30'`. Arrow keys move through the list and Enter picks.

### Typing the time

```blade
<tk:time-picker wire:model="startsAt" trigger="input" />
<tk:time-picker wire:model="startsAt" :dropdown="false" />
```

The first is a text input that also opens the list. It is masked as `99:99` (`99:99 AM` in 12-hour locales): typing the four digits, `1430`, gives `14:30`, and leaving the field or pressing Enter sets the time; an incomplete or invalid time puts the previous one back. The second is the input alone, with no list.

### Limits and steps

```blade
<tk:time-picker name="slot" interval="15" min="08:00" max="18:00" unavailable="12:00-13:30, 16:45" format="12-hour" />
```

Times every 15 minutes between 8 AM and 6 PM, in 12-hour format; the lunch break and 4:45 PM are disabled.

### Several times

```blade
<tk:time-picker wire:model="reminders" multiple />
```

```php
public string $reminders = '08:00,20:00';
```

The list stays open and each time is toggled on and off. The value is a comma-separated string of the picked times, sorted, e.g. `'08:00,12:00,20:00'`; an array is also accepted as the initial value.

## Props

### `tk:time-picker`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `trigger` | string | button | `input` uses a text input that can be typed in, instead of a button. |
| `dropdown` | bool | `true` | `false` removes the list, leaving only the text input. |
| `interval` | int | `30` | Minutes between the times in the list. |
| `min`, `max` | string | `null` | The earliest and latest times that can be picked (`08:00`). |
| `unavailable` | string \| array | `null` | Times or ranges that cannot be picked: `12:00-13:30, 16:45`. |
| `multiple` | bool | `false` | Lets several times be picked. |
| `format` | string | the locale's | `12-hour` or `24-hour`. |
| `openTo` | string | the value | The time the list scrolls to when it opens with no value. |
| `locale` | string | the app's | The locale used to show the times. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `placeholder` — "Select time" by default —, `value`, `help`...) and the [field control props](shared-props.md#field-control-props). `wire:model` and `x-model` bind `HH:MM`, or with `multiple` a comma-separated string of them.

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:time-picker` | `picked` | `{ value }` | The user picked or typed a time. |

```blade
<tk:time-picker name="at" @picked="$wire.check($event.detail.value)" />
```

## Customization

Classes and attributes go to the field; the parts take prefixes:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `trigger:` | `tk:time-picker` | The button or the text input. |
| `popover:`, `list:` | `tk:time-picker` | The dropdown panel and the list of times. |
| `slot:` | `tk:time-picker` | Each time in the list. |
| `placeholder:`, `formatted:` | `tk:time-picker` | The placeholder and the shown value, on the button. |

```blade
<tk:time-picker name="at" class="max-w-40" list:class="max-h-60" />
```

## Accessibility

Follows the WAI-ARIA combobox pattern with a listbox popup: the trigger has `aria-haspopup="listbox"` and `aria-expanded`, the times are options with `aria-selected`, and the times that cannot be picked are disabled and skipped. Opening the list moves the focus to the selected time.

| Key | Action |
|-----|--------|
| Enter, Space, Arrow Down | Opens the list. |
| Arrow Down / Arrow Up | Moves through the times. |
| Enter | Picks the focused time. |
| Escape | Closes the list. |

## Related

- [Date picker](date-picker.md): picking a date, or a date range.
- [Calendar](calendar.md): an inline month grid.
- [Input](input.md): `type="time"` for the browser's own time field.
