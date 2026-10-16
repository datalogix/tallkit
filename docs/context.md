# Context

A menu opened with a right-click, a long press on touch screens, or the keyboard's context menu key, placed where the user pointed. Use it for actions on an area or a row, such as copy, rename or delete, in place of the browser's own context menu.

```
tk:context
├── trigger (first element)
└── tk:menu or tk:popover (last element)
```

`tk:context` is a `tk:dropdown` that opens on a context menu request instead of a click. The first element inside it is the area that opens the menu; the last one is what opens.

## Usage

```blade
<tk:context>
    <div class="h-40 grid place-items-center border border-dashed rounded-lg">
        Right-click here
    </div>

    <tk:menu>
        <tk:menu.item icon="copy" wire:click="copy">Copy</tk:menu.item>
        <tk:menu.item icon="clipboard" wire:click="paste">Paste</tk:menu.item>
    </tk:menu>
</tk:context>
```

A dashed box. Right-clicking anywhere in it opens the menu with its top-left corner at the pointer, instead of the browser's menu, and focuses Copy. Clicking an item runs its `wire:click` and closes the menu. A click or a right-click outside the menu, Escape, or Tab out of it also closes it.

### Rendering from data

```blade
<div class="grid gap-2">
    @foreach ($files as $file)
        <tk:context wire:key="file-{{ $file->id }}">
            <div class="p-2 border rounded">{{ $file->name }}</div>

            <tk:menu>
                <tk:menu.item icon="download" wire:click="download({{ $file->id }})">Download</tk:menu.item>
                <tk:menu.separator />
                <tk:menu.item icon="trash" variant="danger" wire:click="delete({{ $file->id }})">Delete</tk:menu.item>
            </tk:menu>
        </tk:context>
    @endforeach
</div>
```

Each row has its own menu, acting on that file. Right-clicking a row opens the menu of that row only.

### A button as the trigger

```blade
<tk:context>
    <tk:button icon="ellipsis">Options</tk:button>

    <tk:menu>
        <tk:menu.item wire:click="rename">Rename</tk:menu.item>
        <tk:menu.item wire:click="archive">Archive</tk:menu.item>
    </tk:menu>
</tk:context>
```

A left click on the button does nothing here: only a right-click, a long press or the context menu key opens the menu. On a button the trigger also gets `aria-haspopup` and `aria-expanded`, which a plain `div` does not.

### Any content

```blade
<tk:context>
    <div class="p-4 border rounded">Invoice #1024</div>

    <tk:popover class="p-4">
        <p>Issued on 2026-10-01, due in 30 days.</p>
    </tk:popover>
</tk:context>
```

The last element can be a `tk:popover` with any content instead of a menu. It opens at the pointer as the menu does; with nothing focusable inside, focus stays where it was. A click inside it closes it, as a menu item does; add `keep-open` to the `tk:popover` to keep it open, for example when it holds a form.

## Props

### `tk:context`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `position` | string | `bottom` | Which side of the pointer the menu opens on: `bottom`, `top`, `left`, `right`, or `start` / `end` (the side that follows the text direction). |
| `align` | string | `start` | How the menu lines up with the pointer: `start`, `end`, `left`, `right` or `center`. `start` puts the pointer at the menu's leading corner. |

These are the props of `tk:dropdown`, which receives every attribute of the tag. `open` defaults to `context`, the right click or long press on the area. When the menu does not fit on the screen in that position, another side or alignment that fits is used.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:context` | default | Two elements: the trigger first, then the `tk:menu` or `tk:popover` it opens. |

## Events

Events are dispatched on the component's root, the wrapper the tag's attributes go to, and do not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `opened` | | The menu opened. |
| `closed` | | The menu closed. |

With Alpine:

```blade
<div x-data="{ count: 0 }">
    <tk:context x-on:opened="count++">
        <div class="p-4 border rounded">Right-click here</div>

        <tk:menu>
            <tk:menu.item>Copy</tk:menu.item>
        </tk:menu>
    </tk:context>

    <span x-text="count"></span>
</div>
```

Calling a Livewire action:

```blade
<tk:context x-on:closed="$wire.clearSelection()">
    <div class="p-4 border rounded">Right-click here</div>

    <tk:menu>
        <tk:menu.item wire:click="copy">Copy</tk:menu.item>
    </tk:menu>
</tk:context>
```

The component's own state is also named `opened`, so inside the tag `opened` means whether the menu is open: name a counter of your own differently, as above.

## Customization

Classes and attributes on the tag go to the wrapper around the trigger and the menu, an `inline-flex` element. Classes for the menu itself go on the `tk:menu` or `tk:popover`, and its animation too (`animation` on `tk:menu`).

```blade
<tk:context class="flex w-full">
    <div class="flex-1 h-40 border rounded">Right-click here</div>

    <tk:menu class="min-w-48" animation="fade">
        <tk:menu.item>Copy</tk:menu.item>
    </tk:menu>
</tk:context>
```

## Accessibility

The trigger is put in the tab order (`tabindex="0"`) when it is not a link, button or form control, and gets `aria-controls` pointing to the menu. When it is a link, a button, a `select` or has a role, it also gets `aria-haspopup` and an `aria-expanded` that follows the menu. The menu is a `tk:menu` (`role="menu"` of `role="menuitem"` items) with its own keyboard. Opening it focuses the first item; closing it with focus inside returns focus to the trigger.

| Key | Action |
|-----|--------|
| Context menu key, Shift + F10 | On the focused trigger, opens the menu below it and focuses the first item. |
| Arrow Down / Arrow Up | Moves focus to the next / previous item, wrapping around. |
| Home / End | Moves focus to the first / last item. |
| A letter | Moves focus to the next item starting with it. |
| Enter, Space | Activates the focused item. |
| Escape | Closes the menu and returns focus to the trigger. |
| Tab | Moves focus out and closes the menu. |

## Behavior notes

- On touch screens the menu opens after a press of half a second; moving the finger more than a few pixels cancels it, and the tap that ends the press does not click the trigger. Text selection and the system's long-press callout are turned off on the wrapper on touch devices.
- Clicking or activating an item runs it and closes the menu, unless the item or the `tk:menu` has `keep-open`. A right-click inside the menu leaves it open.
- Right-clicking the trigger again while the menu is open moves it to the new point; the context menu key moves it back below the trigger.
- With one `tk:context` inside another's trigger, a right-click on the inner area opens only the inner menu; outside it, the outer one opens.
- Dispatching `tallkit:open` or `tallkit:close` on the trigger opens (below the trigger) or closes the menu from your own code.
- A Livewire update keeps an open menu open, and the trigger keeps its `tabindex` and ARIA attributes.

## Related

- `tk:dropdown`: the same menu or panel opened by a left click on its trigger, or on hover with `open="hover"`.
- `tk:menu`: the list of actions, the content of this component.
