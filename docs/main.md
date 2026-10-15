# Main

The main content area of a page, and the piece that turns its parent into the page grid. With `tk:main` inside it, an element lays its children out in the header, sidebar, main, aside and footer areas, so [`tk:header`](header.md), `tk:sidebar`, [`tk:aside`](aside.md) and [`tk:footer`](footer.md) find their places by themselves.

## Usage

```blade
<div class="min-h-screen">
    <tk:header>Acme</tk:header>

    <tk:main>
        <tk:heading level="1" label="Dashboard" />
    </tk:main>

    <tk:footer>© 2026 Acme</tk:footer>
</div>
```

The parent `<div>` becomes a grid: the header across the top, the main area filling the remaining height, the footer at the bottom. The `<main>` has padding (`p-6`, `p-8` from `lg`).

### Centered column

```blade
<tk:main container="lg">
    ...
</tk:main>
```

The content is held in a centered column of that width, as [`tk:container`](container.md) sizes it. `container` alone (`true`) uses the container's default width.

## Props

### `tk:main`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `container` | bool \| string | `false` | Centers the content in a column: `true` for the default width, or a size (`xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `full`). |
| `id` | string | `main-content` | The element id, the target of a "skip to content" link. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:main` | default | The page content. |

## Customization

Classes and attributes go to the `<main>` element.

```blade
<tk:main container="xl" class="space-y-8">
    ...
</tk:main>
```

Inside a [`tk:container`](container.md), the main area drops its own horizontal padding, so the container's applies.

## Accessibility

A native `<main>`, the `main` landmark, with `tabindex="-1"` and the id `main-content`, so a skip link (`<a href="#main-content">`) can move the focus to it.

## Behavior notes

- The grid is set on the direct parent of `tk:main`: header, sidebar, aside and footer must be its siblings.
- Below the `lg` breakpoint the aside moves under the main content, and the footer under the aside.
- Use one `tk:main` per page.

## Related

- [Layout](layout.md): ready-made layouts with the main area already in place.
- [Header](header.md), [Aside](aside.md), [Footer](footer.md): the other areas of the grid.
- [Container](container.md): the centered column the `container` prop uses.
