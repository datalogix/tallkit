# Stepper

The steps of a process and where the user is in it: a numbered circle per step, filled for the completed ones, outlined for the current one, faint for the next ones, joined by lines. It only shows progress; moving between steps is up to your page.

```
tk:stepper
└── tk:stepper.step ...
```

## Usage

```blade
<tk:stepper :current="$step" :items="[
    ['label' => 'Cart'],
    ['label' => 'Shipping'],
    ['label' => 'Payment'],
]" />
```

```php
public int $step = 2;
```

Three circles in a row, numbered 1 to 3, with their labels under them. Cart is filled (completed), Shipping is outlined (current) and Payment is faint. The line after a completed step is filled too. Changing `$step` moves the progress.

### Icons

```blade
<tk:stepper current="2" icon-completed="check" :items="[
    ['label' => 'Account', 'icon' => 'account'],
    ['label' => 'Plan', 'icon' => 'tag'],
    ['label' => 'Done', 'icon' => 'flag'],
]" color="green" />
```

Each step shows its icon instead of its number; completed steps show a check. `iconActive` does the same for the current step. `color` paints the completed and current steps green.

### Vertical, with content

```blade
<tk:stepper current="2" vertical>
    <tk:stepper.step label="Order placed">
        <tk:text variant="subtle" label="Oct 12, 10:24" />
    </tk:stepper.step>
    <tk:stepper.step label="Shipped" />
    <tk:stepper.step label="Delivered" />
</tk:stepper>
```

The steps in a column, each with a line down to the next; content in a step's slot sits beside the line, under its label. Steps written by hand are marked with a dot instead of a number.

## Props

### `tk:stepper`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `null` | The steps, as arrays of `tk:stepper.step` props (`label`, `icon`...). |
| `current` | int | `0` | The current step, counting from 1. The ones before it are completed. |
| `vertical` | bool | `false` | Lays the steps out in a column. |
| `iconCompleted` | string | `null` | An icon for the completed steps, in place of their number or icon. |
| `iconActive` | string | `null` | An icon for the current step. |
| `color` | string | neutral | A color name (or `accent`) for the completed and current steps and the lines between them. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |

### `tk:stepper.step`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The step name, translated. |
| `icon` | string | `null` | An icon in the circle, in place of the number. |
| `status` | string | from `current` | `completed`, `active` or `pending`, to set it by hand. |
| `iconCompleted`, `iconActive`, `color` | | | As on the stepper, for this step. |

`size`, `vertical` and `current` are inherited from the stepper. `index` and `total` (the number shown and the "Step 2 of 3" name) are set by the stepper for the steps from `items`. Also accepts the [element props](shared-props.md#element-props) (`badge`, `suffix`...) and the link props of [Element](element.md#props), to make a step a link.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:stepper` | default | Steps written by hand, after those from `items`. |
| `tk:stepper.step` | default | Content under the step, beside the line. |

## Customization

Classes and attributes on `tk:stepper` go to the row. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `step:` | `tk:stepper` | Each step from `items`. |
| `line:` | `tk:stepper`, `tk:stepper.step` | The lines between steps. |
| `icon:` | `tk:stepper.step` | The icon in the circle. |
| `bullet:` | `tk:stepper.step` | The dot of a step written by hand. |

```blade
<tk:stepper :current="$step" :items="$steps" class="max-w-xl" step:class="text-sm" line:class="bg-zinc-300" />
```

## Accessibility

A `role="list"` of `role="listitem"` steps. Each step is named with its position and state, translated: "Step 2 of 3: Shipping (current)". The current step has `aria-current="step"`. The lines are hidden from screen readers.

## Behavior notes

- `current="0"` (the default) leaves every step pending; a `current` past the last step marks all of them completed.
- Steps written by hand get their state from their order inside the stepper.

## Related

- [Progress](progress.md): progress as a single bar.
- `tk:tab`: switching between sections that can be visited in any order.
