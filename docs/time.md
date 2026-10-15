# Time

A date shown as relative time ("5 minutes ago", "in 2 days") in the app's language, inside a `<time>` element that carries the exact moment for machines and, on hover, for people.

## Usage

```blade
<tk:time :value="$comment->created_at" />
```

"5 minutes ago" (translated), in a `<time datetime="2026-10-14T10:25:00-03:00">`. Hovering it shows the full date and time as a tooltip, such as "October 14, 2026 10:25 AM", in the app's locale.

### A fallback for empty dates

```blade
<tk:time :value="$user->last_login_at" fallback="Never" />
```

With no date, "Never" (translated) in a `<span>`; without `fallback`, nothing is rendered.

### Your own text

```blade
<tk:time :value="$order->shipped_at" format="LL">
    {{ $order->shipped_at->isoFormat('LL') }}
</tk:time>
```

The slot replaces the relative text, here the date alone, while the element keeps the machine-readable `datetime` and the tooltip in the `format` given.

## Props

### `tk:time`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string \| DateTimeInterface \| int | `null` | The date: anything `Carbon::parse()` reads. |
| `format` | string | `LLL` | The [ISO format](https://carbon.nesbot.com/docs/#iso-format-available-replacements) of the tooltip. |
| `fallback` | string | `null` | Text shown when `value` is empty, translated. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:time` | default | The visible text, in place of the relative time. |

## Customization

Classes and attributes go to the `<time>` (or to the fallback `<span>`).

```blade
<tk:time :value="$post->published_at" class="text-sm text-zinc-500" />
```

## Accessibility

A native `<time>` with an ISO 8601 `datetime`. The full date is in the `title`, which not every screen reader announces: when the exact date matters, show it in the text with the slot.

## Behavior notes

- The relative text is computed when the page renders and does not update while it stays open; a Livewire update renders it again.
- The language is the app's locale at render time.

## Related

- [Date picker](date-picker.md): picking a date in a form.
- [Table](table.md): shows date columns in the locale's format.
