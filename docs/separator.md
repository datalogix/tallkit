# Separator

A thin line between blocks of content, horizontal or vertical, optionally with a label in the middle such as "or".

## Usage

```blade
<tk:separator />
```

A 1px gray line across the full width, announced to screen readers as a separator.

### With a label

```blade
<tk:button label="Sign in with Google" />
<tk:separator label="or" />
<tk:button label="Sign in with email" />
```

Two lines with "or" between them, centered, in muted medium-weight text. The label is translated; the slot can be used instead for any markup.

### Vertical

```blade
<div class="flex items-center gap-4 h-6">
    <tk:link href="/docs" label="Docs" />
    <tk:separator vertical />
    <tk:link href="/blog" label="Blog" />
</div>
```

A 1px line as tall as the row, between the two links, with `aria-orientation="vertical"`.

## Props

### `tk:separator`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | Text in the middle of the line, translated. The default slot takes its place. |
| `vertical` | bool | `false` | A vertical line, as tall as its container. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:separator` | default | The content in the middle of the line, in place of `label`. |

## Customization

Classes and attributes go to the line (to both halves when there is a label). The label takes a prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `content:` | `tk:separator` | The label in the middle (`content:class="mx-2 text-xs uppercase"`). |

```blade
<tk:separator label="or" class="bg-zinc-300" content:class="mx-3 text-xs uppercase" />
```

## Accessibility

`role="separator"`, with `aria-orientation="vertical"` when vertical. The label is read as text.

## Related

- [Menu](menu.md): `tk:menu.separator`, the line between menu items.
- [Section](section.md): draws a separator under its header.
