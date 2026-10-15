# Aside

A secondary column next to the main content, for what complements the page without being part of it: a table of contents, related links, a summary. Use it as a sibling of `tk:main`; for the application's navigation column use `tk:sidebar`.

## Usage

```blade
<div class="min-h-screen">
    <tk:header>Acme</tk:header>

    <tk:main>
        Article content
    </tk:main>

    <tk:aside aria-label="Related">
        Related articles
    </tk:aside>
</div>
```

The element that holds `tk:main` becomes a grid, and the aside takes the column to the right of the main content, under the header, 16rem wide with a border on the side facing the content. Below the `lg` breakpoint it moves under the main content, at full width, with the border on top.

### Sticky

```blade
<div class="min-h-screen">
    <tk:header sticky>Acme</tk:header>

    <tk:main>
        Article content
    </tk:main>

    <tk:aside sticky aria-label="On this page">
        <a href="#install">Install</a>
        <a href="#usage">Usage</a>
    </tk:aside>
</div>
```

The aside stays in view while the page scrolls. It is never taller than the space left in the viewport, so content that does not fit scrolls inside the aside. Below `lg` it is not sticky and grows with its content.

### Inside a layout

```blade
<tk:layout.sidebar aside:aria-label="Related" aside:class="w-80">
    Page content

    <x-slot:aside>
        Related articles
    </x-slot:aside>
</tk:layout.sidebar>
```

`tk:layout.sidebar` renders a sticky `tk:aside` when its `aside` slot has content, and no aside at all when it is empty. Attributes prefixed with `aside:` go to it.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `sticky` | bool | `false` | Keeps the aside in view while the page scrolls, from the `lg` breakpoint up, with its own scroll when the content is taller than the viewport. |
| `ariaLabel` | string | none | The accessible name of the landmark, translated. |

## Slots

| Slot | Description |
|------|-------------|
| default | The content of the aside. |

## Customization

Classes and attributes go to the `<aside>` element. The default width (`w-64`) gives way to any width class passed; below `lg` the aside is always as wide as the page.

```blade
<div class="min-h-screen">
    <tk:main>
        Article content
    </tk:main>

    <tk:aside class="w-80 bg-zinc-50 dark:bg-zinc-900">
        Related articles
    </tk:aside>
</div>
```

## Accessibility

Renders an `<aside>` element, which is a `complementary` landmark. Give it an `ariaLabel` when the page has more than one, so they can be told apart. It adds no keyboard interaction of its own: the content is reached in the normal tab order, and focusing something out of sight in a sticky aside scrolls the aside to it.

## Behavior notes

- The grid placement depends on the parent: the aside only goes to the side column when it is a direct child of the same element as `tk:main`. Anywhere else it renders in the normal flow, as a bordered box.
- A sticky aside sticks at the distance from the top it has when the page loads, which is the height of the header above it. With a header that is not sticky, that gap stays empty above the aside once the header scrolls away; pair it with `<tk:header sticky>` to keep the space filled.
- The sticky offset and the maximum height are recalculated when the window or the page content is resized.
- The position is set by the script as an inline style, and it survives Livewire updates.
- In a right-to-left page the border moves to the other side of the aside.

## Related

- `tk:sidebar`: the navigation column on the other side of the page, which can collapse and turn into a drawer on small screens.
- `tk:main`: the main content the aside sits next to.
- `tk:layout.sidebar`: a full page layout that already places a sticky aside from its `aside` slot.
