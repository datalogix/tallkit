# Shared props

Groups of props that several components accept. Each component's doc names the groups it takes and links here.

## Element props

Accepted by the components that render a label with decorations around it: buttons, links, headings, texts, badges and the like. In the order they render:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `prepend` | slot | `null` | Content before everything else. |
| `icon` | string \| slot | `null` | An icon name, or any content, before the label. |
| `iconDot` | bool \| string \| int | `null` | A dot over the icon. A value of one or two characters shows inside it, as a counter. |
| `prefix` | string \| slot | `null` | Small muted text before the label. |
| `label` | string \| slot | `null` | The text, translated. The default slot takes its place when it has content. |
| `suffix` | string \| slot | `null` | Small muted text after the label. |
| `iconTrailing` | string \| slot | `null` | An icon name, or any content, after the label. |
| `badge` | string \| slot | `null` | A badge after the label. |
| `kbd` | string \| slot | `null` | A keyboard shortcut hint after the label. |
| `append` | slot | `null` | Content after everything else. |
| `ariaLabel` | string | from the label | The accessible name. Links and buttons take it from `label`, or from the tooltip when there is no label and no slot content. |

Each part takes attributes through its prefix: `icon:`, `icon-dot:`, `prefix:`, `suffix:`, `icon-trailing:`, `badge:` and `kbd:`.

## Field props

Accepted by the form controls (`tk:input`, `tk:select`, `tk:autocomplete` and the like). They build what goes around the control: label, texts and validation error.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| bool \| slot | from the name | The label. Without one it is derived from the field name (`first_name` gives "First Name") and translated; `false` removes it. |
| `labelPrepend` | slot | `null` | Content before the label text. |
| `labelAppend` | slot | `null` | Content after the label text. |
| `badge` | string \| slot | `null` | A badge next to the label. |
| `info` | string \| slot | `null` | A help icon next to the label, with this text as its tooltip. |
| `description` | string \| slot | `null` | Text between the label and the control. |
| `help` | string \| slot | `null` | Text under the control. |
| `prefix` | string \| slot | `null` | An attached block before the control. |
| `suffix` | string \| slot | `null` | An attached block after the control. |
| `placeholder` | string \| bool | `null` | The placeholder, translated. `true` uses the label. |
| `value` | mixed | `null` | The initial value. Old input from a failed submit takes precedence. Inside Livewire use `wire:model` instead. |
| `id` | string | from the name | The id of the control, which the label points to. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |
| `color` | string | `null` | The color of the focus ring. |
| `invalid` | bool | from the errors | Forces the invalid state. By default the field is invalid when its name has a validation error. |
| `showError` | bool | `true` | `false` hides the validation message under the control. |
| `bag` | string | `null` | The error bag to read the validation errors from. |
| `wireModel` | bool | `true` | Inside a Livewire component, a field is bound to the public property with the same name. `false` turns that off. |

Each part takes attributes through its prefix: `field:` (the wrapper), `label:`, `info:`, `badge:`, `description:`, `help:`, `error:`, `group:`, `prefix:` and `suffix:`.

## Field control props

Accepted by the same controls. They put content inside the control itself, next to the text.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `prepend` | slot | `null` | Content at the start of the control. |
| `icon` | string \| slot | `null` | An icon name, or any content, at the start. |
| `iconTrailing` | string \| slot | `null` | An icon name, or any content, at the end. |
| `kbd` | string | `null` | A keyboard shortcut hint at the end. |
| `append` | slot | `null` | Content at the end of the control. |
| `loading` | bool \| string | `false` | Shows a spinner while a Livewire request runs. A string is the `wire:target`; with `wire:model.live` the target is the model. |

Each part takes attributes through its prefix: `control:` (the wrapper), `prepend:`, `icon:`, `icon-trailing:`, `kbd:`, `append:` and `loading:`.
