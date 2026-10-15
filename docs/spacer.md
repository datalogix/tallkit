# Spacer

An empty element that takes space in a flex row or column: without a size it grows to fill what is left, pushing the next items to the other end; with a size it is a fixed gap.

## Usage

```blade
<tk:header>
    <tk:brand />
    <tk:spacer />
    <tk:appearance.menu />
</tk:header>
```

The brand at the start of the header and the account menu pushed to the end: the spacer grows (`flex-1`) to fill the space between them.

### A fixed gap

```blade
<div class="flex items-center">
    <tk:button label="Save" />
    <tk:spacer size="lg" />
    <tk:button variant="ghost" label="Cancel" />
</div>
```

A square gap, 2.5rem on each side at `lg`, between the two buttons, in a row that has no `gap` of its own.

## Props

### `tk:spacer`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `size` | string | grows | A fixed square gap: `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Without it the spacer fills the remaining space. |

## Customization

Classes and attributes go to the `<div>`.

```blade
<tk:spacer class="max-sm:hidden" />
```

## Accessibility

Hidden from screen readers (`aria-hidden="true"`).

## Related

- [Separator](separator.md): a visible line between items.
- [Layout](layout.md): uses a spacer in its header.
