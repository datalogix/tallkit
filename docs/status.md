# Status

A stat tile: a large value with a title and a description under it, on a soft colored background, for dashboards ("Revenue R$ 12k, +8% this month"). `tk:status.group` lays several of them out in a row that wraps.

```
tk:status.group
└── tk:status ...
```

## Usage

```blade
<tk:status value="R$ 12.480" title="Revenue" description="+8% this month" color="green" />
```

A rounded tile with a light green background, "R$ 12.480" large in the middle, "Revenue" under it and "+8% this month" in muted text.

### A row from data

```blade
<tk:status.group :items="[
    ['title' => 'Revenue', 'value' => $revenue, 'color' => 'green'],
    ['title' => 'Orders', 'value' => $orders, 'href' => route('orders.index'), 'icon' => 'cart'],
    ['title' => 'Refunds', 'value' => $refunds, 'color' => 'red'],
]" />
```

Three tiles in a row, each at least 10rem wide, wrapping on narrow screens. The Orders tile is a link with a cart icon and darkens on hover.

### With extra content

```blade
<tk:status value="99.98%" title="Uptime">
    <tk:badge color="green" label="All systems normal" />
</tk:status>
```

The slot goes under the texts.

## Props

### `tk:status`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string \| int \| slot | `null` | The large value. |
| `title` | string \| slot | `null` | The text under the value, translated. |
| `description` | string \| slot | `null` | Muted text under the title. |
| `icon` | string \| slot | `null` | An icon before the texts. |
| `color` | string | gray | A color name for the background and the value (`green`, `red`, `blue`...). |
| `href` | string | `null` | Makes the tile a link. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the value size and the padding. Inherited from the group. |
| `prepend`, `append`, `actions` | slot | `null` | Content before the value, after the texts, and actions. |

### `tk:status.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `null` | The tiles, as arrays of `tk:status` props. |
| `size` | string | `md` | Passed to every tile; also sets the gap. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:status` | default | Content under the texts. |
| `tk:status.group` | default | Tiles written by hand, after those from `items`. |

## Customization

Classes and attributes on `tk:status` go to the texts block; the tile itself and the value take prefixes:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:status` | The tile (`container:class="min-h-32"`). |
| `value:` | `tk:status` | The large value. |

```blade
<tk:status value="342" title="Orders" container:class="border" value:class="tabular-nums" />
```

## Accessibility

The value and texts are read in order, as plain text; a tile with `href` is a link whose name is its whole text. The value is not a heading: put a heading above the group when the dashboard needs one.

## Related

- [Card](card.md): a box for any content.
- [Badge](badge.md): a small label for a state.
- [Chart](chart.md): the trend behind the numbers.
