# Credit card

A picture of a payment card that follows the card fields of a form as the user types: number, cardholder name, expiration and security code, with the brand detected from the number. It only shows the data; the inputs are your own.

## Usage

```blade
<form>
    <tk:credit-card />

    <tk:input name="number" label="Card number" autocomplete="cc-number" />
    <tk:input name="holder" label="Cardholder name" autocomplete="cc-name" />
    <tk:input name="expiration" label="Expiration" autocomplete="cc-exp" />
    <tk:input name="cvv" label="Security code" autocomplete="cc-csc" />
</form>
```

The card starts with placeholders (`0000 0000 0000 0000`, "Cardholder name", `MM/YYYY`, `123`). It finds the inputs of the closest `<form>` by their `autocomplete` token and updates as they change: typing `3782 8224 6310 005` shows `3782 822463 10005` with the American Express logo and colors. Focusing the security code turns the card to its back, where the code is printed; focusing any other card field turns it to the front. Clicking the card turns it over.

### Bound to Livewire

```blade
<form wire:submit="pay">
    <tk:credit-card />

    <tk:input wire:model="number" label="Card number" autocomplete="cc-number" />
    <tk:input wire:model="holder" label="Cardholder name" autocomplete="cc-name" />
    <tk:input wire:model="expiration" label="Expiration" autocomplete="cc-exp" />
    <tk:input wire:model="cvv" label="Security code" autocomplete="cc-csc" />
</form>
```

```php
public string $number = '';
public string $holder = '';
public string $expiration = '';
public string $cvv = '';
```

The card itself binds to nothing: it reads the inputs, which are bound as usual. When the server changes a property (`$this->number = '4111111111111111'`), the card picks the new value up after the Livewire update, here showing `4111 1111 1111 1111` and the Visa logo.

### Fields outside a form

```blade
<tk:credit-card for="payment-fields" />

<div id="payment-fields">
    <tk:input label="Card number" autocomplete="cc-number" />
    <tk:input label="Cardholder name" autocomplete="cc-name" />
    <tk:select label="Month" autocomplete="cc-exp-month" :options="['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']" />
    <tk:select label="Year" autocomplete="cc-exp-year" :options="['2027', '2028', '2029', '2030']" />
</div>
```

`for` points the card to another element, by id or CSS selector, and it follows the fields inside it. The month and year fields are combined: choosing `04` and `2030` shows `04/2030`, and a missing part shows as `MM` or `YYYY`.

### A saved card

```blade
<tk:credit-card :options="[
    'number' => '•••• •••• •••• 4242',
    'holderName' => 'Jane Doe',
    'expirationDate' => '12/2030',
    'type' => 'visa',
]" />
```

Outside a form, and with no `for`, the card shows the values given in `options` and nothing changes them. A number with anything other than digits and spaces is printed as given, so a masked number keeps its dots. `type` sets the brand when the number alone cannot tell it.

### Custom faces

```blade
<tk:credit-card>
    <x-slot:front-svg>
        <div class="aspect-[750/471] rounded-3xl bg-zinc-900 p-8 font-mono text-white">
            <span x-text="displayNumber()"></span>
            <span x-text="values.holderName"></span>
        </div>
    </x-slot:front-svg>
</tk:credit-card>
```

The slot replaces the drawing of that face. Its content sits inside the card's Alpine scope, so it can print `values.number`, `values.holderName`, `values.expirationDate`, `values.cvv` and the formatted `displayNumber()`. The brand logo is still placed over the top corner of the front.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `for` | string | the closest `<form>` | The id of the element whose fields the card follows, or a CSS selector for it. |
| `options` | array | `null` | Initial values and settings, below. |

Keys of `options`:

| Key | Default | Description |
|-----|---------|-------------|
| `number` | `null` | The card number shown before a field provides one. |
| `holderName` | `null` | The cardholder name. |
| `expirationDate` | `null` | The expiration, shown as given (`12/2030`). |
| `cvv` | `null` | The security code, shown on the back. |
| `type` | detected | Forces the brand: `americanexpress`, `diners`, `discover`, `jcb`, `maestro`, `mastercard`, `unionpay` or `visa`. Any other name shows no brand. |
| `back` | `false` | Starts with the back of the card showing. |
| `for` | `null` | As the `for` prop, which wins when both are given. |

## Slots

| Slot | Description |
|------|-------------|
| `front-svg` | Replaces the drawing of the front. |
| `back-svg` | Replaces the drawing of the back. |

## Events

`flipped` is dispatched on the card element and does not bubble: listen on the tag itself.

| Event | Detail | When |
|-------|--------|------|
| `flipped` | `{ back }`, `true` when the back is now showing | The card turned over, by a click, the Flip card button, or the focus moving between the security code and another card field. Not fired when the side does not change. |

With Alpine:

```blade
<div x-data="{ showingBack: false }">
    <tk:credit-card @flipped="showingBack = $event.detail.back" />
</div>
```

Calling a Livewire action:

```blade
<tk:credit-card @flipped="$wire.trackFlip($event.detail.back)" />
```

## Customization

Classes and attributes go to the card element. Its classes are merged with the defaults, so `class="max-w-xs"` replaces the default `max-w-[400px]`. The component has no prefixed attributes; use the `front-svg` and `back-svg` slots to change the faces.

```blade
<tk:credit-card class="max-w-xs" aria-label="Company card" />
```

## Accessibility

The card is a `role="group"` named "Credit card" (translated), followed by the detected brand ("Credit card Visa"); an `aria-label` of your own replaces the first part. Both faces are `aria-hidden`: the values are read from the inputs, not from the picture. A visually hidden "Flip card" button with `aria-pressed` (`true` while the back shows) turns the card over, and the card shows a focus ring while that button has keyboard focus.

| Key | Action |
|-----|--------|
| Tab | Reaches the Flip card button. |
| Enter, Space | On the Flip card button, turns the card over. |

## Behavior notes

- Fields are found by `autocomplete` token, not by name: `cc-number`, `cc-name`, `cc-exp`, `cc-csc`, and `cc-exp-month` with `cc-exp-year` for a split expiration. A token may come with others (`autocomplete="billing cc-number"`).
- When a field is found, its value replaces the one from `options`, even when it is empty: inside a form with an empty `cc-number` input, `options.number` is never shown. Use `options` for a card that has no fields.
- The brand is detected from the leading digits and sets the logo and the card colors. American Express numbers are grouped 4-6-5, Diners Club 4-6-4 and the others 4-4-4-4; digits beyond the groups are kept at the end.
- Long texts are squeezed to fit their place on the card instead of overflowing.
- Resetting the form puts the card back to what the fields hold after the reset.
- The card has `wire:ignore`: a Livewire update does not touch it, and `options` is read only once. The values still follow the fields after every update.
- Clicking anywhere on the card turns it over, including on content of the custom face slots.

## Related

- `tk:input`: the fields the card reads.
- `tk:form`: the form the card follows by default.
