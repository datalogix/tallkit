# Legend

The title of a `<fieldset>`, styled as a heading. [`tk:fieldset`](fieldset.md) renders it from its `label`; write the tag yourself inside a fieldset of your own, or when the title needs markup the `label` prop cannot carry.

## Usage

```blade
<fieldset>
    <tk:legend label="Shipping address" />

    <tk:input name="street" />
    <tk:input name="city" />
</fieldset>
```

A `<legend>` with "Shipping address" in the heading style (semibold, two steps above body text), which names the fieldset for screen readers.

### With an icon and a badge

```blade
<tk:fieldset>
    <tk:legend label="Billing" icon="credit-card" badge="Optional" />

    <tk:input name="tax_id" />
</tk:fieldset>
```

The icon before the title and the badge after it, vertically centered on the line.

## Props

### `tk:legend`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The title, translated. The default slot takes its place. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `suffix`...) and the props of [`tk:heading`](heading.md) (`size`, `scale`, `variant`, `color`).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:legend` | default | The title, in place of `label`. |

## Customization

Classes and attributes go to the `<legend>` element; the parts take the prefixes of the element props (`icon:`, `badge:`...).

```blade
<tk:legend label="Billing" size="sm" variant="subtle" class="uppercase tracking-wide" />
```

## Accessibility

A native `<legend>`: as the first child of a `<fieldset>`, it names the group, and screen readers announce it when focus enters any field inside.

## Behavior notes

- With no label, no slot content and no other part (icon, badge...), nothing is rendered.
- It is dimmed while its fieldset is disabled.

## Related

- [Fieldset](fieldset.md): the group that renders a legend from its `label`.
- [Heading](heading.md): the same style for titles that are not fieldset legends.
- [Label](label.md): the label of a single field.
