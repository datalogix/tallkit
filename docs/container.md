# Container

A centered block with a maximum width and horizontal padding, for keeping page content in a readable column. Use it around the content of a page or a section; the header, footer and main areas of a layout take the same box through their own `container` prop.

```
tk:container
tk:container.wrapper
```

## Usage

```blade
<tk:container>
    <h1>Invoices</h1>
    <p>12 invoices, 3 overdue.</p>
</tk:container>
```

The content sits in a column up to `max-w-7xl` (1280px) wide, centered with automatic side margins, with `px-4` of padding on each side that grows to `px-6` from the `lg` breakpoint. On narrower screens it takes the full width.

### A narrower column

```blade
<tk:container size="md">
    <tk:card title="Profile">
        Name, email and password.
    </tk:card>
</tk:container>
```

`size="md"` caps the width at `max-w-4xl` (896px), which suits a form or an article. `full` removes the cap and keeps only the padding.

### Rendering from data

```blade
@foreach ($sections as $section)
    <tk:container :size="$section['size']" wire:key="section-{{ $section['id'] }}">
        <h2>{{ $section['title'] }}</h2>
    </tk:container>
@endforeach
```

Each section gets its own width. The container holds no state, so a Livewire update renders it again from the props.

### Optional container

```blade
<tk:container.wrapper :container="$boxed" class="py-4">
    <p>Content</p>
</tk:container.wrapper>
```

`tk:container.wrapper` renders a `tk:container` around its content only when `container` is set, and passes its attributes to it. With `$boxed` set to `'md'` the content gets the `md` container with `py-4`; with `false` or `null` the content renders alone, with no element around it. It is what `tk:header` and `tk:footer` use for their `container` prop.

## Props

### `tk:container`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `size` | string | `2xl` width | The maximum width: `xs` (`max-w-xl`), `sm` (`max-w-2xl`), `md` (`max-w-4xl`), `lg` (`max-w-5xl`), `xl` (`max-w-6xl`), `2xl` (`max-w-7xl`), `3xl` (`max-w-[96rem]`) or `full` (no maximum). Any other value gives `max-w-7xl`. |

### `tk:container.wrapper`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `container` | bool \| string | `null` | `true` wraps the content in a container of the default width; a string wraps it in a container of that `size`. Left out or `false`, the content renders without a container. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:container` | default | The content of the column. |
| `tk:container.wrapper` | default | The content, wrapped or not. |

## Customization

Classes and attributes go to the container's `<div>`. On `tk:container.wrapper` they go to the container it renders.

```blade
<tk:container size="lg" class="max-w-md px-0 lg:px-0 py-8">
    Content
</tk:container>
```

A `max-w-*` class wins over the width from `size`, so here the column is `max-w-md`. The padding classes replace the default ones.

## Behavior notes

- The width from `size` is applied with no specificity (`:where()`), so any `max-w-*` class on the tag wins over it, whatever the order of the classes.
- The default padding is `px-4` plus `lg:px-6`. A class replaces only the one of the same breakpoint: `class="px-8"` still has `px-6` from `lg` up, so pass both (`px-8 lg:px-8`) to change it at every width.
- A `tk:main` inside a container drops its own horizontal padding, so the two do not add up.
- The container has `data-tallkit-container`, which is what `tk:main` looks for.
- On `tk:container.wrapper`, `container="0"` counts as off, and when there is no container the attributes given to the wrapper are dropped along with it.

## Related

- [Card](card.md): a bordered box with padding and a header, for grouping content rather than setting the page width.
- `tk:header`, `tk:footer`: their `container` prop puts the content in a container inside the full-width bar, so the background spans the page while the content keeps the column width.
- `tk:main`: its `container` prop applies the same maximum width to the main area itself.
