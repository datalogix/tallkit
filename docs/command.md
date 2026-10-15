# Command

A list of actions with a search box on top that filters it as the user types, for command menus and quick navigation. `tk:command` shows the list in the page; `tk:command.modal` puts it in a modal opened by a button or a keyboard shortcut, as a command palette.

```
tk:command
└── tk:listbox.item

tk:command.modal
├── trigger (default slot)
└── tk:listbox.item (content slot)
```

Each item is a `tk:listbox.item`. Selecting it clicks it, so what it does is written on the item itself (`wire:click`, `x-on:click`).

## Usage

```blade
<tk:command label="Commands">
    <tk:listbox.item icon="plus" wire:click="createInvoice">New invoice</tk:listbox.item>
    <tk:listbox.item icon="user" wire:click="inviteMember">Invite member</tk:listbox.item>
    <tk:listbox.item icon="settings" wire:click="openSettings">Settings</tk:listbox.item>
</tk:command>
```

A bordered box with a search box over the three items. Typing `inv` leaves New invoice and Invite member, with the first one highlighted; Enter, Tab or a click selects it, which runs its `wire:click`. The search box keeps its text after a selection.

### As a command palette

```blade
<tk:command.modal shortcut="ctrl.k">
    <x-slot:content>
        <tk:listbox.item icon="plus" wire:click="createInvoice">New invoice</tk:listbox.item>
        <tk:listbox.item icon="settings" wire:click="openSettings">Settings</tk:listbox.item>
    </x-slot:content>
</tk:command.modal>
```

Renders a "Search" button. The button, or Ctrl+K anywhere on the page, opens a modal with the search box focused. Selecting an item runs its action and closes the modal; Escape closes it too. The items go in the `content` slot, since the default slot is the trigger.

### Your own trigger

```blade
<tk:command.modal name="palette" :close-on-select="false">
    <tk:button icon="search" label="Find a page" />

    <x-slot:content>
        <tk:listbox.item x-on:click="$wire.pin('dashboard')">Pin Dashboard</tk:listbox.item>
        <tk:listbox.item x-on:click="$wire.pin('invoices')">Pin Invoices</tk:listbox.item>
    </x-slot:content>
</tk:command.modal>
```

The default slot replaces the "Search" button; clicking anything in it opens the modal, unless it holds a disabled button. With `:close-on-select="false"` the modal stays open after each selection, so the user can pin several pages before pressing Escape. The `name` lets other code open it: `tallkit.modal('palette').show()` in JavaScript, or `$this->modal('palette')->show()` in the Livewire component that renders it.

### Rendering from data

```blade
<tk:command label="Pages">
    @foreach ($pages as $page)
        <tk:listbox.item :label="$page->title" :icon="$page->icon" wire:click="go({{ $page->id }})" wire:key="page-{{ $page->id }}" />
    @endforeach
</tk:command>
```

Each item's label is what the search matches. After a Livewire update that changes the items, the list is read again and the current search is applied to it.

## Props

### `tk:command`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sizes the search box, the items, the rounding and the height of the list. |
| `items` | array | `null` | Items rendered after the ones in the slot. A string is an item's label; an array holds the item's attributes (`label`, `icon`, `disabled`...). |
| `label` | string | `null` | The accessible name of the list, translated. |
| `searchable` | bool | `true` | `false` removes the search box; the list then takes focus itself. |
| `noRecords` | string \| bool | `No records found` | The message shown, translated, when the search finds nothing. `false` removes it. |
| `hideEmpty` | bool | `false` | Shows no item until the user types. The no-records message is then never shown. |
| `clearOnSelect` | bool | `false` | Empties the search box after an item is selected. |
| `fuseOptions` | array | `null` | Options merged into the [Fuse.js](https://www.fusejs.io/api/options.html) search (by default `threshold` `0.1`, `ignoreLocation` and `ignoreDiacritics`). `autoHighlight: false` stops highlighting the first match; `tabSelects: false` stops Tab from selecting. |
| `color` | string | `null` | The color of the list's focus ring, seen when the list itself has focus (`searchable` off). |

### `tk:command.modal`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | generated | The modal's name, to open or close it from elsewhere. |
| `size` | string | `md` | Sizes the trigger button and the command inside. |
| `shortcut` | string | `null` | A key combination that opens the modal from anywhere on the page, written as Alpine key modifiers: `ctrl.k`, `meta.k`, `slash`. |
| `focusOnOpen` | bool | `true` | Moves focus to the search box when the modal opens. `false` leaves it on the dialog. |
| `closeOnSelect` | bool | `true` | Closes the modal after an item is selected. |

The other attributes, and the `tk:command` props, go to the `tk:command` inside.

### `tk:listbox.item`

Takes the [element props](shared-props.md#element-props) (`label`, `icon`, `badge`...) and `disabled`, which keeps the item in the list but out of reach of the keyboard and the pointer.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:command` | default | `tk:listbox.item` tags. They come before the ones from `items`. |
| `tk:command` | `search` | Replaces the search box. A `tk:input` placed here still filters the list and drives it with the arrow keys. |
| `tk:command` | `empty` | Replaces the message shown when the search finds nothing. |
| `tk:command.modal` | default | The trigger, in place of the "Search" button. |
| `tk:command.modal` | `content` | `tk:listbox.item` tags. |
| `tk:command.modal` | `search`, `empty` | As on `tk:command`. |
| `tk:listbox.item` | default | The item content, in place of `label`. |

## Events

Events are dispatched on the command's root, the element the tag's attributes go to, and do not bubble: listen on the tag itself. The same holds for `tk:command.modal`, whose attributes go to the command inside.

| Event | Detail | When |
|-------|--------|------|
| `ready` | | The command was set up, after its first filtering. |
| `searched` | `{ query }`, the text of the search box | The user typed in the search box, before the list is filtered. |
| `filtered` | `{ list, items, filteredItems }` | The list was filtered: on load, when the search box gets focus and after typing. Each item is `{ title, el, li }`. |
| `highlighted` | `{ index, item }` | The highlighted item changed. `index` is its position among the visible items. |
| `selected` | `{ index, item, button }` | An item was selected, right after its click ran. |

On `tk:command.modal`, an `x-on:selected` of your own replaces the one that closes the modal: call `close()` in it to keep that (`x-on:selected="close(); ..."`). The modal's `opened` and `closed` events are heard only through the `modal:` prefix (`modal:x-on:closed`), and a `modal:x-on:opened` replaces the one that focuses the search box.

With Alpine:

```blade
<div x-data="{ last: null }">
    <tk:command x-on:selected="last = $event.detail.item.li.textContent.trim()">
        <tk:listbox.item>Archive</tk:listbox.item>
        <tk:listbox.item>Delete</tk:listbox.item>
    </tk:command>

    <span x-text="last"></span>
</div>
```

Calling a Livewire action:

```blade
<tk:command x-on:searched="$wire.logSearch($event.detail.query)">
    <tk:listbox.item wire:click="archive">Archive</tk:listbox.item>
</tk:command>
```

## Customization

On `tk:command`, classes and attributes go to the bordered box around the search box and the list. On `tk:command.modal` they go to that same box inside the modal, which is `w-md` wide unless a width class is given. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `search:` | `tk:command` | The search box, a `tk:input` (`search:placeholder`). |
| `items:` | `tk:command` | The list (`role="listbox"`). |
| `item:` | `tk:command` | Each item coming from `items`. |
| `no-records:` | `tk:command` | The message shown when nothing is found. |
| `trigger:` | `tk:command.modal` | The default "Search" button. |
| `modal:` | `tk:command.modal` | The `tk:modal`, a `dialog`, and its props (`modal:backdrop`). |

`tk:command.modal` also takes the prefixes of `tk:command`.

```blade
<tk:command.modal
    shortcut="ctrl.k"
    class="w-xl"
    modal:class="mt-32"
    trigger:label="Commands"
    search:placeholder="Type a command"
    no-records:label="No command found"
>
    <x-slot:content>
        <tk:listbox.item>Settings</tk:listbox.item>
    </x-slot:content>
</tk:command.modal>
```

## Accessibility

Follows the WAI-ARIA combobox pattern with a listbox that is always shown. The search box has `role="combobox"`, `aria-autocomplete="list"`, `aria-expanded="true"`, `aria-controls` pointing to the list and the name "Search". The list is a `role="listbox"`, named by `label`, of `role="option"` items; disabled ones carry `aria-disabled`. Focus stays in the search box and the highlighted item is exposed through `aria-activedescendant`. A search that finds nothing shows the no-records message in a live region. `tk:command.modal` is a native modal `dialog` named "Search".

| Key | Action |
|-----|--------|
| Arrow Down / Arrow Up | Highlights the next / previous item, wrapping around and skipping disabled ones. |
| Enter | Selects the highlighted item. |
| Tab | Selects the highlighted item and moves focus on. |
| Escape | Removes the highlight. In `tk:command.modal`, closes the modal. |

Without the search box the list itself is in the tab order, and Home / End highlight the first / last item and Space also selects.

## Behavior notes

- The search ignores case and accents and matches anywhere in the label: `sao` finds "São Paulo". After typing, the first match is highlighted; before that, nothing is until an arrow key is pressed.
- Selecting an item fires a click on it, so its `wire:click` or `x-on:click` runs, and then `selected` is dispatched. A disabled item cannot be selected and is not highlighted by the pointer.
- With `clearOnSelect` the search box is emptied, but the list keeps the previous filter until the user types again or the box gets focus.
- With the search box, the list has a maximum height that grows with `size` and scrolls beyond it. Without it, the list shows every item at full height.
- A Livewire update keeps the search text and re-applies it to the new items, and an open `tk:command.modal` stays open.
- Closing `tk:command.modal` keeps the search text: opened again, it shows the same filtered list.

## Related

- [Combobox](combobox.md): a form field where the user picks a value from a searchable list.
- [Autocomplete](autocomplete.md): a text input with suggestions, where the typed text is the value.
- `tk:listbox`: the list alone, the base of this component, for a list of options that is not a command menu.
- `tk:modal`: a modal with any content, for dialogs that are not a command palette.
