# Toggle

A button that stays pressed: bold in a text toolbar, a favorite star, a "wrap lines" option. It is a checkbox underneath, styled as a button, so it binds a bool and is sent with forms.

## Usage

```blade
<tk:toggle wire:model.live="wrap">Wrap lines</tk:toggle>
```

```php
public bool $wrap = true;
```

A button reading "Wrap lines", with a darker background while on. Clicking it, or Space while it has focus, flips `$wrap`. While that request runs, a spinner takes the place of the icon and the toggle cannot be flipped again.

### Icon only

```blade
<tk:toggle name="bold" icon="format-bold" tooltip="Bold" />
```

A square button with the bold icon. The tooltip is also its accessible name.

### Different icon and text per state

```blade
<tk:toggle name="favorite" icon-on="star" icon-off="star-outline" label-on="Favorited" label-off="Favorite" variant="filled" />
```

An outlined star and "Favorite" while off; a filled star and "Favorited" while on.

## Props

### `tk:toggle`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `checked` | bool | `false` | Starts on. |
| `icon` | string | `null` | An icon for both states. |
| `iconOn`, `iconOff` | string | `icon` | The icon while on, and while off. |
| `labelOn`, `labelOff` | string | `null` | The text while on, and while off. |
| `variant` | string | outline | `filled`, `ghost`, `subtle`, or the default outlined button. |
| `color` | string | `null` | A color name for the pressed state (`blue`...). |
| `tooltip` | string | `null` | A tooltip; with no text in the button, it is the accessible name. |
| `loading` | bool | auto | Shows a spinner and blocks the toggle while its request runs. On with `wire:model.live` or `wire:change`. |
| `loadingDelay`, `loadingMinDuration` | int \| string | `150`, `700` | As on [`tk:switch`](switch.md#props). |
| `align` | string | `left` | With `help` or `description`, where the button sits next to them, as on [`tk:checkbox`](checkbox.md#props). |

Also accepts the [field props](shared-props.md#field-props) (`name`, `value`, `size`, `help`...); `label` is the button text, not a label above it. `wire:model` and `x-model` bind a bool, or an array of `value`s when several toggles share the property.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:toggle` | default | The button content, in place of `label`. |

## Customization

Classes go to the button; other attributes to the hidden checkbox. The parts take prefixes:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon-on:`, `icon-off:` | `tk:toggle` | The icons. |
| `label-checked:`, `label-unchecked:` | `tk:toggle` | The `labelOn` and `labelOff` texts. |
| `loading:` | `tk:toggle` | The spinner. |

```blade
<tk:toggle name="pin" icon="pin" tooltip="Pin" class="rounded-full" icon-on:class="text-amber-500" />
```

## Accessibility

A native checkbox inside the button, so screen readers announce it as checked or not checked, named by its text or tooltip ("Toggle" when it has neither). Space flips it; it is reached with Tab.

## Behavior notes

- Old input from a failed submit restores the state.

## Related

- [Switch](switch.md): an on/off setting with a label beside it.
- [Checkbox](checkbox.md): a choice confirmed later with a form.
- [Button](button.md): a button that runs an action.
