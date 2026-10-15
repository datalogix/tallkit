# Text

A paragraph of body text in the type scale and colors of the other components, for descriptions, hints and captions. It takes the element props, so it can carry an icon or a badge, and renders nothing when it has no content.

## Usage

```blade
<tk:text label="Invoices are issued on the first day of each month." />
```

A `<p>` in the base size and the default text color (lighter in dark mode). The label is translated, and line breaks in it are kept.

### Sizes, weights and colors

```blade
<tk:text size="sm" variant="subtle" label="Last updated 5 minutes ago" />
<tk:text weight label="Total: R$ 120,00" variant="strong" />
<tk:text color="red" icon="alert-circle" label="Payment failed." />
```

Small muted text; bold, darker text; red text with an icon before it.

### Inside other markup

```blade
<tk:text as="span" size="xs">{{ $user->email }}</tk:text>
```

`as` renders another element, here a `<span>` inside a line; the slot takes the place of `label` and is not translated.

## Props

### `tk:text`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The text, translated. The default slot takes its place. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font and icon size. |
| `scale` | string | `null` | `smallest`, `small`, `large` or `largest`: moves the font size one or two steps from `size`. |
| `weight` | bool | `false` | Uses the heavier weight of the size. |
| `variant` | string | default | `strong` (darker), `subtle` (muted), `accent` (the accent color), `none` (no color class), or the default body color. |
| `color` | string | `null` | A color name (`red`, `green`...). Takes precedence over `variant`. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `suffix`...) and `as` for the element to render (`p` by default; `href` makes it a link).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:text` | default | The text, in place of `label`. |

## Customization

Classes and attributes go to the element; the parts take the prefixes of the element props (`icon:`, `badge:`...).

```blade
<tk:text label="Draft" icon="pencil" class="italic" icon:class="text-amber-500" />
```

## Accessibility

Plain text in a `<p>`; read in order. A paragraph that names something (a field, a section) should be linked to it with `aria-describedby`, as the field props do for their description and help.

## Behavior notes

- With no label, no slot content and no other part (icon, badge...), nothing is rendered.
- A `tk:text` right after a `tk:heading` gets a small gap above it.

## Related

- [Heading](heading.md): the title above the text.
- [Link](link.md): text that links somewhere, built on this tag.
- [Element](element.md): the building block it is made of.
