# Slider

A range input for picking a number by dragging: volume, a price limit, a rating. It has the field layout (label, help, error), a filled track up to the thumb, the current value next to the label, and optional tick marks that can be clicked.

```
tk:slider
└── tk:slider.tick ...
```

## Usage

```blade
<tk:slider wire:model.live="volume" label="Volume" min="0" max="100" step="10" show-value />
```

```php
public int $volume = 30;
```

A "Volume" label with "30" at its end, over a track filled up to the thumb at 30%. Dragging the thumb, or the arrow keys, moves it in steps of 10; the number and the fill follow, and `$volume` receives the value.

### Tick marks

```blade
<tk:slider name="size" min="0" max="4" step="1" :ticks="[
    ['label' => 'XS', 'value' => 0],
    ['label' => 'S', 'value' => 1],
    ['label' => 'M', 'value' => 2],
    ['label' => 'L', 'value' => 3],
    ['label' => 'XL', 'value' => 4],
]" />
```

The five labels spread under the track; clicking one moves the thumb to its value. `:ticks="[0, 50, 100]"` shows the numbers themselves, and a tick without a label is drawn as a small mark.

### Icons around the track

```blade
<tk:slider name="brightness" icon="brightness-5" icon-trailing="brightness-7" />
```

A dim sun before the track and a bright one after it.

## Props

### `tk:slider`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `showValue` | bool | `false` | Shows the current value at the end of the label line. |
| `ticks` | array | `null` | Marks under the track: numbers, or arrays of `tk:slider.tick` props (`label`, `value`). |

`min`, `max` and `step` are the native attributes (`0`, `100` and `1` by default). Also accepts the [field props](shared-props.md#field-props) (`label`, `help`, `value`, `size`, `color`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `iconTrailing`, `prepend`, `append`). `prefix` and `suffix` are not shown. `wire:model` and `x-model` bind the number.

### `tk:slider.tick`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | number | from the label | The value the thumb moves to when the tick is clicked. Without it, the label is read as a number. |
| `label` | string \| slot | `null` | The text under the track. Without it, a small mark. |
| `size` | string | inherited | The text size. |

Also accepts the [element props](shared-props.md#element-props).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:slider` | default | `tk:slider.tick` elements, after those from `ticks`. |
| `tk:slider.tick` | default | The tick content, in place of `label`. |

## Customization

Classes on the tag go to the box when there are icons or other content around the track; other attributes go to the `<input type="range">`. Besides the prefixes of the field props:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `slider:` | `tk:slider` | The element around the track and the ticks. |
| `ticks:`, `tick:` | `tk:slider` | The row of ticks, each tick. |
| `value:` | `tk:slider` | The value shown with `showValue`. |

```blade
<tk:slider name="volume" color="blue" value:class="font-semibold" ticks:class="px-1" />
```

## Accessibility

A native `<input type="range">` with its label, so screen readers announce the value and keyboard users move it with the arrow keys, Home, End, Page Up and Page Down. The value shown next to the label is hidden from screen readers, which already announce it. `readonly` blocks the keys and the pointer and sets `aria-readonly`.

## Behavior notes

- The fill and the shown value follow the input as it is dragged, when a Livewire update changes the bound property, and when the form is reset.
- Inside a Livewire component, `value` is ignored: the bound property is the value.

## Related

- [Input](input.md): `type="range"` renders this tag.
- [Money](money.md), [Input](input.md): typing an exact number.
- [Progress](progress.md): a bar showing a value, not changed by the user.
