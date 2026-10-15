# Sidebar

The navigation column of an app layout, on the start side of the page. On large screens it is always there; with `stashable`, below the `lg` breakpoint it hides off-screen and slides in as a drawer over a dimmed page, opened by a `tk:sidebar.toggle` button.

```
tk:sidebar
├── tk:sidebar.toggle (close button, optional)
└── content

tk:sidebar.toggle (open button, in the header)
```

## Usage

```blade
<div class="min-h-screen">
    <tk:sidebar sticky stashable>
        <tk:sidebar.toggle icon="close" class="lg:hidden" />
        <tk:brand />
        <tk:nav list :items="$menu" />
    </tk:sidebar>

    <tk:header>
        <tk:sidebar.toggle class="lg:hidden" />
    </tk:header>

    <tk:main>...</tk:main>
</div>
```

On large screens, a 16rem gray column on the left with the brand and the menu, staying in view while the page scrolls. On small screens the column is hidden and the header shows a menu button; tapping it slides the sidebar in over a dark backdrop and moves the focus into it. The close button, the backdrop, or Escape slide it out and return the focus to the menu button.

### Two sidebars

```blade
<tk:sidebar name="filters" stashable aria-label="Filters">...</tk:sidebar>

<tk:sidebar.toggle name="filters" icon="filter" label="Filters" />
```

Toggles and sidebars are paired by `name`, so a page can have more than one drawer. Without a name, they pair with the unnamed sidebar.

### From your own code

```blade
<button x-on:click="$dispatch('tallkit:sidebar-toggle', { name: 'filters' })">Filters</button>
```

`tallkit:sidebar-toggle` and `tallkit:sidebar-close` events dispatched with the sidebar's `name` open or close it. Each change dispatches `tallkit:sidebar-state` on `window` with `{ name, opened }`.

## Props

### `tk:sidebar`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | Pairs the sidebar with toggles of the same name. |
| `sticky` | bool | `false` | Keeps the sidebar in view while the page scrolls, scrolling inside it when its content is taller than the viewport. |
| `stashable` | bool | `false` | Below `lg`, hides the sidebar and opens it as a drawer. |
| `ariaLabel` | string | `null` | Names the sidebar as a `region` landmark, translated. |

### `tk:sidebar.toggle`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The sidebar it opens and closes. |

A [`tk:button`](button.md) with a menu icon and a "Toggle sidebar" tooltip; takes the [element props](shared-props.md#element-props) (`icon`, `label`...) and the button props (`variant`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:sidebar` | default | The sidebar content. |
| `tk:sidebar.toggle` | default | The button content, in place of `label`. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `window` | `tallkit:sidebar-state` | `{ name, opened }` | A stashable sidebar opened or closed. |
| `tk:sidebar` | `opened`, `closed` | | The drawer opened or closed. Dispatched on the sidebar; do not bubble. |

## Customization

Classes and attributes on `tk:sidebar` go to the column. The backdrop takes a prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `backdrop:` | `tk:sidebar` | The dark backdrop behind the drawer. |

```blade
<tk:sidebar stashable class="w-72 bg-white" backdrop:class="bg-black/70">...</tk:sidebar>
```

The default width (`w-64`) and background give way to classes passed on the tag.

## Accessibility

The toggle has `aria-controls` pointing to the sidebar and an `aria-expanded` that follows it. While the drawer is closed on a small screen the sidebar is `inert`, so its links are out of the tab order. Opening it moves the focus to its first focusable element; closing it returns the focus to where it was.

| Key | Action |
|-----|--------|
| Escape | Closes the drawer. |

## Behavior notes

- The sidebar takes the `sidebar` area of the grid that `tk:main` builds on its parent; placed right before a `tk:header`, it runs the full height of the page beside it.
- The sidebar has `wire:ignore.self`: a Livewire update keeps the drawer open or closed.
- Until the script starts, a stashable sidebar stays hidden on small screens, so it does not flash open on load.

## Related

- [Layout](layout.md): `tk:layout.sidebar`, a complete layout with the sidebar in place.
- [Nav](nav.md): the menu usually inside it.
- [Aside](aside.md): a secondary column on the other side of the content.
