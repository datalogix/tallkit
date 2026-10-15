# Fieldset

A `<fieldset>` that groups related fields under a title, such as the address or the billing section of a form. The title is a `<legend>`, so screen readers announce it when focus enters any field inside.

## Usage

```blade
<tk:fieldset label="Address" description="Where we ship your order">
    <tk:input wire:model="street" label="Street" />
    <tk:input wire:model="city" label="City" />
</tk:fieldset>
```

"Address" as a heading, "Where we ship your order" in muted text under it, then the two fields with even spacing. The fieldset is described by the description, so it is read together with the title.

### Disabling a whole section

```blade
<tk:fieldset label="Shipping" :disabled="$sameAsBilling">
    <tk:input wire:model="shipping.street" label="Street" />
    <tk:input wire:model="shipping.city" label="City" />
</tk:fieldset>
```

While `$sameAsBilling` is true every control inside is disabled by the browser, and the title and labels are dimmed. Disabled controls are not sent with the form.

### A title with more than text

```blade
<tk:fieldset label="Billing" label:badge="Optional" label:icon="credit-card">
    <tk:input name="tax_id" label="Tax ID" />
</tk:fieldset>
```

The title takes the [element props](shared-props.md#element-props) through the `label:` prefix: here an icon before it and an "Optional" badge after it.

## Props

### `tk:fieldset`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The title, rendered as a `<legend>` and translated. |
| `description` | string \| slot | `null` | Text under the title, linked to the fieldset with `aria-describedby`. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the size of the title and the description. |
| `id` | string | generated | The fieldset id, also the base of the description id (`<id>-description`). |

`disabled` is the native attribute and disables every control inside.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:fieldset` | default | The fields. |

## Customization

Classes and attributes on the tag go to the `<fieldset>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `label:` | `tk:fieldset` | The `<legend>`, including its element props (`label:icon`, `label:badge`...). |
| `description:` | `tk:fieldset` | The description. |

```blade
<tk:fieldset label="Address" class="rounded-lg border p-4" label:class="text-lg" description:class="italic">
    <tk:input name="street" label="Street" />
</tk:fieldset>
```

## Accessibility

A native `<fieldset>` with a `<legend>`, which gives the group its name. The description is added to the fieldset's `aria-describedby`, after any id passed in that attribute.

## Behavior notes

- Without `label` and `description` no `<legend>` is rendered; a `tk:legend` can be placed in the slot instead.
- The spacing between fields applies to the form fields placed directly inside, so nested layouts keep their own.

## Related

- [Field](field.md): `tk:field.group` joins controls in one row, where a fieldset groups whole fields.
- `tk:legend`: the title alone, for a `<fieldset>` written by hand.
- `tk:form.section`: a titled section of a form with a side description, for longer forms.
