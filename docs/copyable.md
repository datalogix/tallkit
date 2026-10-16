# Copyable

A small button that copies text to the clipboard: the value of the input it sits next to, the text of any element, or a fixed string. It is usually added through the `copyable` prop of `tk:input`, `tk:textarea` or `tk:color-picker`; use the tag directly to copy a value that is not in a field, such as a token, a command or a link.

## Usage

```blade
<tk:input name="api_key" value="pk_live_123" copyable />
```

An input with a clipboard icon at its end. Clicking it copies the current value of the input, including what the user typed, swaps the icon for a check and shows a "Copied" tooltip for 1.5 seconds.

### Bound to Livewire

```blade
<tk:textarea wire:model="notes" copyable="Copy notes" />
```

```php
public string $notes = "line 1\nline 2";
```

A string in `copyable` becomes the button text, shown next to the icon. Clicking it copies the textarea value as it is in the browser, line breaks included.

### A fixed text

```blade
<tk:copyable label="Copy secret" content="sk_live_123" />
```

`content` is copied as given, with no field around it. Here the button shows the clipboard icon and "Copy secret".

### Copying an element by id

```blade
<code id="install-command">composer require datalogix/tallkit</code>
<tk:copyable target="install-command" />
```

`target` is the id of the element to copy. A form control (anything with a `value`) gives its value; any other element gives its visible text.

### Rendering from data

```blade
<ul>
    @foreach ($keys as $key)
        <li wire:key="key-{{ $key->id }}">
            {{ $key->name }}
            <tk:copyable :content="$key->value" :aria-label="'Copy '.$key->name.' key'" />
        </li>
    @endforeach
</ul>
```

One icon button per row, each copying its own value. An `aria-label` per row tells the buttons apart for screen reader users, since they would all be called "Copy".

## Props

### `tk:copyable`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `content` | string | `null` | The text to copy. When set, `target` and the field around the button are ignored. |
| `target` | string | `null` | The id of the element to copy. Without it, or when no element has that id, the button copies the control of the field it sits in (see Behavior notes). |

Also accepts the [element props](shared-props.md#element-props) (`label`, `badge`, `ariaLabel`...). `icon` replaces the clipboard icon and `iconTrailing` the check shown after a copy.

### Through `tk:input`, `tk:textarea` and `tk:color-picker`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `copyable` | bool \| string | `false` | Adds the button at the end of the control. A string is its `label`. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:copyable` | default | The button text, in place of the `label` prop. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:copyable` | `copied` | | The text was written to the clipboard. |
| `tk:copyable` | `failed` | | There was nothing to copy (an empty field), or the browser refused to write to the clipboard. |

Both events bubble. They are dispatched on the element that was copied: the input of the field, or the `target` element. Listen on the `tk:input` tag or on any element around it, not on `tk:copyable` (nor with `copyable:@copied`). With `content` there is no element to copy, so the events are dispatched on the button itself and `@copied` on `tk:copyable` works.

With Alpine:

```blade
<div x-data="{ copies: 0 }">
    <tk:input name="api_key" copyable @copied="copies++" />
</div>
```

Calling a Livewire action:

```blade
<tk:copyable content="sk_live_123" @copied="$wire.logCopy()" />
```

## Customization

Classes and attributes on `tk:copyable` go to its `<button>`, which is a `tk:button` with `variant="none"`: other `tk:button` props such as `variant`, `size` and `tooltip` pass through. `tooltip` replaces the "Copied" text shown after a copy. From the fields, prefix them:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `copyable:` | `tk:input`, `tk:textarea`, `tk:color-picker` | The copy button. |
| `icon:` | `tk:copyable` | The clipboard icon. |
| `icon-trailing:` | `tk:copyable` | The check icon shown after a copy. |

```blade
<tk:input name="api_key" copyable copyable:class="text-blue-600" copyable:tooltip="Key copied" />
```

## Accessibility

A native `<button type="button">`, in the tab order after the control. Its accessible name is the label, the slot content or `aria-label`; when none is given it is "Copy", which changes to "Copied" while the check icon shows. The tooltip does not open on hover or focus, only after a click: its text ("Copied", or "Copy failed") is also announced through a polite live region. The focus stays on the button.

| Key | Action |
|-----|--------|
| Tab | From the control, moves to the button. |
| Enter, Space | Copies the text. |

## Behavior notes

- Without `content` and `target`, the button copies the first control (`[data-tallkit-control]`) inside the field control it sits in; outside a field, the first control inside its previous sibling, or inside its parent's previous sibling. So `<tk:copyable />` written right after a `tk:input` copies that input.
- When Alpine starts and there is neither `content` nor anything to copy, the button removes itself from the page. It is also hidden until Alpine starts (`x-cloak`).
- The value is read at click time, so it is always the current one, typed or set by `wire:model`.
- The check icon, the "Copied" name and the tooltip go back after 1.5 seconds; a new click restarts the time.
- It uses the Clipboard API, and falls back to the browser's copy command where that API is missing (pages served over plain HTTP).
- It keeps its state across Livewire updates of the page (`wire:ignore.self`).

## Related

- [Clearable](clearable.md): empties the input instead of copying it.
- `tk:input.viewable`: shows and hides the text of a password input.
- [Button](button.md): a button for any other action.
