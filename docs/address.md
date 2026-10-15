# Address

A Brazilian postal address, in two forms: `tk:address` shows one as text, and `tk:address.form` is the set of inputs to type one in, which fills street, neighborhood, city and state from the zipcode (CEP).

```
tk:address

tk:address.form
└── tk:address.fields
```

`tk:address.form` renders `tk:address.fields` itself; the inner tag is only written to place the inputs in a layout of your own, without the lookup.

## Usage

```blade
<tk:address.form />
```

Seven inputs in two rows: zipcode, address, number and complement, then neighborhood, city and state. Once the zipcode has 8 digits the address is looked up, the four fields it knows are filled in and the focus moves to the number.

### Bound to Livewire

```blade
<tk:address.form name="billing" title="Billing address" required />
```

```php
public array $billing = [
    'zipcode' => '',
    'address' => '',
    'number' => '',
    'complement' => '',
    'neighborhood' => '',
    'city' => '',
    'state' => '',
];
```

`name` nests the inputs under one key (`billing[zipcode]`, `billing[city]`...). Inside a Livewire component each input binds itself to the property with the same path, so after the lookup `$billing` holds `['zipcode' => '01001-000', 'address' => 'Praça da Sé', 'neighborhood' => 'Sé', 'city' => 'São Paulo', 'state' => 'SP', ...]`. `required` marks every input but the complement.

### Fields bound one by one

```blade
<tk:address.form
    zipcode:wire:model="form.zipcode"
    address:wire:model="form.street"
    number:wire:model="form.number"
    complement:wire:model="form.complement"
    neighborhood:wire:model="form.district"
    city:wire:model="form.city"
    state:wire:model="form.state"
/>
```

Attributes prefixed with a field name go to that input, so each one can be bound to a property of any name. The values filled by the lookup reach Livewire like typed ones.

### Your own lookup endpoint

```blade
<tk:address.form card title="Shipping address" lookup="/api/zipcodes/{zipcode}" />
```

With `card` the fields sit inside a `tk:card`. The lookup calls your URL, with `{zipcode}` replaced by the 8 digits, and expects JSON with `address`, `neighborhood`, `city` and `state`; a `404` means the zipcode does not exist. `:lookup="false"` turns the lookup off and leaves plain inputs.

### Showing an address

```blade
<tk:address :data="$order->shipping" />

<tk:address zipcode="01001000" address="Praça da Sé" number="100" neighborhood="Sé" city="São Paulo" state="SP" inline />
```

The first renders two lines after a map marker icon: `Praça da Sé, 100, Sé` and `São Paulo - SP, 01001-000`. `data` is an array or an object with the same keys as the props. With `inline` the two parts go on one line, separated by a comma.

## Props

### `tk:address`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | array \| object | `null` | The address, read by the keys `zipcode`, `address`, `number`, `complement`, `neighborhood`, `city` and `state`. |
| `zipcode` | string | from `data` | The zipcode. Eight digits are shown as `99999-999`. |
| `address` | string | from `data` | The street. |
| `number` | string | from `data` | The number. |
| `complement` | string | from `data` | Apartment, block and the like. |
| `neighborhood` | string | from `data` | The neighborhood. |
| `city` | string | from `data` | The city. |
| `state` | string | from `data` | The state. |
| `inline` | bool | `false` | Puts everything on one line. |

Also accepts the [element props](shared-props.md#element-props); `icon` defaults to `map-marker`, and `:icon="false"` removes it. Other attributes go to the `tk:text` it renders (`size`, `variant`, `color`...).

### `tk:address.form`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | Nests the input names under this key: `billing` gives `billing[zipcode]`. |
| `required` | bool | `false` | Makes every input required, except the complement. |
| `lookup` | bool \| string \| array | `config('tallkit.address.lookup')`, `true` | `true` looks the zipcode up in ViaCEP, then BrasilAPI. A string is the URL of your own endpoint. `false` turns it off. An array sets options, below. |
| `card` | bool | `false` | Wraps the fields in a `tk:card` in place of a `tk:section`. |
| `size` | string | `md` | The size of the inputs and of the section. |

Other attributes go to the section or card: `title`, `subtitle`, `description`...

Keys of `lookup` as an array:

| Key | Default | Description |
|-----|---------|-------------|
| `url` | `null` | Your endpoint, with `{zipcode}`. |
| `timeout` | `5000` | Milliseconds to wait for each request. |
| `ttl` | `3600000` | Milliseconds a result stays cached. |
| `persist` | `true` | Keeps the cache in the browser storage; `false` keeps it in memory only. |
| `storage` | `session` | `session` or `local`. |
| `messages` | translated | The texts announced to screen readers: `searching`, `filled` and `failed`. Give all three: the array replaces the defaults. |

### `tk:address.fields`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | Nests the input names under this key. |
| `required` | bool | `false` | Makes every input required, except the complement. |
| `size` | string | `md` | The size of the inputs. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:address` | default | Content after the address, on the same line. |
| `tk:address.form` | default | Content after the fields, such as more inputs or a submit button. |

## Events

Events are dispatched on the element of `tk:address.form` and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:address.form` | `loading` | `{ zipcode }` | A zipcode with 8 digits was typed. Fires for a cached one too. |
| `tk:address.form` | `loaded` | `{ zipcode, data, cached }` | The address was found and filled in. `data` always has `logradouro`, `bairro`, `localidade` and `uf`; from ViaCEP it also brings the other keys of its response. |
| `tk:address.form` | `error` | `{ zipcode, error }` | The lookup failed. `error.name` is `NotFoundError` when the zipcode does not exist. |

`zipcode` is the 8 digits, without the dash.

With Alpine:

```blade
<tk:address.form @loaded="city = $event.detail.data.localidade" x-on:error="city = null" />
```

Calling a Livewire action:

```blade
<tk:address.form @loaded="$wire.quoteShipping($event.detail.zipcode)" />
```

Listen to `error` with `x-on:error`: `@error` is taken by Blade's own `@error` directive.

## Customization

Classes and attributes on `tk:address.form` go to the section or card. To reach an input, prefix the attribute with the field name; everything a `tk:input` accepts works (`label`, `placeholder`, `help`, `wire:model`, `class`...):

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `zipcode:` | `tk:address.form`, `tk:address.fields` | The zipcode input. |
| `address:` | `tk:address.form`, `tk:address.fields` | The address input. |
| `number:` | `tk:address.form`, `tk:address.fields` | The number input. |
| `complement:` | `tk:address.form`, `tk:address.fields` | The complement input. |
| `neighborhood:` | `tk:address.form`, `tk:address.fields` | The neighborhood input. |
| `city:` | `tk:address.form`, `tk:address.fields` | The city input. |
| `state:` | `tk:address.form`, `tk:address.fields` | The state select or input. |
| `error:` | `tk:address.form`, `tk:address.fields` | The "Zipcode not found." message. |

```blade
<tk:address.form
    zipcode:label="CEP"
    number:placeholder="No number? Type S/N"
    :state:options="['SP' => 'São Paulo', 'RJ' => 'Rio de Janeiro']"
    error:class="font-medium"
/>
```

The state is a `tk:select` when `state:options` is given, or when the app defines a `statesBR()` function returning the options; otherwise it is a text input.

## Accessibility

The form is a `role="group"` named by its `title`, or "Address" (translated) without one; an `aria-label` or `aria-labelledby` of your own takes precedence. Every input has a label and an `autocomplete` token (`postal-code`, `address-line1`, `address-line2`, `address-level3`, `address-level2`, `address-level1`).

During the lookup the four fields it fills are `inert` with `aria-busy`, and a spinner shows in the address input. "Searching zipcode...", "Address filled." and the failures are announced through a live region. When the zipcode is not found the message shows under the first row, linked to the zipcode input with `aria-describedby` and `aria-invalid`, and the focus returns to the zipcode.

After filling, the focus moves to the number, or to the address when the zipcode has no street. It only moves if the user is still on the zipcode: someone already typing in another field is left there.

`tk:address` renders an `<address>` element.

## Behavior notes

- The lookup starts 300 ms after the last keystroke, and a new keystroke cancels the one in progress.
- With `lookup` `true` the zipcode is sent from the browser to viacep.com.br and, if that fails, to brasilapi.com.br. Set `tallkit.address.lookup` in the config to change the default for every form.
- Results are cached in `sessionStorage` for an hour, so typing the same zipcode again fills at once, with no request.
- A new lookup overwrites address, neighborhood, city and state with what it finds, including text the user typed there. A field the new zipcode has no value for, or any of the four when it is not found, is cleared only if it still holds what the previous lookup filled: text the user typed stays.
- When the lookup fails for another reason (network, timeout, server error), the fields it had filled are cleared, the failure is announced and the focus goes to the address, to be typed by hand. No message shows on the page: listen to `error` for that.
- A state that is not among the select's options is left unselected.
- The zipcode input gets the `99999-999` mask from `tallkit.masks` when the app locale is `pt_BR`.
- Resetting the surrounding `<form>` cancels a lookup in progress and hides the error.
- The loading and error states survive a Livewire update.
- `tk:address.fields` alone has no lookup and no group around the inputs.
- In `tk:address` a prop wins over the same key in `data`, and nothing renders when every part is empty and there is no slot content.
