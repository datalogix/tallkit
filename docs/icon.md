# Icon

An SVG icon from any [Iconify](https://icon-sets.iconify.design) collection, rendered inline on the server, so it takes the current text color and needs no JavaScript or icon font. It also shows an image from a URL, or any content in the slot, at the same size.

## Usage

```blade
<tk:icon name="home" />
```

A 24px house icon in the color of the surrounding text. A name without a collection is looked up in the configured collections in order (`mdi` first), so `home` is `mdi:home`. The icon is decorative: hidden from screen readers.

### From a specific collection

```blade
<tk:icon name="ph:house" size="lg" />
<tk:icon name="heroicons:trash" class="text-red-500" />
```

`collection:name` picks the icon from that collection, as named on Iconify. Classes go to the `<svg>`, so color and size utilities apply directly.

### An icon that means something

```blade
<tk:icon name="trash-can" tooltip="Delete" />
<tk:icon name="star" aria-label="Favorite" />
```

The first shows "Delete" in a tooltip on hover and on keyboard focus (it becomes focusable for that), and the text is also its accessible name. The second is announced as an image named "Favorite". Give a name to any icon that conveys information not written next to it.

### An image or any content

```blade
<tk:icon name="https://example.com/logo.png" />
<tk:icon tooltip="Celebrate">🎉</tk:icon>
```

A URL is shown as an image, cropped to the icon size. Without a name, the slot is rendered in place of the icon.

## Props

### `tk:icon`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The icon: `name`, `collection:name`, or an image URL. |
| `icon` | string | `null` | Same as `name`, for when `name` is taken by another attribute. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the width and height. |
| `tooltip` | string \| slot | `null` | A tooltip, translated. On an icon with no `aria-label`, it is also the accessible name. |
| `image` | string | from `name` | An image URL, in place of a name. |
| `svg` | string | from `name` | SVG markup to render as is, in place of a looked-up icon. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:icon` | default | Content rendered in place of the icon when there is no `name`, `image` or `svg`. |

## Customization

Classes and attributes go to the `<svg>` (or the `<img>`). Tooltip options take the `tooltip:` prefix (`tooltip:position`, `tooltip:delay`...).

```blade
<tk:icon name="information" tooltip="More details" tooltip:position="right" class="size-5 text-blue-500" />
```

## Accessibility

An icon with no tooltip and no `aria-label` is decorative: `aria-hidden="true"` and `focusable="false"` on the `<svg>`, `alt=""` on an image. With a tooltip or an `aria-label` it has `role="img"` and an accessible name; with both, the `aria-label` names it and the tooltip describes it. An icon with a tooltip gets `tabindex="0"` and a focus outline, so keyboard users can open the tooltip.

## Behavior notes

- Icons are looked up once and kept: first in the icons the package ships with, then in `storage/app/tallkit/icons`, then on the Iconify API, whose answer is saved to that folder. Run `php artisan tallkit:icons` at deploy to fetch every icon the views use ahead of time, so no page waits on the API.
- A name that is not found renders nothing, and is not asked for again for a day (`tallkit.icon.missing_ttl`).
- The collections tried for a name without one are set in `config('tallkit.icon.collections')`.
- The SVG is sanitized before it is stored or rendered.

## Related

- [Element](element.md): the `icon` and `iconTrailing` props of buttons, badges and links render this tag.
- [Avatar](avatar.md): a round image, initials or icon for a person.
