# Transition

A `<div>` with ready-made Alpine enter and leave transitions, for content shown with `x-show`: fade, zoom, slide in any direction. The popovers, menus and dropdowns use it for their `animation`.

## Usage

```blade
<div x-data="{ open: false }">
    <tk:button x-on:click="open = !open" label="Details" />

    <tk:transition animation="fade" x-show="open">
        <p>More information.</p>
    </tk:transition>
</div>
```

The paragraph fades in over half a second when the button is clicked, and fades out faster when it is clicked again.

### Sliding

```blade
<tk:transition animation="slide-down" x-show="open" class="fixed inset-x-0 top-0">
    <tk:alert message="Connection lost." type="warning" />
</tk:transition>
```

The alert slides down from above the screen and back up when it leaves.

## Props

### `tk:transition`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `animation` | string | scale | `fade`; `zoom`; `slide`, which comes from the side given by the `data-position` of an ancestor `group`; `slide-up`, `slide-down`, `slide-left`, `slide-right` (a short distance) and the same with `-full` (the whole height or width); `none` for no transition. By default it grows from half its size. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:transition` | default | The content. |

## Customization

Classes and attributes, `x-show` included, go to the `<div>`.

## Accessibility

Only the appearance changes; showing and hiding is up to `x-show`. Tailwind's `motion-reduce:` variants are not added, so for large movements consider `animation="fade"` for users who prefer reduced motion.

## Related

- [Popover](popover.md), [Menu](menu.md), [Dropdown](dropdown.md): take an `animation` passed to this tag.
