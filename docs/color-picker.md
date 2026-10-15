# Color picker

A form field for choosing a color: a text input with a color button inside it that opens a panel of swatches, a native color input and, optionally, an eye dropper. Use it in forms where the user picks or types a color; for a compact color button with no text field, such as in a toolbar, use `trigger="button"` or `tk:color-picker.button`.

```
tk:color-picker
└── tk:color-picker.button (rendered by the picker; also usable alone)
```

## Usage

```blade
<tk:color-picker name="color" label="Color" />
```

A labelled text input with a small square at its start. Clicking the square opens a panel with 15 swatches, a native color input and a Clear button. Clicking a swatch writes its value (`#ef4444`) into the input, paints the square with that color and closes the panel. The user can also type any CSS color (`rgb(0, 0, 255)`, `red`): it is converted to hex when they press Enter or leave the input.

### Binding to Livewire

```blade
<tk:color-picker wire:model="color" label="Brand color" />
```

```php
public ?string $color = '#ff0000';
```

The picker starts on red. Picking the indigo swatch sets `$color` to `'#6366f1'`, and a value set on the server, even in another notation (`'rgb(0,128,0)'`), is shown and stored as `'#008000'`.

### Button only

```blade
<tk:color-picker name="accent" trigger="button" label="Accent" />
```

Only a square button, filled with the chosen color over a checkerboard, with a palette icon while it is empty. The value goes in a hidden input named `accent`. With `preview="underline"` the button keeps its look and shows the color as a bar along its bottom edge instead.

### Swatches from data

```blade
<tk:color-picker
    wire:model="brand"
    :swatches="$brandColors"
    :clearable="false"
    format="rgb"
    label="Brand"
/>
```

```php
public ?string $brand = 'rgb(99, 102, 241)';

public array $brandColors = ['#6366f1', '#f43f5e', '#f59e0b'];
```

The panel shows only the three brand colors, and the one matching the current value is marked as pressed. With `format="rgb"`, picking the rose swatch sets `$brand` to `'rgb(244, 63, 94)'`. `:clearable="false"` removes the Clear button from the panel, so the field cannot be left empty from it.

### Following an Alpine value

```blade
<div x-data="{ theme: { color: '#0ea5e9' } }">
    <tk:color-picker name="color" live="theme.color" label="Color" />

    <button type="button" @click="theme.color = '#a855f7'">Purple</button>
</div>
```

`live` takes an Alpine expression and the picker shows its value as it changes: clicking Purple turns the picker to `#a855f7`. It only reads: picking a color in the picker does not write back to `theme.color`. Use `x-model` for a two-way binding.

## Props

### `tk:color-picker`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `trigger` | string | `null` | `button` renders only the color button, with the value in a hidden input. Any other value renders the text input with the button inside it. |
| `format` | string | `hex` | The notation of the value: `hex`, `hexa`, `rgb`, `rgba`, `hsl` or `hsla`. Every picked or typed color is converted to it. |
| `preview` | string | fill | With `trigger="button"`, `underline` shows the color as a bar along the bottom of the button instead of filling it. |
| `swatches` | array | 15 colors | The colors offered in the panel, in any CSS notation. |
| `clearable` | bool \| string | `null` | `true` adds a clear button at the end of the text input. The Clear button inside the panel is shown unless this is `false`. A string is the text of both buttons. |
| `copyable` | bool \| string | `false` | Adds a button at the end of the text input that copies the value. A string is its label. Text input only. |
| `dropper` | bool | `false` | Adds an eye dropper button to the panel, to pick a color from anywhere on the screen. Shown only in browsers with the EyeDropper API. |
| `live` | string | `null` | An Alpine expression the picker follows: whenever its value changes, the picker shows it. One way only. |
| `keepOpen` | bool | `false` | Keeps the panel open after a swatch is picked. |

Also accepts the [field props](shared-props.md#field-props) and, with the text input, the [field control props](shared-props.md#field-control-props). With `trigger="button"`, `icon` replaces the palette icon of the button. The default `placeholder` follows `format`: "Transparent", or `#00000000`, `rgba(0, 0, 0, 0)` and `hsla(0, 0%, 0%, 0)` for the formats with alpha.

`wire:model` and `x-model` bind the color as a string in `format`, or `null` when it is empty.

### `tk:color-picker.button`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string | `null` | The initial color. |
| `format` | string | `hex` | As in `tk:color-picker`. |
| `preview` | string | fill | `underline` shows the color as a bar along the bottom of the button. |
| `swatches` | array | 15 colors | The colors offered in the panel. |
| `clearable` | bool \| string | `null` | The Clear button in the panel is shown unless this is `false`. A string is its text. |
| `dropper` | bool | `false` | Adds the eye dropper button to the panel. |
| `size` | string | `md` | The size of the button and the swatches. |
| `keepOpen` | bool | `false` | Keeps the panel open after a swatch is picked. |
| `standalone` | bool | `true` | `false` renders the button and panel without their own state and hidden input. `tk:color-picker` uses it to place the button inside its field. |

Also accepts the [element props](shared-props.md#element-props); `ariaLabel` is read with the current color appended (`Text color, #ef4444`). `name`, `disabled`, `readonly`, `wire:model` and `x-model` go to the hidden input that holds the value, and `disabled` also disables the button.

```blade
<div x-data="{ textColor: '#ef4444' }">
    <tk:color-picker.button x-model="textColor" aria-label="Text color" />
</div>
```

## Events

`picked` is dispatched on the element that holds the value and does not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `picked` | `{ value }`, the new color in `format`, or `null` | The user picked a swatch, a native or eye dropper color, committed a typed color, or cleared the value. Not fired when the value changes through `wire:model`, `x-model` or `live`. |
| `cleared` | | The clear button of `clearable` emptied the text input. Comes from `tk:clearable` and bubbles. |

With Alpine:

```blade
<div x-data="{ color: null }">
    <tk:color-picker name="color" @picked="color = $event.detail.value" />

    <span x-text="color"></span>
</div>
```

Calling a Livewire action:

```blade
<tk:color-picker name="color" @picked="$wire.saveColor($event.detail.value)" />
```

## Customization

With the text input, `class` goes to the box around the input and the other attributes to the input itself; `input:class` adds classes to the input. With `trigger="button"`, the attributes go to the hidden input that holds the value; use `trigger:` to reach the button. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `picker:` | `tk:color-picker` | The element that holds the picker's state, around the input or the button. |
| `trigger:` | `tk:color-picker` | The color button. |
| `copyable:` | `tk:color-picker` | The copy button. |
| `clearable:` | both | The Clear button in the panel and, with the text input, the clear button at its end. |
| `dropdown:` | both | The `tk:dropdown` around the button and the panel (`position`, `align`). |
| `popover:` | both | The panel. |
| `swatch:` | both | The grid of swatches. |
| `option:` | both | Each swatch button. |
| `footer:` | both | The row under the swatches. |
| `custom:` | both | The native color input. |
| `dropper:` | both | The eye dropper button. |

The [field props](shared-props.md#field-props) prefixes (`label:`, `help:`...) also work.

```blade
<tk:color-picker
    name="color"
    dropper
    dropdown:align="end"
    popover:class="w-64"
    option:class="rounded-full"
    custom:title="More colors"
/>
```

## Accessibility

The color button has `aria-haspopup`, `aria-expanded` and `aria-controls` pointing to the panel. Its accessible name is the field label with `trigger="button"`; otherwise "Pick color", followed by the current color (`Pick color, #ef4444`). Each swatch is a button named by its color, with `aria-pressed="true"` on the one matching the value. The native color input is titled "Custom color".

| Key | Action |
|-----|--------|
| Enter, Space, Arrow Down, Arrow Up | On the color button, opens the panel. Focus stays on the button. |
| Tab | Moves through the swatches, the native color input, the eye dropper and the Clear button. |
| Enter, Space | On a swatch, picks it, closes the panel and returns focus to the button. |
| Escape | Closes the panel and returns focus to the button. |
| Enter | In the text input, commits the typed color and leaves the input. |

Focusing the text input selects its content.

## Behavior notes

- A typed color is committed on Enter or blur. Any CSS color is accepted and converted to `format`; text that is not a color is discarded and the input shows the previous value again; an empty input clears the value.
- While the user drags in the native color input, the button already shows the new color, but the value changes only when the native picker commits it. The native input edits the color only: the alpha of the current value is kept.
- Clicking the native color input keeps the panel open; clicking a swatch closes it unless `keepOpen` is set.
- The Clear button in the panel is hidden while the value is empty. With the text input, clearing also empties the input and puts the focus in it.
- `disabled` and `readonly` disable the color button, so the panel cannot be opened; `readonly` keeps the text input focusable but not editable.
- A form reset brings back the value the picker started with.
- Inside a Livewire component, bind the picker with `wire:model`. Unbound (`:wire-model="false"`), the text input starts empty even with a `value`, and with `trigger="button"` the next Livewire update empties the hidden input while the button still shows the color.

## Related

- [Clearable](clearable.md): the clear button `clearable` adds, which can also be placed by hand.
- [Copyable](copyable.md): the copy button `copyable` adds.
- `tk:input`: a plain text field, for a color typed as text without a picker.
