# Clearable

A small button that empties the input it sits next to and puts the focus back in it. It is usually added through the `clearable` prop of `tk:input`; use the tag directly to place it yourself or to run an action of your own when it is clicked.

## Usage

```blade
<tk:input name="search" placeholder="Search..." clearable />
```

An input with an "×" button at its end. The button shows only while the input has a value; clicking it empties the input, keeps the focus there and hides itself until something is typed again.

### Bound to Livewire

```blade
<tk:input wire:model.live="search" clearable />
```

```php
public string $search = 'laravel';
```

Clicking the button sets `$search` to `''`. Clearing fires the input's `input` and `change` events, so `wire:model` and `x-model` pick up the empty value like any typing; with `.live` the request goes out at once.

### With a text label

```blade
<tk:input name="search" clearable="Clear search" />
```

A string in `clearable` becomes the button text, shown next to the icon, in place of the "Clear input" tooltip.

### Placed by hand, with an action

```blade
<div x-data="{ query: 'filter', page: 3 }">
    <tk:input x-model="query">
        <x-slot:append>
            <tk:clearable action="page = 1" />
        </x-slot:append>
    </tk:input>
</div>
```

`tk:clearable` works anywhere inside the control of a field. On click, `action` runs first (here resetting `page`), then the input is emptied, so `query` becomes `''`.

### Outside a field

```blade
<div x-data="{ filters: ['open', 'mine'] }">
    <tk:clearable label="Clear filters" action="filters = []" />
</div>
```

With no field around it there is no input to empty: the button is always visible, and clicking it only runs `action` and fires `cleared`.

## Props

### `tk:clearable`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | Visible text next to the icon, translated. Without a label or slot content the button shows only the icon, with the tooltip "Clear input". |
| `action` | string | `null` | An Alpine expression evaluated on click, before the input is emptied, in the scope of the button. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `badge`, `ariaLabel`...). `icon` defaults to `close`; `:icon="false"` removes it.

### Through `tk:input`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `clearable` | bool \| string | `false` | Adds the button at the end of the control. A string is its `label`. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:clearable` | default | The button text, in place of the `label` prop. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:clearable` | `cleared` | | The button was clicked and the input was emptied. |

The event is dispatched on the input the button cleared and bubbles, so listen on the `tk:input` tag or on any element around it, not on `tk:clearable` (nor with `clearable:@cleared`). A `tk:clearable` with no input around it dispatches the event on itself.

With Alpine:

```blade
<div x-data="{ cleared: 0 }">
    <tk:input name="search" clearable @cleared="cleared++" />
</div>
```

Calling a Livewire action:

```blade
<tk:input wire:model="search" clearable @cleared="$wire.resetFilters()" />
```

## Customization

Classes and attributes on `tk:clearable` go to its `<button>`, which is a `tk:button` with `variant="none"`: other `tk:button` props such as `variant`, `size` and `tooltip` pass through. From `tk:input`, prefix them:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `clearable:` | `tk:input` | The clear button. |

```blade
<tk:input name="search" clearable clearable:class="text-red-500" clearable:icon="trash" clearable:tooltip="Remove" />
```

## Accessibility

A native `<button type="button">`. Its accessible name is the label, or the tooltip text ("Clear input", translated) when there is no label and no slot content. The button does not take the focus on mouse down, and after clearing the focus goes back to the input.

| Key | Action |
|-----|--------|
| Tab | From the input, moves to the button while it is visible. |
| Enter, Space | Clears the input and moves the focus back to it. |

## Behavior notes

- The input is the first `tk:input` element inside the same field control (`[data-tallkit-field-control]`), so the button must sit inside it, as in the `append` slot.
- The button is hidden while the input is empty, disabled or `readonly`, and before Alpine starts (`x-cloak`). It shows again when the input gets a value by typing, through `x-model`, from a Livewire update or after a form reset. A number input holding text the browser cannot parse counts as not empty.
- Clicking it while the input is disabled or `readonly` does nothing, and `action` does not run.
- It keeps its visibility across Livewire updates of the page (`wire:ignore.self`).

## Related

- [Copyable](copyable.md): copies the input value instead of clearing it.
- `tk:input.viewable`: shows and hides the text of a password input.
- [Button](button.md): a button for any other action.
