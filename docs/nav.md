# Nav

A navigation menu of links, in a row for a header or in a column for a sidebar, with the current page highlighted by an indicator that slides from item to item. Items can be grouped under headings, collapsed in expandable groups, or opened as a dropdown.

```
tk:nav
├── tk:nav.item
├── tk:nav.group
│   └── tk:nav.item ...
└── tk:nav.menu
    └── tk:menu.item ...
```

## Usage

```blade
<tk:nav label="Main menu" :items="[
    ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'view-dashboard'],
    ['label' => 'Orders', 'href' => '/orders', 'icon' => 'cart', 'badge' => 3],
    ['label' => 'Customers', 'href' => '/customers', 'icon' => 'account-group'],
]" />
```

Three links in a row, each with its icon; Orders has a "3" badge. The link to the current page (or a page under it) has `aria-current="page"` and a light background that slides to the clicked link when the user navigates with `wire:navigate`.

### In a sidebar

```blade
<tk:nav list label="Main menu">
    <tk:nav.item href="/dashboard" icon="view-dashboard" label="Dashboard" />

    <tk:nav.group label="Sales">
        <tk:nav.item href="/orders" label="Orders" />
        <tk:nav.item href="/invoices" label="Invoices" />
    </tk:nav.group>

    <tk:nav.group label="Settings" expandable :expanded="request()->is('settings/*')">
        <tk:nav.item href="/settings/profile" label="Profile" />
        <tk:nav.item href="/settings/team" label="Team" />
    </tk:nav.group>
</tk:nav>
```

A vertical list. "Sales" is a muted heading over its links. "Settings" is a button with a chevron that shows and hides its links, indented with a guide line, open only on the settings pages.

### Indicator styles

```blade
<tk:nav indicator="line-bottom" :items="$tabs" />
<tk:nav list indicator="line-left" :items="$links" />
<tk:nav :items="$links" :indicator="false" />
```

A line under the current item, as in tabs; a line beside it in a vertical list; and no sliding indicator, only a background on the current item.

### A dropdown inside the nav

```blade
<tk:nav>
    <tk:nav.item href="/" label="Home" />

    <tk:nav.menu label="Products" :items="[
        ['label' => 'Analytics', 'href' => '/products/analytics'],
        ['label' => 'Automation', 'href' => '/products/automation'],
    ]" />
</tk:nav>
```

"Products" looks like the other items, with a chevron, and opens a [`tk:menu`](menu.md) with its links.

## Props

### `tk:nav`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | `null` | The items, rendered before the slot. Each is an array of `tk:nav.item` props and attributes. |
| `label` | string | `null` | The accessible name of the `<nav>`, translated. Give one when the page has more than one nav. |
| `list` | bool | `false` | Lays the items out in a column, for sidebars. |
| `indicator` | string \| false | `bg` | The highlight of the current item: `bg` (a sliding background), `line-top` or `line-bottom` (in a row), `line-left` or `line-right` (in a list). `false` highlights with a fixed background, without animation. |
| `variant` | string | `null` | `accent` colors the current item with the accent color. |
| `scrollable` | bool | `false` | In a row, scrolls horizontally when the items do not fit. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Inherited by the items. |

### `tk:nav.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The link text, translated. |
| `square` | bool | auto | Equal padding on all sides, for an icon-only item. On by default when there is no label. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `iconDot`...) and the link props of [Element](element.md#props) (`href`, `route`, `current`, `exact`...). `size`, `variant` and `indicator` are inherited from the nav.

### `tk:nav.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | `null` | The heading of the group. Without it, the group only spaces its items. |
| `expandable` | bool | `false` | Turns the heading into a button that shows and hides the items. Needs a `label`. |
| `expanded` | bool | `true` | With `expandable`, whether the group starts open. |
| `line` | bool | `true` | With `expandable`, indents the items with a guide line. |
| `collapse` | bool \| string | `false` | With `expandable`, animates opening and closing; a string is passed as `x-collapse` modifiers. |

### `tk:nav.menu`

Takes the props of `tk:nav.item` for its button, and `items`, `keepOpen` and `animation` for the [`tk:menu`](menu.md) it opens. `animate="false"` keeps the chevron still.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:nav` | default | The items, after those from `items`. |
| `tk:nav.item` | default | The link text, in place of `label`. |
| `tk:nav.group` | default | The items of the group. |
| `tk:nav.menu` | default | The menu items, after those from `items`. |

## Customization

Classes and attributes on `tk:nav` go to the `<nav>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `item:` | `tk:nav` | Each item rendered from `items`. |
| `indicator:` | `tk:nav` | The sliding indicator. |
| `trigger:`, `container:`, `line:` | `tk:nav.group` | The expand button, the element around the items, the guide line. |
| `heading:` | `tk:nav.group` | The heading of a group that is not expandable. |
| `dropdown:`, `menu:` | `tk:nav.menu` | The dropdown and the menu. |

```blade
<tk:nav list :items="$links" class="gap-1" item:class="rounded-full" indicator:class="bg-blue-500/10" />
```

## Accessibility

A native `<nav>` landmark, named by `label`, with real links. The current page's link has `aria-current="page"`. An expandable group's button has `aria-expanded` and `aria-controls`, and the indicator is decorative.

## Behavior notes

- The indicator is kept across `wire:navigate` page changes (it is `@persist`ed), so it slides from the old page's item to the new one. It does not animate when the user prefers reduced motion.
- An expandable group keeps the state the user left it in across Livewire updates.
- A slot that starts with a `<nav>` is rendered as it is, without a second `<nav>` around it.

## Related

- [Layout](layout.md): sidebar and header layouts with the nav already in place.
- [Breadcrumb](breadcrumb.md): the path to the current page.
- `tk:tab`: tabs that switch content on the same page instead of navigating.
- [Menu](menu.md): the dropdown `tk:nav.menu` opens.
