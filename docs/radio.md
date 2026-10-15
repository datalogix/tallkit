# Radio

A radio button with its label, for picking one value from a small set that is shown all at once. `tk:radio.group` renders the whole set from data, under a legend.

```
tk:radio
tk:radio.group
└── tk:radio ...
```

## Usage

```blade
<tk:radio.group wire:model="plan" label="Plan" :options="['free' => 'Free', 'pro' => 'Pro', 'team' => 'Team']" />
```

```php
public string $plan = 'pro';
```

A fieldset with "Plan" as its legend and three radio buttons, Pro selected. Choosing Team sets `$plan` to `'team'`. Arrow keys move the choice within the group.

### Written one by one

```blade
<tk:radio name="shipping" value="standard" label="Standard" description="5 to 7 days" checked />
<tk:radio name="shipping" value="express" label="Express" description="Next business day" />
```

Two radios sharing the `shipping` name, Standard selected, each with a description under its label. The form sends the value of the selected one.

### From data, with headings

```blade
<tk:radio.group
    name="region"
    label="Region"
    :options="['Americas' => ['us' => 'United States', 'br' => 'Brazil'], 'Europe' => ['de' => 'Germany', 'pt' => 'Portugal']]"
    required
/>
```

A heading per nested array, with its radios under it. `required` is passed to every radio, so the form cannot be sent without a choice.

## Props

### `tk:radio`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `checked` | bool \| array | `false` | Selects the radio on the first render. An array selects it when it contains the radio's `value`. |
| `align` | string | `left` | Where the button sits next to the label: `left`, `right`, `justify-left` or `justify-right`, as on [`tk:checkbox`](checkbox.md#props). |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `value`, `size`, `color`...). `value` is what the form sends when the radio is selected. `wire:model` and `x-model` bind the selected `value`.

### `tk:radio.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array \| Collection \| enum \| query | `null` | One radio per option, value as the key and label as the value. A nested array renders a heading followed by its options. `option-value` and `option-label` name the attributes to read when the options are arrays or models. |
| `value` | string | `null` | The value selected on the first render. |
| `label` | string \| slot | from the name | The legend of the group. |
| `description` | string \| slot | `null` | Text under the legend. |
| `size` | string | `md` | Passed to every radio, the legend and the headings. |
| `align` | string | `left` | Passed to every radio. |

`name`, `wire:model`, `x-model` and `required` are given to every radio. Without a `name`, the radios share a generated one, so they still form one group. Other props for the radios go through the `radio:` prefix (`radio:color="blue"`).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:radio` | default | The label, in place of `label`. |
| `tk:radio.group` | default | Radios written by hand, before those from `options`. |

## Customization

Classes and attributes on `tk:radio` go to the `<input>`; the parts take the prefixes of the [field props](shared-props.md#field-props), and:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `control:` | `tk:radio` | The box around the input. |
| `radio:` | `tk:radio.group` | Every radio of the group. |
| `heading:` | `tk:radio.group` | The headings of nested options. |
| `error:` | `tk:radio.group` | The validation message under the group. |

```blade
<tk:radio.group name="plan" :options="$plans" radio:color="blue" radio:align="justify-right" class="max-w-sm" />
```

## Accessibility

Native `<input type="radio">` elements with labels, grouped in a `<fieldset>` named by its legend. The validation message of the group is linked to the fieldset with `aria-describedby`, once, not under each radio. Arrow keys move the selection inside the group and Tab leaves it, as browsers do for radios with one name.

## Behavior notes

- Old input from a failed submit selects the radio that was chosen.
- A group with no options and no slot content renders nothing.

## Related

- [Checkbox](checkbox.md): picking any number of values.
- `tk:select`, [Combobox](combobox.md): picking one value from a longer list.
- `tk:toggle`: a segmented control for a few options.
