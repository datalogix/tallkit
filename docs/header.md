# Header

The top bar of a page layout, for the brand, the main navigation and the account menu. It takes the `header` area of the grid that `tk:main` builds, so it spans the page above the main content (and beside the sidebar when the sidebar comes first).

## Usage

```blade
<div class="min-h-screen">
    <tk:header>
        <tk:brand />

        <tk:appearance.menu class="ms-auto" />
    </tk:header>

    <tk:main>
        Page content
    </tk:main>
</div>
```

A `<header>` at least 4rem tall, with horizontal padding and a light gray background (dark gray in dark mode). Its children are laid out in a row, vertically centered: here the brand at the start and the account menu pushed to the end.

### Centered content

```blade
<tk:header container="lg">
    <tk:brand />
    <tk:nav class="ms-auto" :items="$links" />
</tk:header>
```

The background spans the whole width, while the content is held in a centered row, as [`tk:container`](container.md) with that size. `container` alone (`true`) uses the container's default width.

### Always in view

```blade
<tk:header sticky variant="subtle">
    <tk:brand />
</tk:header>
```

The header stays in view while the page scrolls, over the content, with a bottom border and a shadow. `subtle` gives it a white background (near black in dark mode).

## Props

### `tk:header`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `sticky` | bool | `false` | Keeps the header in view while the page scrolls. |
| `container` | bool \| string | `false` | Holds the content in a centered row: `true` for the default width, or a size (`xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `full`). |
| `variant` | string | light gray | The background: `subtle` (white), `strong`, `inverse`, `accent`, `ghost` (transparent), `none` (no classes), or a color name for a tinted one (`blue`, `green`...). |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:header` | default | The header content, laid out in a row. |

## Customization

Classes and attributes on the tag go to the `<header>`. With `container`, the content is inside a container `<div>`, reached with a prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:header` | The centered row, when `container` is set. |

```blade
<tk:header container="lg" class="border-b" container:class="gap-4">
    <tk:brand />
</tk:header>
```

## Accessibility

A native `<header>`; placed at the top level of the page, outside `<main>` and `<article>`, browsers expose it as the `banner` landmark.

## Behavior notes

- A sticky header keeps the distance from the top at which it is rendered, measured again when the window or the page is resized. Placed first on the page, that is the top of the viewport.
- With `sticky`, the element has `wire:ignore.self`, so a Livewire update keeps the position it computed.
- The header takes its place from the grid set up by `tk:main` on their common parent. When a `tk:sidebar` comes right before it, the sidebar takes the full height and the header starts beside it.

## Related

- [Footer](footer.md): the bottom area of the same layout.
- [Aside](aside.md): the side column of the same layout.
- `tk:sidebar`: the navigation column, which can collapse and become a drawer on small screens.
- [Brand](brand.md): the logo and app name usually placed at the start of the header.
