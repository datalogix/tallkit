# Label

The label of a form field: the text above the control, with optional icon, badge, help icon and content on either side, such as a "Forgot password?" link. Every form control renders it from its `label` prop; use the tag directly for a control of your own.

## Usage

```blade
<tk:label for="nickname" label="Nickname" />
<input id="nickname" name="nickname">
```

A `<label for="nickname">` with "Nickname" in medium weight, in the strong text color. Clicking it focuses the input, as a native label does. The label gets the id `nickname-label`, for an `aria-labelledby` elsewhere.

### Content beside the label

```blade
<tk:input name="password" type="password">
    <x-slot:label-append>
        <tk:link href="/forgot-password" label="Forgot password?" />
    </x-slot:label-append>
</tk:input>
```

"Password" at the start of the line and the link at the end. `labelPrepend` places content before the label in the same way.

### Badge and help icon

```blade
<tk:input name="company" badge="Optional" info="Shown on your invoices." />
```

The label is followed by an "Optional" badge and a help icon whose tooltip shows the text. Both are props of the field, passed on to its label.

### Visible only to screen readers

```blade
<tk:input name="search" label="Search" label:sr-only icon="magnify" />
```

The label is not shown, but screen readers still announce the input as "Search".

## Props

### `tk:label`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The text, translated. The default slot takes its place. |
| `for` | string | `null` | The id of the control. Makes the label a `<label for>` and gives it the id `<for>-label`. |
| `as` | string | `label` with `for`, `span` without | The element to render. |
| `labelPrepend` | slot | `null` | Content at the start of the line, before the label. |
| `labelAppend` | slot | `null` | Content at the end of the line, after the label. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |
| `srOnly` | bool | `false` | Hides the label visually, keeping it for screen readers. |
| `info` | string \| slot | `null` | A help icon after the label, with this text as its tooltip. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `suffix`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:label` | default | The text, in place of `label`. |
| `tk:label` | `labelPrepend` | Content before the label. |
| `tk:label` | `labelAppend` | Content after the label. |

## Customization

Classes and attributes go to the inner element that holds the text. To reach the other parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:label` | The `<label>` (or `<span>`) element. |
| `area:` | `tk:label` | The row holding the label and the prepend or append content, when there is any. |
| `label-prepend:` | `tk:label` | The prepend content. |
| `label-append:` | `tk:label` | The append content. |
| `info:` | `tk:label` | The help icon. |

From a field, the label takes the `label:` prefix: `<tk:input name="email" label:class="text-lg" />`.

## Accessibility

With `for`, a native `<label>` linked to the control. Without it (a `<span>`), clicking the label still focuses the control of the same field, checks a checkbox, or picks a radio; but it does not name the control, so give the control an `aria-labelledby` pointing to the label.

## Behavior notes

- With no label and no slot content, nothing is rendered, including the prepend and append content.

## Related

- [Field](field.md): the layout of label, control, help and error that renders it.
- [Input](input.md): the `label`, `badge`, `info`, `labelPrepend` and `labelAppend` props pass through to this tag.
- `tk:legend`: the title of a fieldset.
