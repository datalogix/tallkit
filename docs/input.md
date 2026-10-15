# Input

A text field with its label, description, help text and validation error. It guesses the input type and an input mask from the field name, binds itself to the Livewire property of the same name, and can carry icons, a clear button, a copy button or a show-password button inside the box.

## Usage

```blade
<tk:input name="email" />
```

An "Email" label over an `type="email"` input. Inside a Livewire component with a public `$email` property, the input is bound to it with `wire:model`; after a failed validation, the message shows under the field in red and the input gets a red border and `aria-invalid="true"`.

### Bound to Livewire

```blade
<tk:input wire:model.live.debounce.300ms="search" icon="magnify" placeholder="Search..." :label="false" aria-label="Search" loading />
```

```php
public string $search = '';
```

A search box with a magnifier at the start and no visible label. `$search` receives the text 300 ms after the user stops typing, and a spinner shows at the end of the box while the request runs.

### Masks and types from the name

```blade
<tk:input name="cpf" />
<tk:input name="phone" />
<tk:input name="birth_date" />
<tk:input name="password" />
```

With the `pt_BR` masks of the default config: the CPF field formats what is typed as `999.999.999-99`, the phone one as `(99) 999999999` with a `tel` keyboard, the birth date is an `type="date"` input, and the password field has an eye button that shows and hides the text. `type` and `mask` given on the tag take precedence.

### Extras inside the box

```blade
<tk:input name="api_key" :value="$key" readonly copyable />
<tk:input name="price" prefix="R$" suffix=",00" />
<tk:input name="code" clearable kbd="/" />
```

The first has a copy button that writes the key to the clipboard; the second, attached "R$" and ",00" blocks before and after the box; the third, an "x" that empties it (shown only while there is text) and a `/` key hint.

### Other types

```blade
<tk:input type="file" name="avatar" />
<tk:input type="checkbox" name="terms" label="I accept the terms" />
<tk:input type="range" name="volume" />
```

`file`, `checkbox`, `radio`, `range`, `submit`, `reset` and `button` render `tk:upload`, [`tk:checkbox`](checkbox.md), `tk:radio`, `tk:slider`, `tk:submit` or [`tk:button`](button.md) in its place, with the same name and attributes.

## Props

### `tk:input`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | from the name | The input type. Guessed from the name when not given: `email`, `url` (website, social links), `tel` (phone, whatsapp), `password`, `date` (date, `_on`), `datetime-local` (datetime, `_at`), `time`, `color`, `file` (image, photo, file...). Otherwise `text`. |
| `mask` | string \| false | from the name | An Alpine `x-mask` pattern (`9` digit, `a` letter, `*` any), or the name of a configured mask (`cpf`). Guessed from the name or the type for `text` and `tel` inputs. `false` turns it off. |
| `clearable` | bool \| string | `false` | Adds a button that empties the field. A string is its accessible name. |
| `copyable` | bool \| string | `false` | Adds a button that copies the value. A string is its accessible name. |
| `viewable` | bool \| string | `true` for passwords | Adds a button that shows and hides a password. A string is its accessible name. |
| `old` | bool | `true` | Takes the old input of a failed submit as the value. `false` keeps `value`. Passwords never get it back. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `prefix`, `suffix`, `placeholder`, `value`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `iconTrailing`, `kbd`, `prepend`, `append`, `loading`). `wire:model` and `x-model` bind the value as a string.

### `tk:input.viewable`

The show-password button, for an input of your own inside a field control. Takes the [element props](shared-props.md#element-props).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:input` | default | The initial value, in place of `value`. |

The field props that take a slot (`label`, `description`, `help`, `prefix`...) work as named slots.

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:input` | `viewed` | | The show-password button was clicked. Dispatched on the input; bubbles. |
| `tk:input` | `cleared` | | The clear button emptied the field. Dispatched on the input; bubbles. |
| `tk:input` | `copied` / `failed` | | The copy button copied the value, or could not. Dispatched on the input; bubbles. |

```blade
<tk:input name="token" copyable @copied="$wire.logCopy()" />
```

## Customization

Classes on the tag go to the box (the field control) when there is anything inside it besides the input, otherwise to the input itself. Other attributes go to the `<input>`. Besides the prefixes of the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props):

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `input:` | `tk:input` | Classes for the `<input>` itself (`input:class`). |
| `clearable:` | `tk:input` | The clear button. |
| `copyable:` | `tk:input` | The copy button. |
| `viewable:` | `tk:input` | The show-password button. |

```blade
<tk:input name="search" icon="magnify" class="max-w-xs" input:class="font-mono" clearable:tooltip="Clear search" />
```

## Accessibility

The label is a `<label for>` pointing to the input, and `aria-describedby` lists the description, the help text and the validation error. An invalid field has `aria-invalid="true"`. The buttons inside the box have accessible names (translated "Clear", "Copy", "Toggle password visibility"), and the show-password button has `aria-pressed`.

## Behavior notes

- Inside Livewire, a field with a `name` and no `wire:model` is bound to the public property with the same name, when the component has it; `wire-model="false"` turns that off. `value` and the slot are ignored there: the property is the value.
- `date`, `datetime-local`, `time` and `month` values outside Livewire are converted to the format the browser expects (`2026-10-14`, `2026-10-14T10:30`...), so a `Carbon` or a full datetime string works as `value`.
- A digit-only mask on a `text` input sets `inputmode="numeric"`, for a numeric keyboard on phones.
- The masks guessed from the name come from `config('tallkit.masks')`, per locale, and follow the app's locale.
- Showing a password survives Livewire updates: the type stays `text` until the button is clicked again.

## Related

- `tk:textarea`: multi-line text.
- `tk:select`, [Combobox](combobox.md), [Autocomplete](autocomplete.md): choosing from options.
- [Field](field.md): the label, help and error layout around it, for controls of your own.
- [Clearable](clearable.md), [Copyable](copyable.md): the buttons it places inside the box.
