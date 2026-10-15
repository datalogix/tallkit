# Footer

The bottom area of a page layout, for copyright, secondary links and the like. It takes the `footer` area of the grid that `tk:main` builds, so it sits under the main content and next to the sidebar when there is one.

## Usage

```blade
<div class="min-h-screen">
    <tk:header>Acme</tk:header>

    <tk:main>
        Page content
    </tk:main>

    <tk:footer>
        © 2026 Acme
    </tk:footer>
</div>
```

A `<footer>` under the main content, with padding and a light gray background (dark gray in dark mode).

### Centered content

```blade
<tk:footer container="lg">
    <div class="flex justify-between">
        <span>© 2026 Acme</span>
        <a href="/terms">Terms</a>
    </div>
</tk:footer>
```

The background still spans the whole width, while the content is held in a centered column, as [`tk:container`](container.md) with that size. `container` alone (`true`) uses the container's default width.

### Always in view

```blade
<tk:footer sticky variant="inverse">
    <tk:button variant="primary" label="Save changes" />
</tk:footer>
```

The footer sticks to the bottom of the viewport while the page scrolls, over the content, with a top border and a shadow. `inverse` gives it a dark background (light in dark mode).

## Props

### `tk:footer`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `sticky` | bool | `false` | Keeps the footer at the bottom of the viewport while the page scrolls. |
| `container` | bool \| string | `false` | Holds the content in a centered column: `true` for the default width, or a size (`xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `full`). |
| `variant` | string | light gray | The background: `subtle` (white), `strong`, `inverse`, `accent`, `ghost` (transparent), `none` (no classes), or a color name for a tinted one (`blue`, `green`...). |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:footer` | default | The footer content. |

## Customization

Classes and attributes on the tag go to the `<footer>`. With `container`, the content is inside a container `<div>`, reached with a prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:footer` | The centered column, when `container` is set. |

```blade
<tk:footer container="lg" class="text-sm" container:class="py-4!">
    © 2026 Acme
</tk:footer>
```

The padding (`p-6`, `lg:p-8`) is on the `<footer>` itself, or on the container when `container` is set.

## Accessibility

A native `<footer>`; placed at the top level of the page, outside `<main>` and `<article>`, browsers expose it as the `contentinfo` landmark.

## Behavior notes

- The footer takes its place from the grid set up by `tk:main` on their common parent. Outside that layout it is a plain block.
- With a `tk:aside`, the aside runs down beside the footer on large screens; below `lg` the footer comes after the aside.

## Related

- `tk:header`: the top area of the same layout.
- [Aside](aside.md): the side column of the same layout.
- [Container](container.md): the centered column the `container` prop uses.
