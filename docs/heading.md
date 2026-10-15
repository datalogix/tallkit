# Heading

A title in the type scale of the other components: bolder and two steps larger than body text of the same size. With `level` it is a real `<h1>`–`<h6>`; without it, a `<p>` that only looks like a heading. It takes the element props, so it can carry an icon, a badge or a suffix.

## Usage

```blade
<tk:heading level="2" label="Billing" />
```

An `<h2>` with "Billing" in semibold, large text, in the strong neutral color (white in dark mode). The label is translated.

### Sizes and colors

```blade
<tk:heading level="1" size="2xl" label="Dashboard" />
<tk:heading level="3" size="sm" variant="subtle" label="Last 30 days" />
<tk:heading level="3" color="blue" icon="information" label="Tips" />
```

`size` moves the heading along the same scale as the other components; `variant` and `color` change its color. The last one has an info icon before the text, both in blue.

### A heading with more than text

```blade
<tk:heading level="2" badge="3">
    Open invoices
</tk:heading>
```

The slot takes the place of `label`, and the badge sits after it, vertically centered on the line.

### Title and text together

```blade
<tk:heading level="2" label="Notifications" />
<tk:text label="Choose what we email you about." />
```

A heading followed by a `tk:text` gets a small gap under it, without margins of your own.

## Props

### `tk:heading`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `level` | int | `null` | The heading level, `1` to `6`, rendered as `<h1>`–`<h6>`. Without it (or with another value) the tag is a `<p>`. |
| `label` | string \| slot | `null` | The text, translated. The default slot takes its place when it has content. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |
| `scale` | string | `largest` | How far above body text of the same `size` it is: `smallest`, `small`, `default`, `large` or `largest`. |
| `variant` | string | strong | The neutral color: `strong` (darker), `subtle` (muted), `accent` (the accent color), or `none` for no color class. |
| `color` | string | `null` | A color name (`blue`, `red`...). Takes precedence over `variant`. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `suffix`, `kbd`...). `as` on the tag sets any other element (`as="legend"`).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:heading` | default | The text, in place of `label`. |

## Customization

Classes and attributes on the tag go to the heading element; the parts from the element props take their prefixes (`icon:`, `badge:`...).

```blade
<tk:heading level="2" label="Billing" class="tracking-tight" icon="credit-card" icon:class="text-zinc-400" />
```

## Accessibility

Only `level` makes it a heading for assistive technology: without it, the text is read as a paragraph, however large it looks. Choose the level by the document outline, and the look with `size` and `scale`.

## Behavior notes

- With no label, no slot content and no other part (icon, badge...), nothing is rendered.
- The heading is as wide as its text (`w-fit`), so a background or border on it hugs the text.

## Related

- `tk:text`: body text in the same scale, for the paragraph under a heading.
- `tk:legend`: the heading of a `<fieldset>`.
- [Element](element.md): the building block it is made of.
