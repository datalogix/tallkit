# Switch

An on/off switch with its label, for a setting that takes effect right away: notifications, dark mode, a feature flag. It is a checkbox underneath, with `role="switch"`. While a Livewire request it started is running, it shows a spinner and ignores clicks.

```
tk:switch
tk:switch.all
```

## Usage

```blade
<tk:switch wire:model.live="notify" label="Email notifications" description="A summary every Monday." />
```

```php
public bool $notify = false;
```

A pill-shaped track with a round knob, followed by the label and the description. Clicking it, its label, or pressing Space slides the knob and sets `$notify` to `true`. While that request runs, a small spinner shows in the knob and the switch cannot be flipped again; it appears only if the request takes longer than 150 ms.

### State labels

```blade
<tk:switch wire:model="dark" label-on="On" label-off="Off" :label="false" aria-label="Dark mode" />
```

"On" or "Off" next to the switch, following its state, with "Dark mode" as its accessible name.

### An action on change

```blade
<tk:switch :checked="$user->is_admin" wire:change="toggleAdmin({{ $user->id }})" label="Admin" />
```

Each change calls `toggleAdmin`; the switch is blocked with a spinner until it answers.

### Turning on all at once

```blade
<form>
    <tk:switch.all />
    <tk:switch name="email" label="Email" />
    <tk:switch name="sms" label="SMS" />
</form>
```

A "Mark All" switch on top: turning it on turns on every enabled switch in the same form, and it shows on when all of them are on. `group` limits it to switches with the same `group`, anywhere on the page.

## Props

### `tk:switch`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `checked` | bool \| array | `false` | Turns the switch on at the first render. An array turns it on when it contains the switch's `value`. |
| `labelOn` | string | `null` | Text shown next to the switch while it is on. |
| `labelOff` | string | `null` | Text shown while it is off. |
| `iconOn` | string | `null` | An icon in the knob while on. |
| `iconOff` | string | `null` | An icon in the knob while off. |
| `align` | string | `left` | Where the switch sits next to the label: `left`, `right`, `justify-left` or `justify-right`, as on [`tk:checkbox`](checkbox.md#props). |
| `loading` | bool | auto | Shows a spinner and blocks the switch while its request runs. On with `wire:model.live` or `wire:change`. |
| `loadingDelay` | int \| string | `150` | Milliseconds (or `300ms`, `1s`) before the spinner shows. |
| `loadingMinDuration` | int \| string | `700` | Milliseconds the spinner stays at least, once shown, so it does not flash. |
| `group` | string | `null` | Puts the switch in a set controlled by the `tk:switch.all` with the same `group`. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `value`, `size`, `color`...). `wire:model` and `x-model` bind a bool, or an array of `value`s when several switches share the property.

### `tk:switch.all`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `Mark All` | The label, translated. |
| `group` | string | `null` | Controls the switches with this `group`. Without it, those with no `group` in the same form, or else in the same Livewire component, or else on the page. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:switch` | default | The label, in place of `label`. |

## Customization

Classes and attributes on the tag go to the `<input>`. The parts take the prefixes of the [field props](shared-props.md#field-props), and:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon-on:`, `icon-off:` | `tk:switch` | The icons in the knob. |
| `label-checked:`, `label-unchecked:` | `tk:switch` | The `labelOn` and `labelOff` texts. |
| `state:`, `state-group:` | `tk:switch` | The knob, and the element holding the track and the state texts. |

```blade
<tk:switch name="beta" label="Beta features" color="green" align="justify-right" label-checked:class="text-green-600" />
```

## Accessibility

A native checkbox with `role="switch"`, so screen readers announce "on" and "off". Without a label or state labels it is named "Switch" (translated); give it a `label` or an `aria-label`. While blocked by a request it has `aria-busy` and `aria-disabled`.

| Key | Action |
|-----|--------|
| Space | Flips the switch. |

## Behavior notes

- Old input from a failed submit restores the state.
- `tk:switch.all` skips disabled switches and dispatches `change` on each switch it flips, so `wire:model` on them updates.

## Related

- [Checkbox](checkbox.md): for choices that are confirmed later, with a form.
- `tk:toggle`: a segmented control for a few options.
