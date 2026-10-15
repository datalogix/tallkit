# Dropdown

A button that opens a floating panel next to it: a menu of actions, or a popover with any content. Use it for actions that do not need to be on screen all the time, such as the options of a row or of a page header.

```
tk:dropdown
├── trigger (first element, usually tk:dropdown.button)
└── tk:menu or tk:popover (last element)
```

The first element inside `tk:dropdown` is the trigger and the last one is the panel that opens. `tk:dropdown.button` is a `tk:button` with a chevron that turns while the panel is open; any focusable element works as the trigger.

## Usage

```blade
<tk:dropdown>
    <tk:dropdown.button label="Options" />

    <tk:menu>
        <tk:menu.item icon="pencil" wire:click="edit">Edit</tk:menu.item>
        <tk:menu.item icon="copy" wire:click="duplicate">Duplicate</tk:menu.item>
        <tk:menu.item icon="trash-can" wire:click="delete">Delete</tk:menu.item>
    </tk:menu>
</tk:dropdown>
```

An "Options" button with a chevron. Clicking it opens the menu under the button, aligned to its start edge, and the chevron turns upside down. Clicking an item runs its `wire:click` and closes the menu; a click outside, Escape, or Tab out of the menu also closes it.

### Opening on hover

```blade
<tk:dropdown open="hover" position="bottom" align="end">
    <tk:dropdown.button label="Account" variant="ghost" />

    <tk:menu>
        <tk:menu.item href="/profile">Profile</tk:menu.item>
        <tk:menu.item href="/billing">Billing</tk:menu.item>
    </tk:menu>
</tk:dropdown>
```

The menu opens while the pointer is over the button or over the menu, aligned to the button's end edge, and closes shortly after the pointer leaves both. Focusing the button with the keyboard also opens it. On touch screens, where there is no hover, it opens on tap like the default.

### Any content in the panel

```blade
<tk:dropdown align="end">
    <tk:dropdown.button label="Filters" icon="filter" />

    <tk:popover keep-open class="p-4 w-64">
        <tk:checkbox wire:model.live="onlyActive" label="Only active" />
        <tk:select wire:model.live="sort" label="Sort by" :options="['name' => 'Name', 'date' => 'Date']" />
    </tk:popover>
</tk:dropdown>
```

```php
public bool $onlyActive = false;

public string $sort = 'name';
```

A panel with a checkbox and a select. `keep-open` keeps it open while the user works inside it; without it, the first click inside closes the panel. A click outside or Escape closes it.

### A trigger of your own

```blade
<tk:dropdown position="top" align="start">
    <tk:avatar name="Maria Silva" as="button" />

    <tk:menu>
        <tk:menu.item wire:click="logout">Sign out</tk:menu.item>
    </tk:menu>
</tk:dropdown>
```

The avatar opens the menu above it. The trigger gets `aria-haspopup`, `aria-expanded` and `aria-controls` from the dropdown, whatever element it is.

## Props

### `tk:dropdown`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `open` | string | `click` | What opens the panel: `click`, `hover`, or `context` (a right-click or long press; see [Context](context.md)). |
| `position` | string | `bottom` | Side of the trigger the panel opens on: `top`, `bottom`, `left` or `right`. When there is no room, it flips to the other side. |
| `align` | string | `start` | How the panel lines up with the trigger: `start`, `center` or `end`. `start` and `end` follow the text direction. |
| `animation` | string | scale | How the panel appears: `fade`, `zoom`, `slide`, `slide-up`, `slide-down`, `slide-left`, `slide-right`, the last four also with `-full` (`slide-up-full`...), or `none`. By default it grows from half its size. Passed to the `tk:menu` or `tk:popover` inside. |

### `tk:dropdown.button`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `arrow` | string \| false | `chevron-down` | The icon after the label. `false` removes it. |
| `animate` | bool | `true` | Turns the arrow upside down while the panel is open. `false` keeps it still. |

Also accepts the [element props](shared-props.md#element-props) (`label`, `icon`, `badge`...) and every `tk:button` attribute (`variant`, `size`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:dropdown` | default | The trigger first and the panel last. |
| `tk:dropdown.button` | default | The button content, in place of `label`. |

## Events

Events are dispatched on the `tk:dropdown` element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:dropdown` | `opened` | | The panel opened. |
| `tk:dropdown` | `closed` | | The panel closed. |

With Alpine:

```blade
<tk:dropdown x-data="{ open: false }" @opened="open = true" @closed="open = false">
    <tk:dropdown.button label="Options" />
    <tk:menu><tk:menu.item>Edit</tk:menu.item></tk:menu>
</tk:dropdown>
```

Calling a Livewire action:

```blade
<tk:dropdown @opened="$wire.loadNotifications()">
    <tk:dropdown.button label="Notifications" />
    <tk:menu><tk:menu.item>Loading...</tk:menu.item></tk:menu>
</tk:dropdown>
```

Dispatching `tallkit:open` or `tallkit:close` on the trigger opens or closes the panel from your own code.

## Customization

Classes and attributes on `tk:dropdown` go to its wrapper, an inline-flex `<div>`. The trigger and the panel are your own tags, so their classes and attributes are set on them directly.

```blade
<tk:dropdown class="ms-auto">
    <tk:dropdown.button label="Options" variant="ghost" arrow="chevron-up-down" />
    <tk:menu class="min-w-48">
        <tk:menu.item>Edit</tk:menu.item>
    </tk:menu>
</tk:dropdown>
```

## Accessibility

Follows the WAI-ARIA menu button pattern when the panel is a `tk:menu`. The trigger gets `aria-haspopup`, `aria-controls` pointing to the panel and `aria-expanded`, which follows the panel's state.

| Key | Action |
|-----|--------|
| Enter, Space | Opens the menu and focuses its first item. |
| Arrow Down / Arrow Up | On the trigger, opens the menu and focuses the first / last item. With the menu already open (after a mouse click), moves focus into it. |
| Escape | Closes the panel and returns focus to the trigger. |
| Tab | Leaving the trigger and the panel closes it. |

The keys inside the menu are those of [`tk:menu`](#related).

## Behavior notes

- A mouse click opens a menu without moving focus into it; a click from the keyboard (Enter or Space) focuses the first item.
- Any click inside the panel closes it, which is what a menu wants. For a panel the user works in, set `keep-open` on the `tk:popover` (or on `tk:menu`), or `data-keep-open` on one element inside it to keep the panel open only for clicks there.
- The panel is a browser popover in the top layer, so `overflow: hidden` on a parent does not clip it, and it follows the trigger on scroll and resize.
- The wrapper has `wire:ignore.self`: a Livewire update keeps the panel open and the trigger's ARIA attributes as they are.

## Related

- [Context](context.md): the same panel opened by a right-click or long press, placed at the pointer.
- [Button](button.md): `tk:button.menu` is a ready-made "more options" button with a menu.
- `tk:menu`: the list of actions placed inside the dropdown.
- `tk:popover`: the panel for content that is not a menu.
