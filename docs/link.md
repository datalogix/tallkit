# Link

A text link in the body text style, underlined, that navigates with `wire:navigate` and marks itself when it points to the current page. Use it inside text and for secondary actions; for a link that looks like a button, use `tk:button` with `href`.

## Usage

```blade
Read the <tk:link href="/terms" label="terms of service" /> before signing up.
```

An `<a>` with the text underlined, which loses the underline on hover. Clicking it navigates with `wire:navigate`, without a full page load. While the current URL is `/terms` or a page under it, the link has `aria-current="page"`.

### External links

```blade
<tk:link href="https://laravel.com" external icon-trailing="open-in-new">Laravel</tk:link>
```

Opens in a new tab, without `wire:navigate`, with an icon after the text. `target="_blank"` on the tag does the same, and also adds `rel="noopener noreferrer"` when there is no `rel`.

### Style

```blade
<tk:link href="/help" label="Help" :underline="false" size="sm" variant="subtle" />
<tk:link route="orders.show" :parameters="$order" label="View order" color="blue" />
```

The first has no underline until hovered, in smaller, muted text. The second builds the URL from a route and is blue.

## Props

### `tk:link`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `underline` | bool | `true` | `true` underlines the link and removes the underline on hover; `false` does the opposite. |

Also accepts the props of `tk:text` (`size`, `scale`, `weight`, `variant`, `color`), the [element props](shared-props.md#element-props) (`label`, `icon`, `iconTrailing`, `badge`...), and what makes an element a link: `href`, `route`, `parameters`, `external`, `navigate`, `current`, `exact`. See [Element](element.md#props).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:link` | default | The link text, in place of `label`. |

## Customization

Classes and attributes go to the `<a>` element; the parts take the prefixes of the element props (`icon:`, `icon-trailing:`...).

```blade
<tk:link href="/changelog" label="What's new" class="font-medium" icon-trailing="arrow-right" icon-trailing:class="size-4" />
```

## Accessibility

A native `<a>` with `href`, so it is reached with Tab and opened with Enter. The link to the current page gets `aria-current="page"`. A disabled link (`disabled`) has no `href`, `role="link"` and `aria-disabled="true"`.

## Behavior notes

- Without `href` or `route`, it renders a `<p>` with the link style and nothing to click.
- Links to a file (`.pdf`, `.zip`...) and external links skip `wire:navigate`.

## Related

- [Button](button.md): `tk:button href="..."` for a link that looks like a button.
- [Element](element.md): the link props in detail.
- `tk:text`: body text, which this tag is built on.
