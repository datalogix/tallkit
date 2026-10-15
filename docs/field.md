# Field

The layout every form control is built on: label, description, the control, help text and validation error, stacked or side by side. The form tags (`tk:input`, `tk:select`, `tk:checkbox`...) already use it; write these tags yourself to give the same layout to a control of your own, or to join several controls into one group with attached prefix and suffix blocks.

```
tk:field.wrapper
├── tk:field.group
│   ├── tk:field.group.prefix
│   ├── (controls)
│   └── tk:field.group.suffix
└── tk:field.control

tk:field
```

`tk:field.wrapper` builds the whole field from props. `tk:field.control` adds icons, a spinner and other content inside the control's box. `tk:field.group` joins controls side by side. `tk:field` is only the grid that lays the parts out, for markup written by hand.

## Usage

```blade
<tk:field.wrapper id="color" name="color" label="Color" description="Used in the header" help="Any CSS color">
    <input id="color" name="color" type="color" aria-describedby="color-description color-help" data-tallkit-control class="h-10 w-20">
</tk:field.wrapper>
```

"Color" over "Used in the header", then the color input, then "Any CSS color" under it in muted text. When `color` has a validation error, the message shows under everything in red. The label points to the control through `for`, and the description and help get the ids `color-description` and `color-help`, so pass the control's `id` to the wrapper: without it the label is not linked and the texts have no ids.

### A group of controls

```blade
<tk:field.group label="Search" description="Find a user by name or email">
    <tk:input name="q" :label="false" aria-label="Query" />
    <tk:button label="Go" />
</tk:field.group>
```

The input and the button are joined into one block, with only the outer corners rounded. The group has `role="group"`, named by "Search" and described by its description, so screen readers announce both when focus enters it.

### Attached prefix and suffix

```blade
<tk:field.group prefix="https://" suffix=".com">
    <tk:input name="site" :label="false" aria-label="Site" />
</tk:field.group>
```

Gray blocks with "https://" before the input and ".com" after it, attached to it. Inputs take `prefix` and `suffix` themselves (`<tk:input prefix="R$" />`); the group is for controls that do not, or for several controls between the same blocks.

### Inline layout

```blade
<tk:field inline align="right">
    <label for="news" data-tallkit-label>Newsletter</label>
    <input id="news" type="checkbox" data-tallkit-control>
</tk:field>
```

The label on the left and the checkbox at the end of the line, as `tk:checkbox` and `tk:switch` lay themselves out. `tk:field` places its children by their `data-tallkit-label`, `data-tallkit-control` and `data-tallkit-text` attributes.

## Props

### `tk:field.wrapper`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The field whose validation error is shown. |
| `id` | string | `null` | The id of the control: the label's `for`, and the base of the ids of the description, help and error. Not derived from `name` here. |
| `inline` | bool | `false` | Puts the control and the label side by side, for checkboxes and switches. |
| `align` | string | `left` | With `inline`: `left` puts the control before the label, `right` after it; `justify-left` and `justify-right` do the same with the label taking the remaining width. |

Also accepts the [field props](shared-props.md#field-props): `label`, `description`, `help`, `prefix`, `suffix`, `badge`, `info`, `size`, `showError`, `bag`... When there is nothing around the control (no label, description, help, prefix or suffix), only the control and its error are rendered.

### `tk:field.control`

Takes the [field control props](shared-props.md#field-control-props): `prepend`, `icon`, `iconTrailing`, `kbd`, `append` and `loading`, plus `size`. With none of them and no `class`, it renders the slot alone.

### `tk:field.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The label of the group, which names it. |
| `description` | string \| slot | `null` | Text under the label, which describes the group. |
| `help` | string \| slot | `null` | Text under the group. |
| `prefix` | string \| slot | `null` | A block attached before the controls. |
| `suffix` | string \| slot | `null` | A block attached after the controls. |
| `size` | string | `md` | Sets the font size of the prefix and suffix. |

`labelPrepend`, `labelAppend` and `badge` work as on the field props.

### `tk:field.group.prefix` and `tk:field.group.suffix`

Take `size` and the [element props](shared-props.md#element-props), so a block can have an icon, a label or any slot content.

### `tk:field`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `inline` | bool | `false` | Side-by-side layout. |
| `align` | string | `left` | With `inline`, as on `tk:field.wrapper`. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:field.wrapper` | default | The control. |
| `tk:field.control` | default | The control. |
| `tk:field.group` | default | The controls to join. |
| `tk:field.group.prefix`, `tk:field.group.suffix` | default | The block content, in place of `label`. |
| `tk:field` | default | The label, control and texts, laid out by their data attributes. |

## Customization

Classes and attributes on `tk:field.wrapper` go to the parts through the prefixes of the [field props](shared-props.md#field-props) (`field:`, `label:`, `description:`, `help:`, `error:`, `group:`, `prefix:`, `suffix:`...); `tk:field.control` takes those of the [field control props](shared-props.md#field-control-props). On `tk:field.group`, `class` goes to the block that joins the controls, and `prefix:` / `suffix:` reach the blocks.

```blade
<tk:field.group prefix="@" prefix:class="font-mono" class="max-w-xs">
    <tk:input name="handle" :label="false" aria-label="Handle" />
</tk:field.group>
```

## Accessibility

The label is a `<label>` whose `for` is the field's id. The description, the help text and the error get the ids `<id>-description`, `<id>-help` and `<id>-error`, which the form controls list in their `aria-describedby`; a control of your own should do the same. The error is a polite live region. A `tk:field.group` with a label or a description is a `role="group"` labelled and described by them.

## Behavior notes

- The label is dimmed while the control inside has `disabled`.
- A `tk:field.group` with `label:for` keeps a real `<label>` pointing to that id instead of becoming a named group.
- The validation error is read from the bag by `name`; without a `name`, no error is shown.

## Related

- `tk:fieldset`: groups several fields under a `<legend>`, for a section of a form.
- `tk:label`: the label alone, with the badge and info icon.
- [Error](error.md): a validation message placed by hand.
- `tk:input`: the text input built on these tags.
