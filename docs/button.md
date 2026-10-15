# Button

A button for an action: saving a form, calling a Livewire method, opening something. With `href` it is a link that looks like a button. It shows a spinner by itself while its Livewire action or form submit runs.

```
tk:button.group
├── tk:button
└── tk:button.menu
```

`tk:button` is used alone most of the time. `tk:button.group` joins several controls into one block, and `tk:button.menu` is a button that opens a menu of actions; it also works outside a group.

## Usage

```blade
<tk:button>Save</tk:button>
```

A `<button type="button">` with a white background and a thin border (the `outline` variant).

### Variants and colors

```blade
<tk:button variant="primary">Save</tk:button>
<tk:button variant="danger" icon="trash">Delete</tk:button>
<tk:button variant="ghost">Cancel</tk:button>
<tk:button variant="filled" color="green">Approve</tk:button>
```

`primary` is the solid button for the main action of the page, in the accent color. `danger` is solid red, here with an icon before the text. `ghost` has no background until hovered. `color` tints the lighter variants: Approve has a soft green background with green text.

### Livewire actions and forms

```blade
<tk:button variant="primary" wire:click="publish">Publish</tk:button>

<form wire:submit="save">
    <tk:button type="submit" variant="primary">Save</tk:button>
</form>
```

Clicking Publish calls the action; until the request ends the text gives place to a spinner, the button keeps its width and further clicks are ignored. The submit button does the same while the form is being sent. Nothing has to be configured: see `loading` to change the spinner or turn it off.

### Links and icon-only buttons

```blade
<tk:button href="/orders/create" icon="plus">New order</tk:button>
<tk:button icon="pencil" tooltip="Edit" wire:click="edit" />
<tk:button icon="close" variant="ghost" circle aria-label="Close" />
```

The first is an `<a>` with the look of a button. A button with no text is a square the size of its height; the tooltip of the second is also its accessible name, and the third, round, is named with `aria-label`.

### Group

```blade
<tk:button.group>
    <tk:button variant="primary" wire:click="save">Save</tk:button>
    <tk:button.menu variant="primary" icon="chevron-down" tooltip="More save options" dropdown:align="end" :items="[
        ['label' => 'Save and publish', 'action' => 'saveAndPublish'],
        ['label' => 'Save as template', 'action' => 'saveAsTemplate'],
    ]" />
</tk:button.group>
```

A split button: Save and a narrow arrow button joined into one block, rounded only on the outer corners. The arrow opens a menu with the two other actions, aligned to the end of the group.

### Row actions from data

```blade
@foreach ($orders as $order)
    <div wire:key="order-{{ $order->id }}" class="flex items-center justify-between">
        <span>{{ $order->number }}</span>

        <tk:button.menu :items="[
            ['label' => 'Edit', 'icon' => 'pencil', 'href' => '/orders/'.$order->id.'/edit'],
            ['label' => 'Duplicate', 'icon' => 'copy', 'action' => 'duplicate('.$order->id.')'],
            null,
            ['label' => 'Delete', 'icon' => 'trash', 'variant' => 'danger', 'action' => 'delete('.$order->id.')'],
        ]" />
    </div>
@endforeach
```

```php
public function duplicate(int $id): void
{
    Order::findOrFail($id)->replicate()->save();
}
```

Each row ends with a three-dots button named "More options". It opens a menu where Edit is a link, Duplicate and Delete call the Livewire actions with the id of the row, and the `null` entry draws a line before Delete, shown in red.

## Props

### `tk:button`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The text, translated. The default slot does the same without translating. |
| `variant` | string | `outline` | `outline`, `filled`, `ghost`, `subtle`, `primary`, `accent`, `inverse`, `info`, `success`, `danger`, `warning` or `none`. `none` drops the height, padding, corners and background, for a button styled by your own classes. |
| `color` | string | `null` | A palette color (`red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`, plus the ones in `tallkit.colors`). Tints the `filled`, `outline`, `ghost` and `subtle` variants and replaces the accent color of `primary`. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets height, padding, font size and the size of the icons. |
| `type` | string | `button` | The `type` of the `<button>`: `button`, `submit` or `reset`. |
| `href` | string | `null` | Makes the button a link. |
| `loading` | bool \| string | automatic | Shows a spinner while the action runs. On by default for a button with `wire:click` or `action`, and for a `type="submit"`. `false` turns it off; a string picks the spinner: `infinity`, `bar`, `dots`, `ring` or `ball`. |
| `circle` | bool | `false` | Makes the corners fully round. |
| `square` | bool | `true` with no text | Gives the button the same width as its height. |
| `tooltip` | string | `null` | A tooltip, translated. On a button with no text it is the accessible name too. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `iconTrailing`, `badge`, `kbd`...), and what makes an element a link or a button: `route`, `external`, `action`, `disabled`...

### `tk:button.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `size` | string | `null` | The size of the buttons inside that do not set their own. |

### `tk:button.menu`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | `null` | The menu items. Each entry is an array of `tk:menu.item` props and attributes (`label`, `icon`, `href`, `action`, `variant`, `disabled`...); a `null` entry is a separator. |
| `size` | string | `md` | The size of the button and of the menu. |
| `keepOpen` | bool | `false` | Keeps the menu open after an item is clicked. |
| `animation` | string | `null` | The animation of the menu when it opens: `fade`, `zoom`, `slide`, `slide-up`, `slide-down`, `slide-left`, `slide-right` (each of the four also as `-full`) or `none`. |

Also accepts every prop of `tk:button`. Without them it is a `ghost` button with the `ellipsis-vertical` icon and a "More options" tooltip (translated); `variant`, `icon` and `tooltip` replace each of these, and a `label` removes the tooltip.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:button` | default | The button content, in place of the `label` prop. |
| `tk:button.group` | default | The buttons, and any field or dropdown joined to them. |
| `tk:button.menu` | default | Menu items (`tk:menu.item`, `tk:menu.separator`...). They come after the ones from `items` when both are given. |

## Customization

Classes and attributes on `tk:button` go to the `<button>` (or the `<a>`), and on `tk:button.group` to the `<div>` around the buttons. On `tk:button.menu` they go to the button that opens the menu. To reach the other elements, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon:` | `tk:button` | The icon. |
| `icon-trailing:` | `tk:button` | The icon after the text. |
| `badge:` | `tk:button` | The badge. |
| `tooltip:` | `tk:button` | The tooltip options: `tooltip:position`, `tooltip:align`, `tooltip:delay`... |
| `loading-indicator:` | `tk:button` | The layer over the button that holds the spinner. |
| `loading:` | `tk:button` | The spinner, as props and attributes of `tk:loading`. |
| `dropdown:` | `tk:button.menu` | The `tk:dropdown` around the button and the menu: `dropdown:position`, `dropdown:align`. |
| `menu:` | `tk:button.menu` | The menu. `menu:item:` reaches the items rendered from `items`. |

The other prefixes of the element props work too (`prefix:`, `suffix:`, `kbd:`...).

```blade
<tk:button
    class="w-full"
    icon="download"
    icon:class="text-emerald-500"
    wire:click="export"
    loading:label="Exporting..."
>
    Export
</tk:button>
```

## Accessibility

A real `<button>`, or an `<a>` with `href`, so it needs no role. A button with only an icon has no name of its own: give it a `tooltip` or an `aria-label`. A disabled link has no `href` and carries `role="link"` and `aria-disabled="true"`. The spinner is hidden from assistive technology, so the loading state is not announced.

`tk:button.menu` follows the WAI-ARIA menu button pattern: the button has `aria-haspopup`, `aria-expanded` and `aria-controls`, and opens a `role="menu"` with `role="menuitem"` items. `tk:button.group` is a plain `<div>`; give it `role="group"` and an `aria-label` when the buttons belong together for a screen reader too.

| Key | Action |
|-----|--------|
| Tab | Moves to the button. Each button of a group is a separate stop. |
| Enter, Space | Runs the action. On a link, only Enter follows it. |

In a `tk:button.menu`:

| Key | Action |
|-----|--------|
| Enter, Space, Arrow Down | On the button, opens the menu and moves focus to the first item. |
| Arrow Down / Arrow Up | Moves to the next / previous item, going around at the ends. |
| Home / End | Moves to the first / last item. |
| A character | Moves to the next item starting with it. |
| Enter | Runs the focused item, closes the menu and returns focus to the button. |
| Escape | Closes the menu and returns focus to the button. |
| Tab | Closes the menu and moves on to the next element of the page. |

Disabled items are skipped by the arrow keys.

## Behavior notes

- The spinner of a `wire:click` button follows the request of that expression (`wire:target` is set to it). Every button calling the same method without arguments shows it together; one with arguments (`wire:click="remove(5)"`) shows it only for its own call, and a call to `remove(5)` also puts the buttons with plain `remove` in loading. Pass your own `wire:target` to change what the button waits for.
- A button with `loading` and no `wire:click` shows the spinner during any request of the Livewire component; give it a `wire:target` to follow one action.
- A `wire:click` that starts with `$js.` runs in the browser, so the button gets no spinner.
- A submit button shows the spinner while it is disabled. Livewire disables it during a `wire:submit`. In a plain form, the submit buttons are disabled as soon as the form is sent, which also blocks a double submit; they come back after 10 seconds, or when the page is restored by the browser's back button. `data-allow-resubmit` on the form, or on the button that was clicked, turns this off, and a form with a `target` other than `_self` is left alone.
- A submit button that is rendered with `disabled`, `:disabled` or `x-bind:disabled` gets no spinner.
- With `href` the button is a link: `action` is ignored and there is no spinner.
- `aria-disabled="true"` blocks the click and gives the disabled look, keeping the button reachable with the keyboard; `disabled` takes it out of the tab order.
- A `badge` makes a button with no text stop being square.
- `color` does nothing on `accent`, `inverse`, `info`, `success`, `danger`, `warning` and `none`, and the neutral colors (`slate`, `gray`, `zinc`, `stone`) do nothing on the tinted variants, which are neutral already.
- A group only passes `size` down: `variant` is set on each button. `ghost` and `subtle` buttons are not joined, they keep their own corners.
- A field (`tk:input`, `tk:select`...) inside a group is joined to the buttons, and its label is hidden visually but still read by screen readers.
- Buttons inside a popover opened from a group are not joined to it.
- In a right-to-left page the group is mirrored: the first button is on the right, with the rounded corners there.
- `class` and the other attributes of a `tk:button.menu` go to its button, not to the element around it: inside a `@foreach`, put the `wire:key` on the row or pass it as `dropdown:wire:key`.
- A menu opened with the mouse leaves the focus on the button, and the arrow keys do not move into it; opened with the keyboard, the focus goes to the first item.

## Related

- `tk:submit`: a `tk:button` with `type="submit"` and a "Send" label, for the end of a form.
- `tk:link`: a link that looks like text, inside a sentence or a list.
- `tk:dropdown` with `tk:menu`: when the trigger is not a button, or the menu needs a custom layout.
- [Badge](badge.md): a small label that can be clicked, for filters and tags rather than actions.
