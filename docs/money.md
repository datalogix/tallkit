# Money

A currency input: the amount is typed and shown in the currency's format (`1.234,50` in reais, `1,234.50` in dollars), with the symbol attached, while the Livewire property and the submitted form receive a plain number. It is a [`tk:input`](input.md) with a money mask.

## Usage

```blade
<tk:money wire:model="price" label="Price" />
```

```php
public float $price = 1234.5;
```

With the app in Brazilian Portuguese, a field with "R$" at the start showing `1.234,50`. As the user types, thousands separators and the decimal comma are placed for them; `$price` receives the number with a dot as the decimal separator, e.g. `'987.65'`.

### A specific currency

```blade
<tk:money name="amount" currency="USD" />
<tk:money name="total" currency="EUR" position="append" />
```

`$` before a `1,234.50` format; `€` after a `1.234,50` format. Without `currency`, the currency follows the app's locale: BRL for Portuguese, USD for English (GBP for `en_GB`), JPY for Japanese, EUR otherwise.

### Amounts in cents

```blade
<tk:money wire:model="cents" currency="USD" as="cents" />
```

```php
public int $cents = 1999;
```

The field shows `19.99`, and `$cents` receives whole cents: typing `25.50` sets it to `2550`.

## Props

### `tk:money`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `currency` | string | from the locale | `BRL`, `USD`, `EUR`, `GBP` or `JPY`. Sets all the format props below; any of them given on the tag takes precedence. |
| `symbol` | string | from the currency | The currency symbol shown beside the amount. |
| `delimiter` | string | from the currency | The decimal separator. |
| `thousands` | string | from the currency | The thousands separator. |
| `precision` | int | `2` (`0` for JPY) | The number of decimal places. |
| `position` | string | `prepend` | Where the symbol goes: `prepend` (before) or `append` (after). |
| `placeholder` | string | from the currency | The placeholder, such as `0,00`. |
| `as` | string | `decimal` | The value sent to Livewire and the form: `decimal` (`'1234.5'`) or `cents` (`123450`). |
| `prepend`, `append` | string \| slot | the symbol | Replace the content before or after the amount. |

Also accepts the props of [`tk:input`](input.md) (`label`, `help`, `size`, `clearable`...) and the [field props](shared-props.md#field-props). `wire:model` binds the number; its modifiers (`.live`, `.blur`, `.change`) work as on any input.

## Customization

As [`tk:input`](input.md#customization): classes go to the box, other attributes to the `<input>`, and the parts take the input's prefixes.

```blade
<tk:money wire:model="price" class="max-w-40" input:class="text-end tabular-nums" />
```

## Accessibility

A text input with `inputmode="decimal"` (`numeric` with no decimals), so phones show a number keyboard. The symbol is visible text next to the input; include the currency in the `label` when it is not obvious from the page.

## Behavior notes

- The displayed text is formatted, but a native form submit sends the plain number under the field's `name`, as Livewire does.
- A value set on the server, or changed by a Livewire update, is shown formatted in the field.
- A value with more decimals than `precision` is rounded half up for display.
- In `cents` mode, amounts too large for a JavaScript number are sent as strings of digits, without losing precision.

## Related

- [Input](input.md): the input it is built on, with masks for other formats.
- `tk:slider`: picking a number in a range.
