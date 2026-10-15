# Card

A bordered box with a white background and a soft shadow that groups related content: a summary, a plan, a record, a block of a form. It can carry an image on top, a header with a title and actions, and a footer. For the same title-and-body layout without the box, use `tk:section`.

## Usage

```blade
<tk:card title="Invoices" subtitle="Last 30 days">
    12 invoices, 3 overdue.
</tk:card>
```

The title sits at the top in bold, the subtitle under it in muted text, and a thin line separates them from the body. Everything inside the card is padded.

### Image and footer

```blade
<tk:card image="/img/cabin.jpg" alt="Wooden cabin by a lake" title="Lakeside cabin" description="Sleeps four, two bedrooms.">
    From $120 per night.

    <x-slot:footer>
        <div class="border-t border-zinc-100 px-4 py-3">
            <tk:button variant="primary" href="/cabins/lakeside">Book</tk:button>
        </div>
    </x-slot:footer>
</tk:card>
```

The image covers the full width of the card at the top, with no padding around it, and the corners of the card clip it. The footer slot is rendered after the padded area, also edge to edge, so it brings its own padding and border.

### Header actions

```blade
<tk:card title="Team members" subtitle="Who can access this project">
    <x-slot:actions>
        <tk:button size="sm" icon="plus" wire:click="invite">Invite</tk:button>
    </x-slot:actions>

    <ul>
        <li>Ana</li>
        <li>Bruno</li>
    </ul>
</tk:card>
```

The `actions` slot is placed at the end of the header row, beside the title, and the separator is drawn under the whole row. Clicking Invite calls the Livewire action.

### Rendering from data

```blade
<div class="grid gap-4 sm:grid-cols-2">
    @foreach ($plans as $plan)
        <tk:card :title="$plan->name" :subtitle="$plan->features" wire:key="plan-{{ $plan->id }}">
            <x-slot:actions>
                <tk:badge>{{ $plan->price }}</tk:badge>
            </x-slot:actions>

            <tk:button variant="primary" wire:click="choose({{ $plan->id }})">Choose</tk:button>
        </tk:card>
    @endforeach
</div>
```

One card per plan. `features` is an array, so the subtitle becomes a bulleted list under the plan name, with the price at the end of the header row.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string \| slot | `null` | The title at the top, translated. |
| `subtitle` | string \| array \| slot | `null` | Muted text right under the title, translated. An array renders as a bulleted list. |
| `description` | string \| slot | `null` | A further paragraph under the subtitle, translated. |
| `prepend` | slot | `null` | Content above the title. Shows only when the header has something else (see the behavior notes). |
| `append` | slot | `null` | Content under the description, still in the header. |
| `actions` | slot | `null` | Content at the end of the header row, such as buttons or a badge. |
| `content` | string \| slot | `null` | Body content placed before the default slot. A string is translated. |
| `separator` | bool | automatic | `true` always draws the line between header and body; `false` never does. Left out, it is drawn when the header has a title with a subtitle, description or append, or has actions, and the card has a body. |
| `image` | string | `null` | The URL of an image shown at the top, full width. |
| `alt` | string | `''` | The image's alternative text, translated. Empty marks the image as decorative. |
| `header` | slot | `null` | Content above the padded area, under the image, edge to edge. |
| `footer` | slot | `null` | Content after the padded area, edge to edge. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font size, padding, spacing and corner radius, and is passed to the title, texts, icon and badge. |

`icon` and `badge` are not props of the card but are passed to the title: `icon="folder"` puts an icon before it and `badge="New"` a badge after it.

## Slots

| Slot | Description |
|------|-------------|
| default | The body, inside the padded area under the header. |
| `header` | Content between the image and the padded area. |
| `footer` | Content after the padded area. |
| `actions` | Content at the end of the header row. |
| `prepend`, `append` | Content above the title / under the description. |

`title`, `subtitle`, `description` and `content` also accept a slot in place of the prop.

## Customization

Classes and attributes go to the card's root `<div>`. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `image:` | The `<img>`. |
| `section:` | The padded `<section>` holding the header and the body. |
| `title:` | The title. It is a `<p>`; `title:level="2"` makes it an `<h2>`. |
| `icon:`, `badge:` | The title's icon and badge. |
| `subtitle:` | The subtitle text. |
| `list:` | The list, when `subtitle` is an array. |
| `container:` | The element wrapping prepend, title, subtitle, description and append. |
| `actions:` | The element wrapping the actions. |
| `separator:` | The line between header and body. |
| `content:` | The element wrapping the body. |

```blade
<tk:card class="max-w-sm" title="Orders" title:level="2" section:class="p-8" content:class="text-zinc-500">
    No orders yet.
</tk:card>
```

A class passed with a prefix wins over the card's default for the same property: here the padding becomes `p-8`.

## Accessibility

The card is a plain `<div>` with no role and no keyboard interaction; its content keeps the normal tab order. The title is a `<p>`: pass `title:level` to make it a real heading when the card is a section of the page. The image has an empty `alt` unless one is given, so screen readers skip it; give `alt` when the image carries information. The separator has `role="separator"`.

## Behavior notes

- The parts render in this order: image, `header` slot, the padded area (header row, separator, body), `footer` slot. Only the padded area has padding.
- `prepend` alone does not render the header: it shows only when there is also a title, subtitle, description, append or actions.
- A title alone does not draw the separator; add `subtitle`, `description`, `append` or `actions`, or pass `separator`.
- When a card or a table is placed directly in the body, the separator is hidden, unless `separator` is `true`.
- `content="0"` renders nothing.
- Inside Livewire the card has no state of its own: every update renders it again from the props.

## Related

- `tk:section`: the same header and body layout, without the box, padding and image.
- `tk:form.card`: a card wrapped in a form, with the session alert at the top of the body.
