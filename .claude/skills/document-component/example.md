# Tree

A hierarchical list whose branches expand and collapse, for navigating nested data such as folders or categories. Use it when the levels matter; for a flat list of choices use a listbox.

```
tk:tree
└── tk:tree.item
    └── tk:tree.item ...
```

## Usage

```blade
<tk:tree>
    <tk:tree.item label="Documents" expanded>
        <tk:tree.item label="Invoices" />
        <tk:tree.item label="Contracts" />
    </tk:tree.item>

    <tk:tree.item label="Pictures" />
</tk:tree>
```

Documents starts open, showing its two children. Pictures has no children, so it is a leaf and has no expand button.

### Selecting an item

```blade
<tk:tree selectable wire:model="folder">
    <tk:tree.item name="docs" label="Documents">
        <tk:tree.item name="invoices" label="Invoices" />
    </tk:tree.item>
</tk:tree>
```

```php
public ?string $folder = null;
```

Clicking Invoices sets `$folder` to `'invoices'`. With `selectable="multiple"` the property is an array of names, e.g. `['docs', 'invoices']`. Items without a `name` cannot be selected.

### Rendering from data

```blade
<tk:tree selectable wire:model="folder">
    @foreach ($folders as $folder)
        <tk:tree.item :name="$folder->slug" :label="$folder->name" :expanded="$folder->is_pinned" wire:key="folder-{{ $folder->id }}">
            @foreach ($folder->children as $child)
                <tk:tree.item :name="$child->slug" :label="$child->name" wire:key="folder-{{ $child->id }}" />
            @endforeach
        </tk:tree.item>
    @endforeach
</tk:tree>
```

Give each item a `wire:key`, so a Livewire update that adds or removes folders keeps the open branches as they are.

### Custom label

```blade
<tk:tree>
    <tk:tree.item icon="folder">
        <x-slot:label>
            Documents <tk:badge>12</tk:badge>
        </x-slot:label>

        <tk:tree.item label="Invoices" />
    </tk:tree.item>
</tk:tree>
```

The `label` slot replaces the text with any markup; here the count sits next to the name, inside the clickable row.

## Props

### `tk:tree`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `selectable` | bool \| string | `false` | Lets the user select items. `true` selects one; `multiple` selects several. |
| `size` | string | `md` | `sm`, `md` or `lg`. Sets row height, font size and indentation. |
| `lines` | bool | `false` | Draws guide lines connecting each branch to its children. |
| `disabled` | bool | `false` | Disables every item. |

`wire:model` and `x-model` bind the selected item names: one name with `selectable`, an array of names with `selectable="multiple"`.

### `tk:tree.item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The item text. |
| `name` | string | `null` | Identifies the item in the tree's `wire:model` / `x-model` value. Required for the item to be selectable. |
| `expanded` | bool | `false` | Opens the branch on the first render. |
| `disabled` | bool | `false` | Disables this item and its children. |

`size` is inherited from the tree. Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `suffix`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:tree` | default | The top-level items. |
| `tk:tree.item` | default | The child items. An item with no content here is a leaf and has no expand button. |
| `tk:tree.item` | `label` | The item content, in place of the `label` prop. |

## Events

Events are dispatched on the component's own element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:tree.item` | `opened` | | The branch was expanded. |
| `tk:tree.item` | `closed` | | The branch was collapsed. |
| `tk:tree` | `selected` | `{ value }`, the selected name or names | The selection changed by user action. |

With Alpine:

```blade
<tk:tree selectable @selected="current = $event.detail.value">
    <tk:tree.item name="docs" label="Documents" />
</tk:tree>
```

Calling a Livewire action:

```blade
<tk:tree selectable @selected="$wire.open($event.detail.value)">
    <tk:tree.item name="docs" label="Documents" @opened="$wire.loadChildren('docs')" />
</tk:tree>
```

## Customization

Classes and attributes on a tag go to its root element. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `toggle:` | `tk:tree.item` | The expand button. |
| `label:` | `tk:tree.item` | The element wrapping the label. |

```blade
<tk:tree class="max-h-96 overflow-y-auto">
    <tk:tree.item label="Documents" label:class="font-semibold" toggle:aria-label="Toggle Documents" />
</tk:tree>
```

## Accessibility

Follows the WAI-ARIA tree view pattern. The root has `role="tree"`, each item `role="treeitem"` with `aria-expanded` on branches and `aria-selected` when `selectable`; children are wrapped in `role="group"`. Only one item is in the tab order at a time.

| Key | Action |
|-----|--------|
| Arrow Down / Arrow Up | Moves focus to the next / previous visible item. |
| Arrow Right | Expands a closed branch; on an open one, moves to its first child. |
| Arrow Left | Collapses an open branch; otherwise moves to the parent. |
| Home / End | Moves focus to the first / last visible item. |
| Enter, Space | Selects the focused item. |

Disabled items are skipped by the arrow keys.

## Behavior notes

- `expanded` only sets the initial state: after the first render the browser owns it, so a Livewire update never collapses a branch the user opened.
- Selecting an item inside a collapsed branch through `wire:model` does not expand its ancestors.
- A disabled branch can still be expanded and collapsed; only selection is blocked.

## Related

- [Accordion](accordion.md): sections of content that open and close, with no hierarchy or selection.
- `tk:listbox`: a flat list of options to choose from.
