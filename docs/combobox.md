# Combobox

A form field that opens a searchable list of options and stores the value of the one picked, or of several with `multiple`. Use it when the user must choose from a set too long for a plain select; when free text is allowed, use [Autocomplete](autocomplete.md).

```
tk:combobox
└── tk:combobox.option
```

The options usually come from `options`; `tk:combobox.option` is written when the list is built in the view, or to give an option a badge or other markup.

## Usage

```blade
<tk:combobox name="fruit" :options="['a' => 'Apple', 'b' => 'Banana', 'c' => 'Cherry']" />
```

A field labelled "Fruit" showing `---`. Clicking it opens a list with a search box on top; typing `che` leaves only Cherry, and Enter or a click picks it: the field shows "Cherry", the list closes and the form sends `c`.

### Bound to Livewire

```blade
<tk:combobox label="Fruit" wire:model="fruit" :options="['a' => 'Apple', 'b' => 'Banana', 'c' => 'Cherry']" />
```

```php
public ?string $fruit = 'b';
```

The field starts on Banana. Picking Cherry sets `$fruit` to `'c'`; a value set on the server is shown as soon as the update arrives.

### Picking several

```blade
<tk:combobox label="Fruits" wire:model="fruits" multiple :options="['a' => 'Apple', 'b' => 'Banana', 'c' => 'Cherry']" />
```

```php
public array $fruits = ['a', 'c'];
```

The chosen options show as badges inside the field, each with a remove button. The list stays open while the user picks: picking Banana sets `$fruits` to `['a', 'c', 'b']`, and picking a chosen option again removes it.

### Typing in the field

```blade
<tk:combobox label="Fruit" wire:model="fruit" trigger="input" :options="['a' => 'Apple', 'b' => 'Banana', 'c' => 'Cherry']" />
```

With `trigger="input"` the field itself is the search box and there is none inside the list. Focusing it opens the list, typing filters it, and picking an option writes its label in the input. Text that matches nothing is not kept: the input shows the chosen label again when it loses focus.

### Rendering from data

```blade
<tk:combobox label="Country" wire:model="country">
    @foreach ($countries as $country)
        <tk:combobox.option :value="$country->code" :label="$country->name" wire:key="country-{{ $country->code }}" />
    @endforeach
</tk:combobox>
```

Each `tk:combobox.option` is one option; its `value` is what is stored and its label is what is shown and searched. An option written in the slot can carry a badge or other markup (`badge="New"`) and be `disabled`, which keeps it in the list but stops it from being picked.

## Props

### `tk:combobox`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array \| Collection \| enum \| query | `null` | The options, value as the key and label as the value. A list of plain values uses each value as its own key. A nested array renders a heading followed by its options. `option-value` and `option-label` name the attributes to read when the options are arrays or models. Labels are translated. |
| `multiple` | bool | `false` | Lets the user pick several options. The value is then an array. |
| `trigger` | string | `null` | `input` turns the field into a text input that filters the list as the user types. Any other value renders a button-like field with a search box inside the list. |
| `searchable` | bool | `true` | `false` removes the search box from the list. Ignored with `trigger="input"`. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `help`, `placeholder`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `loading`...), and `disabled` and `readonly`. Without a `placeholder` the empty field shows `---`.

`wire:model` and `x-model` bind the value of the picked option, or `null` when none; with `multiple`, an array of values. A comma-separated string is also accepted as the initial value of a `multiple` combobox.

### `tk:combobox.option`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The option text, translated. It is what the search matches and what the field shows once picked. |

`value` is the value stored when the option is picked; without it, the option's text is used. `disabled` keeps the option in the list but stops it from being picked. `size` is inherited from the combobox. Also accepts the [element props](shared-props.md#element-props) (`badge`, `iconTrailing`, `suffix`...); `icon` replaces the check mark of the chosen options (`check` by default), shown only while the option is chosen.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:combobox` | default | `tk:combobox.option` tags. They come before the ones from `options` when both are given. |
| `tk:combobox` | `search` | Replaces the search box at the top of the list. Ignored with `trigger="input"`. |
| `tk:combobox.option` | default | The option content, in place of `label`. |

## Events

`picked` is dispatched on the hidden input that holds the value, the element the tag's attributes go to, and does not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `picked` | `{ value }`, the new value: the picked option's value, or with `multiple` the whole array | The user picked an option or, with `multiple`, removed one. Not fired when a single value is cleared, nor when the value changes through `wire:model` or `x-model`. |

The `change` event of the hidden input also reaches the tag whenever the value changes. The events of the list inside (`opened`, `selected`, `filtered`...) are dispatched on an inner element and cannot be heard from the tag.

With Alpine:

```blade
<div x-data="{ fruit: null }">
    <tk:combobox name="fruit" :options="['a' => 'Apple', 'b' => 'Banana']" x-on:picked="fruit = $event.detail.value" />

    <span x-text="fruit"></span>
</div>
```

Calling a Livewire action:

```blade
<tk:combobox name="fruit" :options="['a' => 'Apple', 'b' => 'Banana']" x-on:picked="$wire.loadVarieties($event.detail.value)" />
```

## Customization

`class` goes to the box around the field; the other attributes go to the hidden input that holds the value. `combobox:class` adds classes to the field itself. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `trigger:` | `tk:combobox` | The field (`role="combobox"`). `class` is not passed this way: use `combobox:class`. |
| `search:` | `tk:combobox` | The text input, with `trigger="input"`. |
| `selected:` | `tk:combobox` | The element around the chosen label or badges. Not with `trigger="input"`. |
| `selected-label:` | `tk:combobox` | The chosen label, or with `multiple` the placeholder. Not with `trigger="input"`. |
| `selected-option:` | `tk:combobox` | Each badge, with `multiple`. |
| `selected-clear:` | `tk:combobox` | The clear button, without `multiple`. |
| `toggle:` | `tk:combobox` | The arrow button at the end, with `trigger="input"`. |
| `popover:` | `tk:combobox` | The panel that floats under the field. |
| `listbox:` | `tk:combobox` | The `tk:listbox` inside the panel; its own prefixes follow (`listbox:search:`, `listbox:items:`, `listbox:no-records:`). |
| `option:` | `tk:combobox` | Each option coming from `options`. |
| `heading:` | `tk:combobox` | Each group heading coming from `options`. |

The [field props](shared-props.md#field-props) prefixes (`label:`, `help:`...) also work.

```blade
<tk:combobox
    name="fruit"
    :options="['a' => 'Apple', 'b' => 'Banana']"
    combobox:class="font-medium"
    popover:class="shadow-lg"
    listbox:search:placeholder="Find a fruit"
    listbox:no-records:label="No fruit found"
    option:class="text-sm"
/>
```

## Accessibility

Follows the WAI-ARIA combobox pattern with a listbox popup. The field has `role="combobox"`, `aria-haspopup="listbox"`, `aria-expanded` and `aria-controls` pointing to the list, and is labelled by the field label. The list is a `role="listbox"`, with `aria-multiselectable` when `multiple`, of `role="option"` items carrying `aria-selected`. While the list is open, focus is in the search box (or stays in the input with `trigger="input"`), and the active option is exposed through `aria-activedescendant`. A search that finds nothing shows "No records found" in a live region. Each badge's remove button is named "Remove: " followed by the option label.

| Key | Action |
|-----|--------|
| Enter, Space, Arrow Down, Arrow Up | On the closed field, opens the list and moves focus to the search box. |
| Arrow Down / Arrow Up | With the list open, moves to the next / previous option, wrapping around. |
| Enter | Picks the active option. Without `multiple` the list closes and focus returns to the field. |
| Tab | Without `multiple`, picks the active option and moves on. |
| Escape | Closes the list and returns focus to the field. |
| Backspace, Delete | On the closed field, clears the value; with `multiple`, removes the last option. |

With `trigger="input"`, focusing the input opens the list and Backspace in an empty input removes the last option of a `multiple` combobox. With `searchable="false"` focus stays on the field, and the arrows, Enter and Space act on the list from there.

## Behavior notes

- The search ignores case and accents and matches anywhere in the label: `SAO` finds "São Paulo". The first match becomes the active option.
- The search text is kept when the list closes, so reopening shows the same filtered list. Without `multiple`, picking an option empties it.
- With `trigger="input"` the input holds the chosen label, so focusing it again filters the list by that label; erase the text to see every option. After a pick or Escape, the list opens again only when the input is focused anew or the arrow button is clicked: typing alone does not reopen it.
- Clearing a single value with the clear button, Backspace or Delete sets a bound Livewire property to `''`.
- A `disabled` field is out of the tab order and never opens. A `readonly` field does not open and cannot be cleared; with `trigger="input"` it still opens its list on focus, and a `multiple` one still takes a picked option.
- A disabled option cannot be picked, but the arrow keys and the pointer still highlight it.
- The options are rendered once: a Livewire update that changes `options` or the slot does not reach a combobox already on the page. The value still follows `wire:model` both ways.
- Group headings from a nested `options` array are all shown above the options, not each above its group.
- Inside a Livewire component, bind the combobox with `wire:model`. Unbound (`:wire-model="false"`), the field shows `value` but the hidden input starts empty until the user picks something.
- Outside Livewire, a `multiple` combobox sends one `name[]` input per chosen value. A form reset brings back the value the combobox started with.
- Each option's value is compared as a string: `1` and `'1'` are the same option.

## Related

- [Autocomplete](autocomplete.md): a text input with suggestions, where the typed text is the value.
- `tk:select`: the native select, for a short fixed list.
- `tk:listbox`: the list alone, always visible, with an optional search box.
