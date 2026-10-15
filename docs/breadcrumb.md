# Breadcrumb

A trail of links showing where the current page sits in the hierarchy of the site, from the top level down to the page itself. Use it on pages more than one level deep, so the user can go back up.

```
tk:breadcrumb
└── tk:breadcrumb.item
    └── tk:breadcrumb.separator
```

Each item renders its own separator; `tk:breadcrumb.separator` is only written to place one by hand.

## Usage

```blade
<tk:breadcrumb>
    <tk:breadcrumb.item href="/">Home</tk:breadcrumb.item>
    <tk:breadcrumb.item href="/orders">Orders</tk:breadcrumb.item>
    <tk:breadcrumb.item aria-current="page">Order #1042</tk:breadcrumb.item>
</tk:breadcrumb>
```

Three items in a row with a chevron between them. Home and Orders are links, slightly dimmed until hovered; the last one is plain text in a medium weight, announced as the current page.

### From an array

```blade
<tk:breadcrumb :items="[
    ['label' => 'Home', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Orders', 'href' => '/orders'],
    'Order #1042',
]" />
```

Each entry is a label, or an array with the props and attributes of a `tk:breadcrumb.item`. The last entry is marked as the current page for you.

### Rendering from data

```blade
<tk:breadcrumb>
    @foreach ($crumbs as $crumb)
        <tk:breadcrumb.item
            :href="$loop->last ? null : $crumb['url']"
            :aria-current="$loop->last ? 'page' : null"
        >
            {{ $crumb['title'] }}
        </tk:breadcrumb.item>
    @endforeach
</tk:breadcrumb>
```

```php
public array $crumbs = [
    ['title' => 'Home', 'url' => '/'],
    ['title' => 'Orders', 'url' => '/orders'],
    ['title' => 'Order #1042', 'url' => '/orders/1042'],
];
```

Every crumb but the last is a link; the last is text marked as the current page. When the data already has the shape of the items (`label`, `href`), pass it to `items` instead of looping.

### Separator

```blade
<tk:breadcrumb separator="slash">
    <tk:breadcrumb.item href="/">Home</tk:breadcrumb.item>
    <tk:breadcrumb.item href="/docs">
        Docs
        <x-slot:separator>·</x-slot:separator>
    </tk:breadcrumb.item>
    <tk:breadcrumb.item href="/docs/install">Install</tk:breadcrumb.item>
</tk:breadcrumb>
```

`separator` takes an icon name and applies to every item. An item can have its own, as an icon name or as a slot with any content: here a slash follows Home and a dot follows Docs.

### Icons and custom content

```blade
<tk:breadcrumb>
    <tk:breadcrumb.item href="/" icon="home" aria-label="Home" />
    <tk:breadcrumb.item href="/orders" label="Orders" badge="12" />
    <tk:breadcrumb.item>
        <a href="/orders/1042" class="underline">Order #1042</a>
    </tk:breadcrumb.item>
</tk:breadcrumb>
```

The first item is a link with only an icon, the second has a badge after its label. The third has nothing but slot content, so that content is placed in the list item as it is, and the link is entirely yours.

## Props

### `tk:breadcrumb`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | `null` | The items to render. Each entry is a string, used as the label, or an array of `tk:breadcrumb.item` props and attributes (`label`, `href`, `icon`, `class`...). |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font size and the size of the icons and separators. |
| `scale` | string | `null` | `smallest`, `small`, `large` or `largest`. Moves the font size one or two steps down or up, leaving the icons as `size` sets them. |
| `separator` | string \| slot | `chevron-right` | The icon between the items. |

### `tk:breadcrumb.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The item text, translated. The default slot does the same without translating. |
| `current` | bool \| string | from the URL | Marks a link as the current page. `true` sets `aria-current="page"`, a string sets that value (`step`, `location`...), `false` never marks it. |
| `separator` | string \| slot | inherited | The icon after this item, or any content in its place. |
| `size` | string | inherited | Sets the size of this item's icon and separator. |

`size` and `separator` are inherited from the breadcrumb. Also accepts the [element props](shared-props.md#element-props) (`icon`, `iconTrailing`, `badge`, `suffix`...), and what makes an element a link or a button: `href`, `route`, `external`, `tooltip`, `action`...

### `tk:breadcrumb.separator`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string \| slot | `chevron-right` | The icon name, or any content. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:breadcrumb` | default | The items. They come after the ones from `items` when both are given. |
| `tk:breadcrumb` | `separator` | Content in place of the separator icon, for the items rendered from `items`. |
| `tk:breadcrumb.item` | default | The item content, in place of the `label` prop. |
| `tk:breadcrumb.item` | `label` | The item content, as markup. |
| `tk:breadcrumb.item` | `separator` | Content in place of the separator icon after this item. |

## Customization

Classes and attributes on `tk:breadcrumb` go to the `<nav>`. On `tk:breadcrumb.item` they go to the link (or the `<span>`, when the item has no `href`), not to the list item around it. To reach the other elements, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `list:` | `tk:breadcrumb` | The `<ol>`. |
| `item:` | `tk:breadcrumb` | Each item rendered from `items`, as props and attributes of `tk:breadcrumb.item`. |
| `container:` | `tk:breadcrumb.item` | The `<li>`. |
| `separator:` | `tk:breadcrumb.item` | The separator. |
| `icon:` | `tk:breadcrumb.item` | The icon. |
| `content:` | `tk:breadcrumb.item` | The element wrapping the text. |

The other prefixes of the element props work too (`icon-trailing:`, `suffix:`, `badge:`...), and `item:` can carry any of the item's prefixes: `item:container:class`, `item:separator:class`.

```blade
<tk:breadcrumb
    class="mb-4"
    list:class="gap-x-1"
    item:class="hover:underline"
    item:separator:class="text-zinc-400"
    :items="[['label' => 'Home', 'href' => '/'], ['label' => 'Orders', 'href' => '/orders'], 'Order #1042']"
/>
```

## Accessibility

Follows the WAI-ARIA breadcrumb pattern. The root is a `<nav>` labelled "Breadcrumb" (translated; pass `aria-label` to change it) holding an ordered list, and the current page carries `aria-current="page"`. The separators have `aria-hidden="true"`, and the one after the last item is not displayed.

There is no keyboard interaction beyond that of the links. An item without `href` is not in the tab order.

| Key | Action |
|-----|--------|
| Tab | Moves to the next link in the trail. |
| Enter | Follows the focused link. |

## Behavior notes

- With no `items` and an empty slot, the breadcrumb renders nothing, not even the `<nav>`.
- The last entry of `items` always gets `aria-current="page"`, link or not, even when items written in the slot come after it. Items written in the slot do not: a link is marked only when its `href` is exactly the current URL (a query string in the `href` has to match too), and a last item that is plain text needs `aria-current="page"` on it, as in the first example.
- `current` only acts on an item that is a link or a button. On an item without `href` it does nothing; use `aria-current` there.
- Inside Livewire, the link pointing to the page stays marked as current across updates.
- An item with slot content and no other prop or attribute (`separator`, `size` and `container:` attributes aside) puts the content straight into the `<li>`, with no wrapping element. A direct `<a>` or `<button>` in it still gets the dimmed look of the other links.
- `item:` attributes and the `separator` slot of the breadcrumb only reach the items rendered from `items`. The `separator` prop with an icon name reaches the items written in the slot as well.
- The default chevron and `slash` are mirrored in right-to-left pages. Any other icon, and separator content from a slot, is shown as it is.
- A long label is cut with an ellipsis when it does not fit, and the trail wraps onto more lines when there is no room for all the items.
- `content:class` on an item replaces the class that cuts the label instead of adding to it: include `truncate` in it to keep the ellipsis.
- The values of an `items` entry are escaped: HTML in a label shows as text. For markup in an item, write it in the slot.
- When the slot content starts with a `<nav>` of your own, it is rendered as it is: the breadcrumb adds no wrapper and its props and attributes are ignored.

## Related

- `tk:nav`: a set of links to the sections of the site, not a path to the current page.
- `tk:pagination`: moves between the pages of one list.
- `tk:stepper`: the steps of a process the user goes through in order.
