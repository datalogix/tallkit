# Menu

A list of actions in a floating panel, with keyboard navigation, submenus, groups with headings, and checkable items. It is the panel a [`tk:dropdown`](dropdown.md) or a [`tk:context`](context.md) opens; the account menu and `tk:button.menu` use it too.

```
tk:dropdown
├── trigger
└── tk:menu
    ├── tk:menu.item
    ├── tk:menu.separator
    ├── tk:menu.group
    │   ├── tk:menu.heading
    │   └── tk:menu.item ...
    ├── tk:menu.submenu
    │   └── tk:menu.item ...
    ├── tk:menu.radio.group
    │   └── tk:menu.radio ...
    └── tk:menu.checkbox.group
        └── tk:menu.checkbox ...
```

## Usage

```blade
<tk:dropdown>
    <tk:dropdown.button label="Actions" />

    <tk:menu>
        <tk:menu.item icon="pencil" wire:click="edit" kbd="E">Edit</tk:menu.item>
        <tk:menu.item icon="content-copy" wire:click="duplicate">Duplicate</tk:menu.item>
        <tk:menu.separator />
        <tk:menu.item icon="trash-can" variant="danger" wire:click="delete">Delete</tk:menu.item>
    </tk:menu>
</tk:dropdown>
```

Three actions with icons, a line, and Delete in red when highlighted. Choosing an item runs its `wire:click` and closes the menu. Typing a letter jumps to the next item starting with it.

### From data

```blade
<tk:menu :items="[
    ['label' => 'Profile', 'href' => '/profile', 'icon' => 'account'],
    ['label' => 'Settings', 'href' => '/settings', 'icon' => 'cog'],
    null,
    ['label' => 'Sign out', 'action' => 'logout', 'icon' => 'logout'],
]" />
```

Each entry is an array of `tk:menu.item` props and attributes; an empty entry (`null`) is a separator, and `'separator' => true` adds one after an item. Items with `href` are links that navigate with `wire:navigate`.

### Groups and submenus

```blade
<tk:menu>
    <tk:menu.group label="Export">
        <tk:menu.item wire:click="export('csv')">CSV</tk:menu.item>
        <tk:menu.item wire:click="export('pdf')">PDF</tk:menu.item>
    </tk:menu.group>

    <tk:menu.submenu label="Move to" icon="folder-move">
        @foreach ($folders as $folder)
            <tk:menu.item wire:click="move({{ $folder->id }})" wire:key="folder-{{ $folder->id }}">{{ $folder->name }}</tk:menu.item>
        @endforeach
    </tk:menu.submenu>
</tk:menu>
```

The group has a muted "Export" heading and lines around it. "Move to" opens a second menu beside it on hover, on click, or with Arrow Right.

### Checkable items bound to Livewire

```blade
<tk:menu>
    <tk:menu.radio.group label="Sort by" wire:model.live="sort" :items="['name' => 'Name', 'date' => 'Date']" />
    <tk:menu.checkbox.group label="Columns" wire:model.live="columns" :items="['email' => 'Email', 'phone' => 'Phone']" keep-open />
</tk:menu>
```

```php
public string $sort = 'name';

public array $columns = ['email'];
```

A check mark shows next to the chosen sort and next to each visible column. Choosing Date sets `$sort` to `'date'` and closes the menu; with `keep-open`, the column items stay open while toggled, and `$columns` becomes, e.g., `['email', 'phone']`.

## Props

### `tk:menu`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `null` | Items rendered before the slot. Each is an array of `tk:menu.item` props; `null` is a separator. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Inherited by the items. |
| `keepOpen` | bool | `false` | Keeps the menu open when an item is chosen. |
| `animation` | string | scale | How the menu appears. See [Dropdown](dropdown.md#props). |

### `tk:menu.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `null` | `danger` highlights the item in red. |
| `keepOpen` | bool | `false` | Keeps the menu open when this item is chosen. |

Also accepts the [element props](shared-props.md#element-props) (`label`, `icon`, `badge`, `kbd`, `suffix`...) and the link and action props of [Element](element.md#props) (`href`, `route`, `action`, `disabled`...).

### `tk:menu.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The heading of the group, which also names it. |

### `tk:menu.submenu`

Takes the props of `tk:menu.item` (`label`, `icon`...) for its row, and `keepOpen` and `animation` for the menu it opens.

### `tk:menu.radio.group` and `tk:menu.checkbox.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `null` | The options: `value => label`, a list of labels (the label is the value), or arrays of item props with `value`. |
| `value` | mixed | `null` | The initial value: one value for radios, an array for checkboxes. |
| `label` | string | `null` | The heading of the group. |
| `keepOpen` | bool | `false` | Keeps the menu open while items are toggled. |

`wire:model` and `x-model` bind the value: the chosen value for radios, an array of values for checkboxes.

### `tk:menu.radio` and `tk:menu.checkbox`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `checked` | bool | `false` | The initial state, outside a group. |
| `iconOn` | string | `check` | The icon shown when checked. |
| `iconOff` | string | `null` | The icon shown when unchecked. |

Inside a group, the item's `value` attribute is compared with the group's value. Also accepts the element props.

### `tk:menu.heading` and `tk:menu.separator`

A muted heading (`label` and the element props), and a line, with an optional `label` in the middle.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:menu` | default | The items. |
| `tk:menu` | `prepend`, `append` | Content before and after everything else. |
| `tk:menu.item`, `tk:menu.radio`, `tk:menu.checkbox` | default | The item text, in place of `label`. |
| `tk:menu.group`, `tk:menu.submenu`, the choice groups | default | The items inside. |

## Customization

Classes and attributes on `tk:menu` go to the panel's content box. On the items, they go to the item element, and the parts take the prefixes of the element props.

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `item:` | `tk:menu`, choice groups | Each item rendered from `items`. |
| `separator:` | `tk:menu` | Each separator rendered from `items`. |
| `heading:`, `separator-top:`, `separator-bottom:` | `tk:menu.group` | The heading and the lines around the group. |
| `menu:`, `container:` | `tk:menu.submenu` | The menu it opens, and the element around both. |
| `icon-on:`, `icon-off:` | `tk:menu.radio`, `tk:menu.checkbox` | The checked and unchecked icons. |
| `container:` | `tk:menu.separator` | The element around the line. |

```blade
<tk:menu class="min-w-56" :items="$items" item:class="font-normal" />
```

## Accessibility

Follows the WAI-ARIA menu pattern: `role="menu"`, items with `role="menuitem"`, `menuitemradio` or `menuitemcheckbox` with `aria-checked`, groups with `role="group"` named by their heading. Only one item is in the tab order at a time.

| Key | Action |
|-----|--------|
| Arrow Down / Arrow Up | Moves to the next / previous item, skipping disabled ones. |
| Home / End | Moves to the first / last item. |
| A letter | Moves to the next item starting with it. |
| Enter, Space | Chooses the focused item. |
| Arrow Right | On a submenu item, opens it and focuses its first item. |
| Arrow Left | In a submenu, closes it and returns to its item. |
| Escape | Closes the menu and returns focus to the trigger. |

## Behavior notes

- Separators at the start or end of the menu, and two in a row, are hidden, so a list built from data can add them freely.
- When any item has an icon, the items without one get an empty space of the same width, so the labels line up.
- A `tk:menu` outside a dropdown or context is shown in place, as a plain list of actions.

## Related

- [Dropdown](dropdown.md): opens the menu from a button.
- [Context](context.md): opens it with a right-click.
- [Button](button.md): `tk:button.menu`, a "more options" button with a menu.
- [Listbox](listbox.md): a list of options to search and pick from.
