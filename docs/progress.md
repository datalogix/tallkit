# Progress

A progress bar with its percentage, for a task whose progress is known: an upload, an import, the steps of a form. The value comes from the server or follows an Alpine expression as it changes.

## Usage

```blade
<tk:progress :value="40" label="Import" />
```

A thin rounded gray track with a dark bar filling 40% of it, and "40%" under it. Screen readers announce a progress bar named "Import" at 40.

### Updated by Livewire

```blade
<tk:progress :value="$percent" label="Import" wire:poll.1s />
```

```php
public int $percent = 0;
```

Each time `$percent` changes on the server, the bar slides to the new width and the percentage follows.

### Following an Alpine expression

```blade
<div x-data="{ done: 3, total: 8 }">
    <tk:progress variable="done / total * 100" position="right" color="blue" />
</div>
```

`variable` is a JavaScript expression evaluated in the surrounding Alpine scope, kept between 0 and 100. The bar and the percentage, here on the right of the bar, follow it as the data changes, without a server request.

### As an overlay

```blade
<div class="relative">
    <img src="/photo.jpg" alt="">
    <tk:progress variable="file.progress" overlay="bottom" />
</div>
```

A square-ended bar along the bottom edge of the positioned parent, over its content, with no percentage text. `top` puts it at the top edge, and `fill` fills the parent from left to right behind its content.

## Props

### `tk:progress`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | int \| float | `0` | The percentage, from 0 to 100. |
| `variable` | string | `null` | An Alpine expression for the percentage, followed as it changes. Takes the place of `value`. |
| `label` | string \| false | `Progress` | The accessible name, translated. `false` hides the bar from screen readers, for a decorative bar. |
| `position` | string | `bottom` | Where the percentage goes: `bottom`, `top`, `left`, `right`, or `none` to hide it. |
| `color` | string | neutral | A color name for the bar (`blue`, `green`...). |
| `variant` | string | `null` | `accent` paints the bar in the accent color. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the bar height and the text size. |
| `square` | bool | `false` | Square ends instead of rounded ones. |
| `overlay` | string | `null` | `top`, `bottom` or `fill`: lays the bar over its positioned parent, square, without text, and decorative unless a `label` is given. |

## Customization

Classes and attributes on the tag go to the track. To reach the other parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:progress` | The element holding the track and the percentage. |
| `bar:` | `tk:progress` | The filled bar (`bar:class="bg-green-500"`). |
| `percent:` | `tk:progress` | The percentage text. |

```blade
<tk:progress :value="72" size="lg" class="bg-zinc-100" bar:class="bg-linear-to-r from-blue-500 to-green-500" percent:class="font-semibold" />
```

## Accessibility

A `role="progressbar"` with `aria-valuemin="0"`, `aria-valuemax="100"`, `aria-valuenow` kept up to date and an `aria-label`. With `label` set to `false` (and in overlays without a label) it is hidden from screen readers, so describe the progress in text nearby when it matters.

## Behavior notes

- The bar animates when it grows and jumps when it goes back, so a restarted task does not slide backwards. It does not animate at all when the user prefers reduced motion.
- Values below 0 or above 100 are clamped, and the percentage is rounded: `:value="33.333"` shows "33%".
- `variable` is evaluated on the bar, where TALLKit adds one name of its own, `paint`: an expression that uses a `paint` of yours reads TALLKit's instead.

## Related

- [Loading](loading.md): a spinner, for a wait whose progress is unknown.
- `tk:skeleton`: placeholders for content that is loading.
- [Alert](alert.md): shows a progress bar for its timer.
