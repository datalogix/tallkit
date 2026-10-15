# Popover

A floating panel with any content, opened from a trigger by a [`tk:dropdown`](dropdown.md) or a [`tk:context`](context.md): a filter form, a color palette, a short preview. It is the panel behind [`tk:menu`](menu.md); use `tk:menu` for a list of actions and `tk:popover` for everything else.

## Usage

```blade
<tk:dropdown align="end">
    <tk:button icon="filter" label="Filters" />

    <tk:popover keep-open class="w-72 p-4 space-y-4">
        <tk:checkbox wire:model.live="onlyActive" label="Only active" />
        <tk:select wire:model.live="sort" label="Sort by" :options="['name' => 'Name', 'date' => 'Date']" />
    </tk:popover>
</tk:dropdown>
```

A white panel with a border, rounded corners and a shadow, opened under the button and aligned to its end. It grows from the corner nearest the button. With `keep-open` it stays open while the user works inside it; a click outside or Escape closes it.

### Without `keep-open`

```blade
<tk:dropdown>
    <tk:button label="Share" />

    <tk:popover class="p-2 flex gap-1">
        <tk:button icon="email" tooltip="Email" wire:click="share('email')" />
        <tk:button icon="link" tooltip="Copy link" wire:click="share('link')" />
    </tk:popover>
</tk:dropdown>
```

A row of buttons; any click inside, as on one of them, runs it and closes the panel, as a menu would.

## Props

### `tk:popover`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `keepOpen` | bool | `false` | Keeps the panel open on clicks inside it. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font size, padding, minimum width and maximum height. |
| `animation` | string | scale | How the panel appears. See [Dropdown](dropdown.md#props), which passes it down. |

`position` and `align` are set on the `tk:dropdown` around it.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:popover` | default | The panel content. |

## Customization

Classes and attributes on the tag go to the visible panel. The outer element, in the browser's top layer, takes the `container:` prefix.

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:popover` | The `popover` element that holds the panel. |

```blade
<tk:popover class="w-96 max-h-none p-0" container:id="filters-panel">...</tk:popover>
```

The panel scrolls when its content is taller than the maximum height for its `size`; `max-h-*` on the tag changes it.

## Accessibility

A native `popover` element: the trigger gets `aria-haspopup`, `aria-controls` and `aria-expanded` from the dropdown. Opened with the keyboard, the focus moves into the panel, to the first option, item or tab it finds, or to the panel itself; Escape closes it and returns the focus to the trigger, and Tab out of it closes it too.

## Behavior notes

- The panel is in the browser's top layer, so `overflow: hidden` and `z-index` of its parents do not clip or cover it, and it follows the trigger on scroll and resize, flipping to the other side when there is no room.
- `data-keep-open` on one element inside the panel keeps it open for clicks on that element only.
- The panel has `wire:ignore.self`: a Livewire update keeps it open.

## Related

- [Dropdown](dropdown.md): opens the panel from a trigger.
- [Menu](menu.md): a list of actions in the same panel.
- [Context](context.md): opens it with a right-click.
- [Modal](modal.md): a dialog that covers the page, for longer tasks.
