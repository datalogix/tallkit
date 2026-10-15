# Layout

Ready-made page layouts that put the shell pieces together: a sidebar app, a top-navigation app, the sign-in pages and the error pages. Each takes the page content in its slot and the menus as props, so a layout view only fills in the parts.

```
tk:layout.sidebar
tk:layout.header
tk:layout.auth
tk:layout.error
```

`tk:layout.sidebar` and `tk:layout.header` are made of [`tk:header`](header.md), `tk:sidebar`, `tk:nav`, [`tk:brand`](brand.md), the [appearance menu](appearance.md) and `tk:main`; `tk:layout.auth` and `tk:layout.error` are centered single-column pages. Put them inside [`tk:html`](html.md).

## Usage

```blade
<tk:html>
    <tk:layout.sidebar
        :menu="[
            ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'view-dashboard'],
            ['label' => 'Orders', 'href' => '/orders', 'icon' => 'cart'],
        ]"
        :user-menu="[
            ['label' => 'Profile', 'href' => '/profile'],
            ['label' => 'Sign out', 'action' => 'logout'],
        ]"
    >
        {{ $slot }}
    </tk:layout.sidebar>
</tk:html>
```

A sidebar on the left with the brand at the top and the menu as a vertical list, the current page highlighted. A sticky header beside it ends with the account menu, which holds the light and dark mode selector and the user menu items. The page content sits in a centered column. On small screens the sidebar becomes a drawer, opened by a menu button in the header.

### Top navigation

```blade
<tk:layout.header :menu="$menu" :user-menu="$userMenu" align="center">
    {{ $slot }}
</tk:layout.header>
```

The brand, the menu as horizontal links and the account menu in one header bar; `align="center"` centers the links between them. On small screens the links move to a drawer, opened by a menu button.

### Header and sidebar slots

```blade
<tk:layout.sidebar :menu="$menu">
    <x-slot:brand>
        <tk:brand logo="/logo.svg" name="Acme" />
    </x-slot:brand>

    <x-slot:search>
        <tk:command.modal shortcut="ctrl.k" />
    </x-slot:search>

    <x-slot:sidebar>
        <tk:nav :items="$secondary" list class="mt-auto" />
    </x-slot:sidebar>

    {{ $slot }}
</tk:layout.sidebar>
```

`brand` replaces the default brand, `search` and `notification` go in the header before the account menu, and `sidebar` adds content under the menu in the sidebar.

### Sign-in pages

```blade
<tk:layout.auth>
    <tk:form wire:submit="login">
        <tk:input wire:model="email" />
        <tk:input wire:model="password" />
    </tk:form>
</tk:layout.auth>
```

The brand over the form, centered on a soft gradient, with a light and dark mode toggle in the corner. When images are found in `public/imgs/hero/` (or `public/images/hero/`, or a single `hero.webp`...), the page splits in two on large screens and one of them, kept for the session, fills the other half.

### Error pages

```blade
<tk:layout.error>
    <tk:heading level="1" label="Page not found" />
    <tk:button href="/" label="Back home" />
</tk:layout.error>
```

The brand and the content centered on the screen.

## Props

### `tk:layout.sidebar` and `tk:layout.header`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `menu` | array | `null` | The main menu items, as `tk:nav` takes them (`label`, `href`, `icon`, `badge`...). |
| `userMenu` | array | `null` | The items of the account menu, as `tk:menu` takes them. |
| `appearance` | bool \| string | auto | The theme control: the selector inside the account menu, or a toggle button when there is no menu. `toggle` forces the button, `selector` the row, `false` none. See [Appearance](appearance.md). |

`tk:layout.header` also takes:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `align` | string | `left` | Where the menu sits in the header: `left` (after the brand), `center` or `right` (before the account menu). |

### `tk:layout.auth`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `bg` | string \| array | images in `public/` | The image (or images, one picked per session) for the second half of the page. |
| `right` | bool | `false` | Puts the form on the right and the image on the left. |
| `appearance` | bool | `true` | `false` removes the light and dark mode toggle. |

`tk:layout.error` has no props of its own.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| all | default | The page content. |
| `tk:layout.sidebar`, `tk:layout.header` | `brand` | Content of the brand, in place of the app logo and name. |
| `tk:layout.sidebar`, `tk:layout.header` | `nav` | Menu items written as tags, added to `menu`. |
| `tk:layout.sidebar`, `tk:layout.header` | `header` | Content at the top of the sidebar (and, in `tk:layout.header`, in place of the menu in the header). |
| `tk:layout.sidebar`, `tk:layout.header` | `sidebar` | Content at the bottom of the sidebar. |
| `tk:layout.sidebar`, `tk:layout.header` | `prepend`, `append` | Content at the start of the header, and before the account menu. |
| `tk:layout.sidebar`, `tk:layout.header` | `search`, `notification` | Content before the account menu, after `append`. |
| `tk:layout.sidebar`, `tk:layout.header` | `avatarMenu` | Items added to the account menu. |
| `tk:layout.sidebar` | `aside` | A sticky side column on the right of the content. |
| `tk:layout.auth` | `hero` | Content over the image half. |
| `tk:layout.auth`, `tk:layout.error` | `brand` | Content in place of the brand. |

## Customization

Classes and attributes on the tag go to the outer element. Each piece takes attributes through its prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `header:` | `tk:layout.sidebar`, `tk:layout.header` | The header. |
| `sidebar:` | `tk:layout.sidebar`, `tk:layout.header` | The sidebar. |
| `sidebar-brand:`, `sidebar-menu:` | `tk:layout.sidebar`, `tk:layout.header` | The brand and the menu inside the sidebar. |
| `sidebar-open:`, `sidebar-close:` | `tk:layout.sidebar`, `tk:layout.header` | The buttons that open and close the drawer. |
| `brand:`, `menu:` | `tk:layout.header` | The brand and the menu in the header. |
| `area:` | `tk:layout.header`, `tk:layout.auth` | The row inside the header; the form half of the auth page. |
| `spacer:` | `tk:layout.sidebar`, `tk:layout.header` | The flexible space in the header. |
| `appearance:` | all but `tk:layout.error` | The account menu, or the theme toggle on the auth page. |
| `main:` | `tk:layout.sidebar`, `tk:layout.header` | The main content area. |
| `aside:` | `tk:layout.sidebar` | The aside column. |
| `container:` | `tk:layout.auth`, `tk:layout.error` | The centered column. |
| `card:`, `hero:` | `tk:layout.auth` | The card around the content (without an image), the image half. |

```blade
<tk:layout.sidebar :menu="$menu" sidebar:class="w-72" header:variant="subtle" main:class="pb-16">
    {{ $slot }}
</tk:layout.sidebar>
```

## Accessibility

The pieces are native landmarks: `<header>`, the sidebar's `<nav>` regions labelled "Main menu", and `<main>`. The drawer buttons and the account menu have accessible names, and the drawer follows the sidebar's own keyboard handling.

## Behavior notes

- The list of hero images for `tk:layout.auth` is looked up once a day and cached; clear the cache after adding images.
- In `tk:layout.header`, the sidebar exists only for small screens: on large ones it is hidden and the menu is in the header.

## Related

- [Html](html.md): the page shell to put the layout in.
- [Header](header.md), [Aside](aside.md), [Footer](footer.md): the pieces, for a layout of your own.
- `tk:sidebar`, `tk:nav`, `tk:main`: the sidebar, the menu and the content area.
