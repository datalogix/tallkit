# Skeleton

Gray placeholders in the shape of content that is still loading: lines of text, boxes, avatars. They hold the layout while data arrives, so the page does not jump, and are announced to screen readers as a single "Loading...".

```
tk:skeleton.group
├── tk:skeleton.line
├── tk:skeleton.circle
├── tk:skeleton.box
└── tk:skeleton
```

## Usage

```blade
<tk:skeleton.group animate="shimmer">
    <div class="flex items-center gap-3">
        <tk:skeleton.circle />
        <div class="flex-1">
            <tk:skeleton.line class="w-1/3" />
            <tk:skeleton.line class="w-2/3" />
        </div>
    </div>
    <tk:skeleton.box class="w-full h-32" />
</tk:skeleton.group>
```

A round placeholder next to two lines of different widths, and a large box under them, all light gray with a light band sweeping across. Screen readers announce "Loading..." once for the whole group.

### While Livewire loads

```blade
<div wire:loading.remove wire:target="search">
    @foreach ($results as $result) ... @endforeach
</div>

<tk:skeleton.group wire:loading wire:target="search" animate="pulse">
    @foreach (range(1, 3) as $i)
        <tk:skeleton.line />
    @endforeach
</tk:skeleton.group>
```

Three pulsing lines take the place of the results while `search` runs.

### Lazy components

```blade
public function placeholder()
{
    return <<<'HTML'
        <tk:skeleton.group animate="shimmer">
            <tk:skeleton.box class="h-48" />
        </tk:skeleton.group>
    HTML;
}
```

The placeholder a lazy Livewire component shows until it loads.

## Props

### `tk:skeleton.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `animate` | string | `null` | `shimmer` (a light band sweeping across) or `pulse` (fading in and out). Inherited by the placeholders inside. Without it they are still. |
| `size` | string | `md` | Sets the gap between the placeholders. |

### `tk:skeleton.line`, `tk:skeleton.circle` and `tk:skeleton.box`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `size` | string | `md` | A line takes the height of a line of text of that size; a circle and a box, the width and height of an avatar of that size. |

### `tk:skeleton`

The bare placeholder they are built on: full width, rounded, gray. Takes `animate`, inherited from the group; set its size with classes.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:skeleton.group` | default | The placeholders, laid out as you need. |
| `tk:skeleton*` | default | Content inside the placeholder, rarely needed. |

## Customization

Classes and attributes go to the placeholder element; width, height and rounding classes on the tag replace the defaults.

```blade
<tk:skeleton.box class="w-full h-40 rounded-xl" />
<tk:skeleton class="h-4 w-24" />
```

## Accessibility

The group is a `role="status"` region with a visually hidden "Loading..." (translated), and every placeholder is `aria-hidden`, so screen readers hear one message instead of empty shapes. The animations stop when the user prefers reduced motion.

## Related

- [Loading](loading.md): a spinner, for a short wait or a small area.
- [Loadable](loadable.md): shows a loading state while content loads in the browser; its `loading` slot can hold a skeleton.
- [Progress](progress.md): for a wait whose progress is known.
