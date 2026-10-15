# Checkbox

A checkbox with its label, for a yes/no choice or for picking any number of values from a set. `tk:checkbox.group` renders a set of them from data, and `tk:checkbox.all` adds a box that checks or unchecks the others at once.

```
tk:checkbox.group
├── tk:checkbox
└── tk:checkbox ...

tk:checkbox.all
```

## Usage

```blade
<tk:checkbox name="remember" />
```

A box with the label "Remember", taken from the name. Clicking the box or the label checks it, and the form sends `remember=on`.

### Bound to Livewire

```blade
<tk:checkbox wire:model="terms" label="I accept the terms" />
```

```php
public bool $terms = false;
```

Checking the box sets `$terms` to `true`; unchecking it sets it back to `false`.

### Several values in one property

```blade
@foreach ($roles as $role)
    <tk:checkbox wire:model="selectedRoles" :value="$role->slug" :label="$role->name" wire:key="role-{{ $role->id }}" />
@endforeach
```

```php
public array $selectedRoles = ['editor'];
```

Checkboxes bound to the same array property each add or remove their own `value`: the Editor box starts checked, and checking Admin makes `$selectedRoles` `['editor', 'admin']`.

### A group from options

```blade
<tk:checkbox.group wire:model="tags" label="Tags" description="Pick any that apply" :options="['news' => 'News', 'sports' => 'Sports', 'tech' => 'Tech']" />
```

```php
public array $tags = [];
```

A fieldset with the legend "Tags", the description under it and one checkbox per option. Checking News and Tech sets `$tags` to `['news', 'tech']`. Outside Livewire, give it a `name` and the boxes submit as `tags[]`; `:value="['news']"` checks News on the first render.

### Select all

```blade
<form>
    <tk:checkbox.all />

    <tk:checkbox name="notify_email" label="Email" />
    <tk:checkbox name="notify_sms" label="SMS" />
    <tk:checkbox name="notify_push" label="Push" />
</form>
```

A "Mark All" box above the three. Clicking it checks every enabled box in the same form, and clicking it again unchecks them. When only some are checked it shows a minus, as partially checked. To control a specific set anywhere on the page, give the same `group` to `tk:checkbox.all` and to the boxes (or to a `tk:checkbox.group`).

## Props

### `tk:checkbox`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `checked` | bool \| array | `false` | Checks the box on the first render. An array checks it when it contains the box's `value`. |
| `indeterminate` | bool | `false` | Starts in the partially checked state, showing a minus. |
| `align` | string | `left` | Where the box sits next to the label: `left` (before it), `right` (right after it), `justify-left` (box at the start, label at the end of the row) or `justify-right` (label at the start, box at the end of the row). |
| `iconOn` | string | `check` | The icon shown when checked. |
| `iconOff` | string | `null` | An icon shown when unchecked. None by default. |
| `iconIndeterminate` | string | `minus` | The icon shown in the partially checked state. Only used with `indeterminate`. |
| `group` | string | `null` | Puts the box in a named set controlled by the `tk:checkbox.all` with the same `group`. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `size`...). For a checkbox, `value` is what the form sends when the box is checked (the browser sends `on` without one), `size` sets the size of the box, and `color` fills the checked box (`accent`, or a palette color such as `blue`) instead of the focus ring. `prefix`, `suffix` and `placeholder` have no effect.

`wire:model` and `x-model` bind a bool, or an array of the `value`s of the checked boxes when several boxes share the property.

### `tk:checkbox.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array \| Collection \| enum \| query | `null` | One checkbox per option, value as the key and label as the value. A nested array renders a heading followed by its options. `option-value` and `option-label` name the attributes to read when the options are arrays or models. |
| `value` | array \| string | `null` | The values checked on the first render. |
| `label` | string \| slot | from the name | The legend of the group. Without one it is derived from the name or the `wire:model`. |
| `description` | string \| slot | `null` | Text under the legend. |
| `size` | string | `md` | Passed to every checkbox, the legend and the headings. |
| `align` | string | `left` | Passed to every checkbox. |
| `iconOn` | string | `check` | Passed to every checkbox. |
| `iconOff` | string | `null` | Passed to every checkbox. |
| `group` | string | `null` | Passed to every checkbox, so a `tk:checkbox.all` with the same `group` controls them. |

`name`, `wire:model` and `x-model` are given to every checkbox; a `name` gets `[]` appended so the form sends an array. Other props for the checkboxes go through the `checkbox:` prefix (`checkbox:color="blue"`).

### `tk:checkbox.all`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `Mark All` | The label, translated. |
| `group` | string | `null` | Controls the boxes with this `group` anywhere on the page. Without it, the boxes with no `group` in the same form, or else in the same Livewire component, or else on the page. |

Also accepts the [field props](shared-props.md#field-props) and the icon props of `tk:checkbox` (`iconOn`, `iconOff`, `iconIndeterminate`).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:checkbox` | default | The label, in place of the `label` prop. |
| `tk:checkbox.group` | default | Checkboxes of your own, rendered before the ones from `options`. |

## Customization

Classes and attributes on `tk:checkbox` go to the `<input>`; on `tk:checkbox.group` they go to the `<fieldset>`. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `control:` | `tk:checkbox` | The element wrapping the input and its icons; sets the box size. |
| `icon-area:` | `tk:checkbox` | The layer over the input that holds the icons. |
| `icon-on:` | `tk:checkbox` | The checked icon. |
| `icon-off:` | `tk:checkbox` | The unchecked icon. |
| `icon-indeterminate:` | `tk:checkbox` | The partially checked icon. |
| `checkbox:` | `tk:checkbox.group` | Every checkbox. |
| `heading:` | `tk:checkbox.group` | The headings of grouped options. |
| `error:` | `tk:checkbox.group` | The validation message under the group. |
| `label:` | `tk:checkbox.group` | The legend. |
| `description:` | `tk:checkbox.group` | The description under the legend. |

The field prefixes work on `tk:checkbox` too (`field:`, `label:`, `description:`, `help:`, `error:`...).

```blade
<tk:checkbox name="remember" class="rounded-full" control:class="size-5" icon-on:class="text-amber-300" />
```

## Accessibility

A native `<input type="checkbox">` with a `<label for>`, so clicking the label toggles it and the partially checked state is exposed by the browser. `aria-describedby` points to the description, the help text and the validation message when they are shown, and an invalid field gets `aria-invalid`. A box with no label and no slot content is named "Checkbox" (translated) through `aria-label`. `tk:checkbox.group` is a `<fieldset>` whose legend names the group, with `aria-describedby` pointing to its description and its validation message.

| Key | Action |
|-----|--------|
| Tab | Moves to the next checkbox; each one is a tab stop. |
| Space | Toggles the focused checkbox. |

## Behavior notes

- `indeterminate` only sets the first render: the first click checks the box and the minus goes away.
- Inside a Livewire component, a `name` that matches a public property binds to it with no `wire:model` (see `wireModel` in the [field props](shared-props.md#field-props)).
- After a failed submit outside Livewire, the old input decides whether the box is checked, over `checked`.
- Attributes go to the input, so native events are heard on the tag: `x-on:change` on `tk:checkbox` runs when the user toggles it.
- `tk:checkbox.all` skips disabled boxes and dispatches `change` on each box it toggles, so `wire:model` on those boxes updates. It follows the boxes from the first render and as they change, also after a Livewire update, showing checked when all are checked and partially checked when only some are.
- In `tk:checkbox.group` the validation message shows once, under the group, and not under each checkbox. A group with no options and no slot content renders nothing.
- `disabled` on `tk:checkbox.group` disables every checkbox in it, as a disabled `<fieldset>` does.

## Related

- `tk:switch`: an on/off toggle for a setting that applies right away.
- `tk:radio`: exactly one choice from a set.
- `tk:select`: many options in a compact control, with `multiple` for several.
