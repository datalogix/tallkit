# Listbox

A list of options with a search box that filters them as the user types, picked with the mouse or the arrow keys. Each option runs its own click action when chosen. It is the list behind [`tk:command`](command.md), [`tk:autocomplete`](autocomplete.md) and [`tk:combobox`](combobox.md); use it directly for an inline picker.

```
tk:listbox
├── tk:listbox.search
├── tk:listbox.items
│   └── tk:listbox.item
└── tk:listbox.no-records
```

`tk:listbox` renders the search box, the list and the "no records" message itself; the inner tags are written only to build a list by hand.

## Usage

```blade
<tk:listbox label="Fruits">
    <tk:listbox.item label="Apple" wire:click="pick('apple')" />
    <tk:listbox.item label="Banana" wire:click="pick('banana')" />
    <tk:listbox.item label="Cherry" wire:click="pick('cherry')" />
</tk:listbox>
```

A search input with a magnifier and a clear button, over the three options. Typing "ban" leaves only Banana, highlighted; Enter, or a click, runs its `wire:click`. With no match, "No records found" shows under the input.

### Options from data

```blade
<tk:listbox
    label="Countries"
    :items="$countries->map(fn ($country) => ['label' => $country->name, 'icon' => 'flag', 'wire:click' => 'pick('.$country->id.')'])"
    hide-empty
/>
```

Each entry is a string (the label) or an array of `tk:listbox.item` props and attributes. With `hide-empty` the list starts empty and only shows the options that match what is typed.

### Without search

```blade
<tk:listbox :searchable="false" label="Sort by">
    <tk:listbox.item icon="sort-alphabetical-ascending" label="Name" x-on:click="sort = 'name'" />
    <tk:listbox.item icon="calendar" label="Date" x-on:click="sort = 'date'" />
</tk:listbox>
```

Only the list. It takes the focus itself: Arrow keys move through the options and Enter or Space picks one.

## Props

### `tk:listbox`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | `null` | The options, rendered after the slot. Each is a label or an array of `tk:listbox.item` props and attributes. |
| `label` | string | `null` | The accessible name of the list, translated. |
| `searchable` | bool | `true` | Shows the search box. |
| `hideEmpty` | bool | `false` | Shows no options until something is typed. |
| `noRecords` | string \| false | `No records found` | The message when nothing matches. `false` removes it. |
| `clearOnSelect` | bool | `false` | Empties the search box after an option is picked. |
| `fuseOptions` | array | `null` | Options for the fuzzy search ([Fuse.js](https://www.fusejs.io/api/options.html)), such as `threshold`. |
| `multiple` | bool | `false` | Marks the list as `aria-multiselectable`. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Also sets the list's maximum height when there is a search box. |
| `color` | string | `null` | The color of the focus ring. |
| `standalone` | bool | `true` | `false` leaves out the listbox script, for a component that provides its own. |

### `tk:listbox.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The option text, translated, which the search matches. |
| `size` | string | inherited | Inherited from the listbox. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `kbd`, `suffix`...). Click attributes (`wire:click`, `x-on:click`, `href`) are the option's action; `disabled` makes it unselectable.

### `tk:listbox.items`, `tk:listbox.search` and `tk:listbox.no-records`

The list (`items`, `size`, `multiple`, `color`, `id`), the search input (the props of [`tk:input`](input.md), plus `controls`, the id of the list it filters) and the message (the [element props](shared-props.md#element-props)).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:listbox` | default | The options. |
| `tk:listbox` | `search` | Replaces the search box. |
| `tk:listbox` | `empty` | Replaces the "no records" message. |
| `tk:listbox.item` | default | The option content, in place of `label`. |

## Events

Events are dispatched on the `tk:listbox` element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:listbox` | `ready` | | The list is set up. |
| `tk:listbox` | `searched` | `{ query }` | The user typed in the search box. |
| `tk:listbox` | `filtered` | `{ list, items, filteredItems }` | The visible options changed. |
| `tk:listbox` | `highlighted` | `{ index, item }` | An option was highlighted by the keyboard or the pointer. |
| `tk:listbox` | `selected` | `{ index, item, button }` | An option was picked, after its click action ran. |

With Alpine:

```blade
<tk:listbox x-on:selected="last = $event.detail.item.el.textContent.trim()">
    <tk:listbox.item label="Apple" />
</tk:listbox>
```

Calling a Livewire action:

```blade
<tk:listbox x-on:searched="$wire.set('query', $event.detail.query)">
    <tk:listbox.item label="Apple" />
</tk:listbox>
```

## Customization

Classes and attributes on `tk:listbox` go to its wrapper. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `search:` | `tk:listbox` | The search input (`search:placeholder="Find a fruit"`). |
| `items:` | `tk:listbox` | The `<ul role="listbox">`. |
| `item:` | `tk:listbox` | Each option from `items`. |
| `no-records:` | `tk:listbox` | The "no records" message. |
| `container:` | `tk:listbox.item` | The `<li role="option">` around the option. |

```blade
<tk:listbox :items="$fruits" class="w-72 rounded-lg border p-2" search:placeholder="Find a fruit" items:class="max-h-40" />
```

## Accessibility

Follows the WAI-ARIA listbox pattern, with the search box as a combobox (`role="combobox"`, `aria-autocomplete="list"`, `aria-controls` pointing to the list). The highlighted option is announced through `aria-activedescendant`, so the focus stays in the search box. The "no records" message is a polite live region.

| Key | Action |
|-----|--------|
| Arrow Down / Arrow Up | Highlights the next / previous option, skipping disabled ones. |
| Home / End | Highlights the first / last option (when the list itself has focus). |
| Enter | Picks the highlighted option. |
| Space | Picks the highlighted option (when the list itself has focus). |
| Tab | Picks the highlighted option and moves on. |
| Escape | Clears the highlight. |

## Behavior notes

- The search matches the option text, ignoring accents and case ("sao" finds "São Paulo"), with fuzzy matching tuned by `fuseOptions`.
- Typing highlights the first match, so Enter picks it right away.
- Options added or removed by a Livewire update are picked up, and the current search is applied to them.

## Related

- [Command](command.md): a search-and-run palette built on this tag, also as a modal.
- [Autocomplete](autocomplete.md): a text input that suggests values from a list.
- [Combobox](combobox.md): a select with search, for a form value.
