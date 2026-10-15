# Select

A native `<select>` with its label, description, help text and validation error, filled from an array, a collection, an enum or a query. Use it for picking from a list the browser can show natively; for search or custom option content, use [`tk:combobox`](combobox.md).

```
tk:select
└── tk:select.option ...
```

## Usage

```blade
<tk:select wire:model="country" label="Country" :options="['us' => 'United States', 'br' => 'Brazil']" />
```

```php
public string $country = 'br';
```

A "Country" label over a select showing "Brazil", with an empty "---" option first. Choosing United States sets `$country` to `'us'`.

### From models or an enum

```blade
<tk:select name="category_id" :options="App\Models\Category::query()->orderBy('name')" option-value="id" option-label="name" />
<tk:select name="status" :options="App\Enums\Status::class" />
```

A query (or a collection, or a model class) gives one option per row, reading `option-value` and `option-label`. A single model instance gives one option, for that record. An enum class gives one option per case: the backed value (or the case name) as the option value, and the `label()` method, when the enum has one, or else the case name as the text.

### Groups and placeholder

```blade
<tk:select name="region" placeholder="Choose a region" :options="[
    'Americas' => ['us' => 'United States', 'br' => 'Brazil'],
    'Europe' => ['pt' => 'Portugal'],
]" />
```

Nested arrays become `<optgroup>`s labelled by their key. The first option reads "Choose a region" and is selected while no value matches; `:placeholder="false"` removes it.

### Several values

```blade
<tk:select wire:model="tags" multiple rows="4" :options="$tags" />
```

```php
public array $tags = ['php', 'laravel'];
```

A list box four rows tall where Ctrl/Cmd+click picks several options; `$tags` receives an array of values. The name gets `[]` appended, so a form sends an array too.

## Props

### `tk:select`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array \| Collection \| enum class \| model class \| model \| query | `null` | The options: value as the key and text as the value. Nested arrays become groups. |
| `multiple` | bool | `false` | Lets several options be picked. |
| `rows` | int | `5` | With `multiple`, the height in rows. |
| `placeholder` | string \| false | `---` | The text of the empty first option, translated. `false` removes it. Not shown with `multiple`. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `value`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `prepend`, `loading`...). The attributes `option-value`, `option-label`, `option-group-label`, `option-group-children`, `option-group-children-value` and `option-group-children-label` tell how to read options that are arrays or models. `wire:model` and `x-model` bind the value: a string, or an array with `multiple`.

### `tk:select.option`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | string | `null` | The option value. |
| `label` | string | `null` | The text, translated. The slot takes its place. |
| `selected` | bool | `false` | Selects the option. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:select` | default | `<option>` and `<optgroup>` elements written by hand, in place of `options`. |

## Customization

Classes on the tag go to the box when there is anything inside it besides the select (an icon...), otherwise to the select; other attributes go to the `<select>`. Besides the prefixes of the field props and field control props:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `select:` | `tk:select` | Classes for the `<select>` itself (`select:class`). |
| `placeholder:` | `tk:select` | The placeholder option. |
| `option:` | `tk:select` | Each option. |
| `optgroup:` | `tk:select` | Each group. |

```blade
<tk:select name="status" :options="$statuses" icon="flag" class="max-w-xs" option:class="capitalize" />
```

## Accessibility

A native `<select>`, with the label linked by `for` and `aria-describedby` listing the description, help and error. Keyboard and screen reader support are the browser's own.

## Behavior notes

- Old input from a failed submit selects the options that were chosen.
- The placeholder is selected whenever the value matches no option, so a stale value shows as empty instead of picking the first option.
- Option texts from `options` are translated.

## Related

- [Combobox](combobox.md): a select with search, chips for multiple values and custom option content.
- [Radio](radio.md): a few options shown at once.
- [Autocomplete](autocomplete.md): free text with suggestions.
