# Brand

The logo and name of the app, linked to the home page. It goes at the top of a sidebar, a header or a sign-in screen; the layouts of this package already render one.

## Usage

```blade
<tk:brand />
```

With no props it looks for `logo.svg` (or `.webp`, `.png`, `.jpg`, `.jpeg`) in `public/`, `public/imgs/` or `public/images/` and shows it, linked to the `home` route. When there is no such file it shows the name of the app, from `config('app.name')`.

### Logo and name

```blade
<tk:brand name="Acme" />
```

The logo with "Acme" next to it. `:name="true"` uses the name of the app.

### A logo for dark mode

```blade
<tk:brand logo="/imgs/acme.svg" logo-dark="/imgs/acme-white.svg" name="Acme" />
```

The first image shows in light mode and the second in dark mode. A `logo-dark` file in the same folders as `logo` is picked up without the prop.

### Inline markup as the logo

```blade
<tk:brand name="Acme">
    <svg viewBox="0 0 24 24" class="size-8 fill-current"><circle cx="12" cy="12" r="10" /></svg>
</tk:brand>
```

The default slot takes the place of the image, so an inline SVG can follow the text color. With content in the slot the logo files are not looked up.

### Not a link

```blade
<tk:brand :href="false" name="Acme" />
```

Renders a `<div>` instead of an `<a>`, for a page where the brand should not lead anywhere.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `logo` | string \| bool \| slot | the `logo` file in `public/` | The image URL. `false` shows no logo. |
| `logoDark` | string \| slot | the `logo-dark` file in `public/` | The image for dark mode. With it, `logo` shows only in light mode. |
| `name` | string \| bool \| slot | `null` | The text next to the logo. `true` uses `config('app.name')`, which is also what shows when there is no logo at all. |
| `alt` | string | `config('app.name')`, or empty with a `name` | The alternative text of the images. |
| `href` | string \| bool | the `home` route, or `/` | Where the brand links to. `false` renders it without a link. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the height of the logo, the size of the name and the spacing. |

Also accepts the [element props](shared-props.md#element-props) (`suffix`, `badge`...), which render after the name, and the link attributes of an element (`external`, `navigate`, `target`...).

## Slots

| Slot | Description |
|------|-------------|
| default | Markup for the logo, in place of the image. |
| `logo` | The same, but as its own box: its attributes (`class`...) go to the wrapper, which has no fixed height. |
| `logoDark` | Markup for the logo in dark mode. |
| `name` | The name, in place of the `name` prop. |

## Customization

Classes and attributes on the tag go to the root element, the link. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `logo:` | The box around the logo. |
| `image:` | The `<img>` of `logo`. |
| `image-dark:` | The `<img>` of `logoDark`. |
| `name:` | The name. |

```blade
<tk:brand name="Acme" logo:class="h-12" image:class="rounded-full" name:class="uppercase tracking-wide" />
```

## Accessibility

The brand is a single link. Its accessible name comes from what it shows:

- with a `name`, the text is the name and the images get an empty `alt`, so it is not read twice;
- with only an image, the `alt` is the name of the app;
- with only markup in a slot, the link gets `aria-label` with the name of the app.

Pass `alt` or `aria-label` to say something else. The name is a `<span>`, not a heading.

## Behavior notes

- The link uses `wire:navigate`, except with `external` or a `target`.
- `logo` and `logoDark` given as props win over the files; the files are only searched for when the prop is missing and the default slot is empty.
- With only `logoDark` (and `:logo="false"`), nothing shows in light mode. Give both, or use the slot for the light version.
- A long name is cut with an ellipsis when the brand has no room.
- The layouts reach their brand with a prefix (`brand:` or `sidebar-brand:`), and the header and sidebar layouts fill its default slot from their own `brand` slot.
