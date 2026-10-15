# Display

A read-only value laid out like a form field: label, the value in a box with the same size and border as an input, help text and validation error. Use it on show pages and summaries, or in a form to show a value the user cannot change, such as a calculated total.

## Usage

```blade
<tk:display label="Total" value="R$ 100,00" />
```

The label sits above a box with the text "R$ 100,00", styled like a `tk:input` but with nothing to type into.

### Bound to a Livewire property

```blade
<tk:display wire:model="total" label="Total" help="Calculated from the items." />
```

```php
public string $total = 'R$ 10,00';
```

The box shows `'R$ 10,00'` and follows the property: when `$total` changes to `'R$ 30,00'`, the text changes with it. Inside a Livewire component a `name` alone does the same when the component has a public property with that name, and the label comes from the name:

```blade
<tk:display name="status" />
```

This shows the `$status` property under the label "Status". `:wire-model="false"` turns that binding off.

### Custom content

```blade
<tk:display label="Status">
    <tk:badge color="green">Paid</tk:badge>
</tk:display>
```

The default slot is the value when there is no `value` and no bound property, and it takes any markup: here a green "Paid" badge inside the box.

### Rendering from data

```blade
<div class="grid gap-4">
    @foreach ($lines as $line)
        <tk:display :label="$line['label']" :value="$line['amount']" wire:key="line-{{ $line['id'] }}" />
    @endforeach
</div>
```

One labeled box per line of the order, such as "Subtotal: R$ 90,00" and "Shipping: R$ 10,00".

### Icon and decorations

```blade
<tk:display label="Website" value="example.com" icon="globe" icon-trailing="external-link" />
```

The globe icon sits at the start of the box and the second icon at the end, as on a `tk:input`. The [field control props](shared-props.md#field-control-props) (`prepend`, `append`, `kbd`, `loading`) work the same way.

## Props

Accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `iconTrailing`, `prepend`...). For a display:

- `value` is the text shown in the box, as is: old input from a failed submit does not replace it. `0` is shown.
- `placeholder` and `color` have no effect.

The text shown is the first one present: `value`, then the property bound through `wire:model` (or through `name`), then the default slot. `wire:model` and `x-model` bind any value that prints as text, and the box follows it as it changes in the browser. Use `value` or a binding, not both: in the browser the binding overwrites the text that `value` rendered.

## Slots

| Slot | Description |
|------|-------------|
| default | The value, used when there is no `value` and no bound property. |

## Customization

Classes on the tag go to the box. Other attributes go to the `<output>` element that holds the value. The parts around it take attributes through the prefixes of the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props), and one more:

| Prefix | Goes to |
|--------|---------|
| `display:` | The `<output>` element. Only `display:class` is used. |

```blade
<tk:display label="Total" value="R$ 100,00" class="bg-zinc-50" display:class="font-semibold tabular-nums" />
```

With a `class`, the box is a wrapper around the `<output>`; without one, and without icons or other decorations, the `<output>` is the box itself. `display:class` always reaches the `<output>`.

## Accessibility

The value is an `<output>` element. The label points to it with `for`, and `aria-describedby` links it to the description, the help text and the validation error. With a validation error on its name, or with `invalid`, it gets `aria-invalid="true"` and a red border.

| Key | Action |
|-----|--------|
| Tab | Moves focus to the value, which shows the focus ring, so keyboard users can reach and read it. |

## Behavior notes

- The `value` and the bound property are escaped: HTML in them is shown as text. The slot is not.
- Line breaks in the value are kept and long words wrap inside the box, so a multiline note shows as written. In the slot this includes the indentation of the Blade source.
- An `<output>` is not sent with a form even when it has a `name`. To submit the value, add a hidden input.
- Bound with `wire:model` or `x-model`, the text changes as soon as the property changes in the browser, before any request to the server.
- Without a `label`, the label is derived from the name or the `wire:model` (`order.number` gives "Number") and translated; `:label="false"` removes it.

## Related

- `tk:input`: when the user should be able to edit the value, or a `readonly` input when it must be submitted with the form.
- [Copyable](copyable.md): a button that copies a value to the clipboard.
- `tk:text`: plain text with no label or box around it.
