# Accordion

A stack of collapsible sections. Each item has a header button that shows or hides its panel; with `exclusive`, opening one closes the others.

```
tk:accordion
└── tk:accordion.item
    ├── tk:accordion.heading
    └── tk:accordion.content
```

An item with a `label` renders its own heading and content, so the two inner tags are only written for a custom header.

## Usage

```blade
<tk:accordion>
    <tk:accordion.item label="Billing" expanded>
        Your invoices are issued on the first day of each month.
    </tk:accordion.item>

    <tk:accordion.item label="Shipping">
        Orders leave the warehouse within two business days.
    </tk:accordion.item>
</tk:accordion>
```

Billing starts open and Shipping closed. Each header toggles its own panel, and both can be open at once.

### One open at a time, bound to Livewire

```blade
<tk:accordion exclusive wire:model="section">
    <tk:accordion.item name="billing" label="Billing">...</tk:accordion.item>
    <tk:accordion.item name="shipping" label="Shipping">...</tk:accordion.item>
</tk:accordion>
```

```php
public ?string $section = null;
```

Opening Shipping closes Billing and sets `$section` to `'shipping'`; closing it sets `null`. Setting the property on the server opens that item. Without `exclusive` the property is an array of the open names, e.g. `['billing', 'shipping']`.

### Rendering from data

```blade
<tk:accordion>
    @foreach ($faqs as $faq)
        <tk:accordion.item :label="$faq->question" wire:key="faq-{{ $faq->id }}">
            {{ $faq->answer }}
        </tk:accordion.item>
    @endforeach
</tk:accordion>
```

### Custom header

```blade
<tk:accordion border size="lg">
    <tk:accordion.item>
        <tk:accordion.heading>
            Billing <tk:badge>3</tk:badge>
        </tk:accordion.heading>

        <tk:accordion.content>
            Your invoices are issued on the first day of each month.
        </tk:accordion.content>
    </tk:accordion.item>
</tk:accordion>
```

Without `label`, the item renders its slot as is: the heading takes any markup, here a badge next to the title, and the whole header stays clickable.

## Props

### `tk:accordion`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `exclusive` | bool | `false` | Only one item stays open; opening another closes it. |
| `collapse` | bool \| string | `true` | Animates opening and closing. `false` turns the animation off; a string is passed as `x-collapse` modifiers (e.g. `duration.500ms`). |
| `reversed` | bool | `false` | Puts the chevron before the label instead of after it. |
| `size` | string | `null` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets padding, font size and corner radius. |
| `border` | bool \| string | `null` | Draws a border around the accordion and pads the items. `true`, `solid`, `dashed` or `dotted`. |
| `disabled` | bool | `false` | Disables every header. |
| `level` | int \| false | `2` | Heading level announced for each header (`aria-level`). `false` drops the heading wrapper. |
| `region` | bool | `true` | `false` drops `role="region"` from the panels. |

`wire:model` and `x-model` bind the open item names: one name with `exclusive`, an array of names otherwise.

### `tk:accordion.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | Header text. When given, the item renders its own heading and content, and the default slot is the panel body. Without it, the default slot is rendered as is. |
| `name` | string | `null` | Identifies the item in the accordion's `wire:model` / `x-model` value. |
| `expanded` | bool | `false` | Opens the item on the first render. |
| `disabled` | bool | `false` | Disables this item's header. |

`size`, `collapse`, `reversed`, `border`, `level` and `region` are inherited from the accordion and can be set per item.

### `tk:accordion.heading` and `tk:accordion.content`

Used inside an item without `label`. The heading takes `label` (or its slot), `size`, `collapse`, `reversed`, `border`, `disabled` and `level`; the content takes `size`, `collapse`, `border` and `region`. All are inherited from the item and the accordion.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:accordion` | default | The items. |
| `tk:accordion.item` | default | The panel body, or the heading and content when `label` is not given. |
| `tk:accordion.heading` | default | The header content, in place of `label`. |
| `tk:accordion.content` | default | The panel body. |

## Events

Events are dispatched on the component's own element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:accordion.item` | `opened` | | The item was opened. |
| `tk:accordion.item` | `closed` | | The item was closed. |
| `tk:accordion` | `changed` | `{ items }`, the item elements | An item of this accordion opened or closed. |

With Alpine:

```blade
<tk:accordion @changed="count = $event.detail.items.filter((item) => item.hasAttribute('data-open')).length">
    <tk:accordion.item label="Billing">...</tk:accordion.item>
</tk:accordion>
```

Calling a Livewire action:

```blade
<tk:accordion>
    <tk:accordion.item label="Billing" @opened="$wire.loadInvoices()">...</tk:accordion.item>
</tk:accordion>
```

## Customization

Classes and attributes on a tag go to its root element. To reach an inner element of an item, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `trigger:` | `tk:accordion.item` | The header button. |
| `content:` | `tk:accordion.item` | The panel. |

```blade
<tk:accordion class="max-w-xl">
    <tk:accordion.item label="Billing" trigger:class="font-semibold" content:class="text-zinc-500">
        Your invoices are issued on the first day of each month.
    </tk:accordion.item>
</tk:accordion>
```

## Accessibility

Follows the WAI-ARIA accordion pattern. Each header is a `<button>` inside an element with `role="heading"` and `aria-level`, carrying `aria-expanded` and `aria-controls` pointing to its panel. Each panel has `role="region"` labelled by its header; set `:region="false"` above six items, where that many landmarks become noise for screen readers.

| Key | Action |
|-----|--------|
| Enter, Space | Opens or closes the focused header's panel. |
| Arrow Down / Arrow Up | Moves focus to the next / previous header, wrapping around. |
| Home / End | Moves focus to the first / last header. |
| Tab | Moves through the headers and the focusable content of open panels. |

Disabled headers are skipped by the arrow keys, Home and End.

## Behavior notes

- `expanded` only sets the initial state: after the first render the browser owns it, so a Livewire update never closes an item the user opened. To drive it from the server, give each item a `name` and bind the accordion with `wire:model` / `x-model`.
- When the accordion is bound with `wire:model` / `x-model`, the bound value decides which items are open and `expanded` is ignored.
- A value coming from `wire:model` / `x-model` on page load is applied without animation and without firing `opened` / `changed`.
- With `exclusive`, if several items are marked `expanded`, only the first stays open.
- A nested accordion inherits the props set on the item that holds it (`disabled`, `reversed`, `border`, `level`, `collapse`, `region`, `size`). To reset one, pass it on the nested accordion (e.g. `:disabled="false"`).

## Related

- `tk:tab`: use tabs when the sections are peers and only one should be visible, laid out side by side.
