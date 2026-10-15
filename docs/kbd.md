# Kbd

A keyboard key or shortcut, shown in a small gray box, as in "Press ⌘K to search". Buttons, menu items and inputs render it from their `kbd` prop; use the tag directly in text.

## Usage

```blade
Press <tk:kbd label="Ctrl K" /> to search.
```

A `<kbd>` with "Ctrl K" in medium weight, on a light gray rounded background (translucent white in dark mode), inline with the text.

### Without the box

```blade
<tk:kbd variant="text" size="sm">⌘K</tk:kbd>
```

`text` drops the background and padding, leaving only the text in the key style, for tight places such as a menu item.

### Inside other components

```blade
<tk:button icon="magnify" label="Search" kbd="⌘K" />
<tk:input name="q" kbd="/" />
```

The button shows the hint after its label; the input, at the end of the box. Both use this tag.

## Props

### `tk:kbd`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The key text, translated. The default slot takes its place. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font size and padding. |
| `variant` | string | box | `text` renders the text only, without background or padding. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `prefix`, `suffix`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:kbd` | default | The key text, in place of `label`. |

## Customization

Classes and attributes go to the `<kbd>` element.

```blade
<tk:kbd label="Esc" class="font-mono uppercase" />
```

## Accessibility

A native `<kbd>`, which marks the text as keyboard input. It has `pointer-events-none`, so it never takes clicks from the button or item around it.

## Behavior notes

- With no label and no slot content, nothing is rendered.

## Related

- [Button](button.md), [Input](input.md): show a key hint with their `kbd` prop.
- [Badge](badge.md): a small colored label for counts and statuses.
