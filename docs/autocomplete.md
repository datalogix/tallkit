# Autocomplete

A text input that suggests values as the user types. The field keeps whatever was typed: a suggestion is only a shortcut to fill it in. Use it when free text is allowed; to make the user pick one of a fixed set of values use `tk:combobox` or `tk:select`.

```
tk:autocomplete
└── tk:listbox.item
```

The suggestions usually come from `options`; `tk:listbox.item` is only written to give one of them an icon, a badge or other markup.

## Usage

```blade
<tk:autocomplete name="city" :options="['Recife', 'Rio de Janeiro', 'Rio Branco', 'Salvador']" />
```

A text input labelled "City". Typing `rio` opens a list under it with Rio de Janeiro and Rio Branco; choosing one, with the mouse or with the arrow keys and Enter, writes it in the input and closes the list.

### Bound to Livewire

```blade
<tk:autocomplete label="City" wire:model="city" :options="$cities" />
```

```php
public string $city = '';
```

`$city` receives the text of the input: `'Rio de Janeiro'` after choosing that suggestion, or `'Rio das Ostras'` if the user typed a city that is not in the list.

### Suggestions filtered on the server

```blade
<tk:autocomplete label="Customer" wire:model.live.debounce.300ms="search" :options="$this->customers" />
```

```php
public string $search = '';

#[Computed]
public function customers(): array
{
    return Customer::where('name', 'like', "%{$this->search}%")->limit(20)->pluck('name')->all();
}
```

Each update re-renders the options, and the list on screen follows them without the input losing focus. Use this when the full set is too large to send to the browser.

### Custom suggestions

```blade
<tk:autocomplete name="page" label="Go to">
    <tk:listbox.item label="Dashboard" icon="home" />
    <tk:listbox.item label="Invoices" icon="file" badge="12" />
    <tk:listbox.item label="Archived" disabled />
</tk:autocomplete>
```

Items in the slot take the [element props](shared-props.md#element-props). A disabled item shows in the list but is skipped by the arrow keys and cannot be chosen. Slot items come before the ones from `options` when both are given.

### Acting on the choice

```blade
<tk:autocomplete name="tag" label="Add a tag" clear-on-select :options="$tags" x-on:selected="$wire.addTag($event.detail.item.title)" />
```

With `clear-on-select` the input is emptied after a suggestion is chosen, ready for the next one, and the choice is read from the `selected` event.

## Props

### `tk:autocomplete`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array \| Collection \| enum \| query | `null` | The suggestions. Only the labels are used: the keys of a keyed array are ignored, and grouped options are flattened into one list. `option-label` names the attribute to show when the options are arrays or models. |
| `hideEmpty` | bool | `true` | Shows nothing while the input is empty. `false` lists every suggestion as soon as the input is focused. |
| `clearOnSelect` | bool | `false` | Empties the input after a suggestion is chosen, instead of filling it. |
| `fuseOptions` | array | `null` | Options for the [Fuse.js](https://www.fusejs.io/api/options.html) search, merged over the defaults (`threshold` `0.1`, `ignoreLocation` and `ignoreDiacritics` on). |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sizes the input and the list. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `help`, `placeholder`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `loading`...), and everything else a `tk:input` takes (`name`, `disabled`, `readonly`, `clearable`...).

`wire:model` and `x-model` bind the text of the input, a string.

### `tk:listbox.item`

Takes the [element props](shared-props.md#element-props) (`label`, `icon`, `badge`...) and `disabled`.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:autocomplete` | default | `tk:listbox.item` tags, as suggestions. |
| `tk:listbox.item` | default | The item content, in place of `label`. |

## Events

Events are dispatched on the input, the element the tag's attributes go to, and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:autocomplete` | `selected` | `{ index, item, button }`; `item.title` is the text of the suggestion | A suggestion was chosen. |
| `tk:autocomplete` | `searched` | `{ query }` | The text of the input changed, also when a choice filled it. |
| `tk:autocomplete` | `filtered` | `{ list, items, filteredItems }` | The list was filtered. `filteredItems` are the matches, best first. |
| `tk:autocomplete` | `highlighted` | `{ index, item }` | A suggestion became the active one, by arrow key or pointer. |
| `tk:autocomplete` | `opened` | | The list opened. |
| `tk:autocomplete` | `closed` | | The list closed. |

With Alpine:

```blade
<tk:autocomplete name="city" :options="['Recife', 'Salvador']" x-on:selected="city = $event.detail.item.title" />
```

Calling a Livewire action:

```blade
<tk:autocomplete name="city" :options="['Recife', 'Salvador']" x-on:selected="$wire.loadDistricts($event.detail.item.title)" />
```

Write `x-on:selected`, not `@selected`: Blade takes `@selected` as its own directive. When `selected` fires the input already holds the chosen text. The native events of the input (`input`, `change`, `blur`) are listened to on the tag as well.

## Customization

Classes and attributes on the tag go to the `<input>`. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:autocomplete` | The element wrapping the input and the list. |
| `popover:` | `tk:autocomplete` | The panel that floats under the input. |
| `items:` | `tk:autocomplete` | The list (`role="listbox"`). |
| `items:item:` | `tk:autocomplete` | Each suggestion coming from `options`. |

The prefixes of `tk:input` work too (`label:`, `field:`, `help:`, `error:`...).

```blade
<tk:autocomplete name="city" :options="['Recife', 'Salvador']" container:class="max-w-sm" popover:class="shadow-lg" items:item:class="font-medium" />
```

## Accessibility

Follows the WAI-ARIA combobox pattern with list autocomplete. The input has `role="combobox"`, `aria-autocomplete="list"`, `aria-expanded` and `aria-controls` pointing to the list, which is a `role="listbox"` of `role="option"` items. Focus never leaves the input: the active suggestion is exposed through `aria-activedescendant`. After typing, the number of results is announced through a live region ("2 results available", "No records found").

| Key | Action |
|-----|--------|
| Arrow Down / Arrow Up | Moves to the next / previous suggestion, wrapping around. With the list closed, opens it. |
| Enter | Chooses the active suggestion. With none active, the key is left alone, so it submits the form. |
| Escape | Closes the list and keeps the text. |
| Tab | Leaves the field without choosing anything. |

## Behavior notes

- The search is fuzzy only slightly: it ignores case and accents and matches anywhere in the text (`PAULO` finds "São Paulo"), but a typo finds nothing (`aple` does not find "Apple"). Raise `threshold` in `fuseOptions` to tolerate typos.
- When nothing matches, the list stays closed; nothing is shown on the page.
- The list opens 150 ms after the last keystroke, and has the width of the input.
- After a suggestion is chosen, or after Escape, the list stays closed until the user types again or presses an arrow key. A Livewire update does not reopen it.
- Choosing with the mouse keeps the focus in the input.
- The value sent is the text. With `['sp' => 'São Paulo']` the input holds `São Paulo`, never `sp`.
- A `disabled` or `readonly` input never opens the list.
- Several autocompletes on one page do not share anything: each list has its own ids.

## Related

- [Combobox](combobox.md): a searchable list where the user must pick one of the options, and the option's value is what is stored.
- `tk:select`: the native select, for a short fixed list.
- `tk:listbox`: the list alone, always visible, with an optional search box.
