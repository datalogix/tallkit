# OTP

A one-time code field: one box per character, with the focus moving on as the user types, the whole code pasted at once, and the code filled in by the phone from an SMS. The value is the joined code, in a hidden field and in the bound property.

```
tk:otp
├── tk:otp.group
│   └── tk:otp.input ...
└── tk:otp.separator
```

`tk:otp` builds the boxes from `format`; the inner tags are written only for a custom layout.

## Usage

```blade
<tk:otp wire:model="code" label="Verification code" />
```

```php
public string $code = '';
```

Six boxes for digits under the label. Typing a digit fills the box and moves to the next; letters are ignored. Pasting `123456` in any box fills them all. On phones the numeric keyboard opens and the code from an SMS is offered above it. `$code` receives the joined value, `'123456'`.

### Format and characters

```blade
<tk:otp name="code" format="999-999" />
<tk:otp name="ref" charset="alpha" format="AAA" />
<tk:otp name="key" format="****-****" />
```

`format` sets the number of boxes and what each accepts: `9` a digit, `A` a letter, `*` either, and `-` a separator between groups, drawn as a dash. Letters are shown and stored in upper case. `charset` sets the default format of six boxes: `numeric`, `alpha` or `alphanumeric`.

### Submitting when complete

```blade
<form wire:submit="verify">
    <tk:otp wire:model="code" submit="auto" />
</form>

<tk:otp name="pin" submit="pinEntered" private format="9999" />
```

With `submit="auto"`, the form is submitted as soon as the last box is filled. Any other string is the name of a Livewire event dispatched with the code, for a `#[On('pinEntered')]` method. `private` masks the characters like a password.

## Props

### `tk:otp`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `format` | string | six of the `charset` | The boxes: `9` digit, `A` letter, `*` letter or digit, `-` separator between groups. |
| `charset` | string | `numeric` | `numeric`, `alpha` or `alphanumeric`, for the default format and the keyboard. |
| `private` | bool | `false` | Hides the characters, as a password. |
| `submit` | string | `null` | `auto` submits the form when the code is complete; another string is a Livewire event dispatched with the code. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `placeholder`...). `placeholder` is shown in every box (`placeholder="0"`). `wire:model` and `x-model` bind the code as a string.

### `tk:otp.input`, `tk:otp.group` and `tk:otp.separator`

One box (`charset`, `private`, inherited from the code), a group of boxes joined together, and the dash between groups (`label` changes it).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:otp` | default | Boxes, groups and separators written by hand, in place of `format`. |

## Events

Events are dispatched on the first box (`focused` and `blurred` on the box concerned), do not bubble, and are listened to on the tag.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:otp` | `changed` | `{ value }` | The user changed any box. |
| `tk:otp` | `completed` | `{ value }` | Every box is filled. |
| `tk:otp` | `incomplete` | `{ value }` | A change left some box empty. |
| `tk:otp` | `cleared` | | Every box is empty. |
| `tk:otp` | `pasted` | `{ pasted, index }` | Text was pasted. |
| `tk:otp` | `focused`, `blurred` | `{ input, index }` | A box gained or lost focus. |

With Alpine:

```blade
<tk:otp name="code" x-on:completed="ready = true" x-on:incomplete="ready = false" />
```

Calling a Livewire action:

```blade
<tk:otp wire:model="code" x-on:completed="$wire.verify($event.detail.value)" />
```

## Customization

Classes on the tag go to the row of boxes; other attributes go to each box. The parts around take the prefixes of the [field props](shared-props.md#field-props), and:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `hidden:` | `tk:otp` | The hidden input that holds the code. |
| `input:` | `tk:otp` | Classes for each box (`input:class="font-mono"`). |

```blade
<tk:otp name="code" size="lg" input:class="font-mono text-xl" />
```

## Accessibility

The boxes are a `role="group"` named by the label (or "One-time passcode"), and each box is named "Digit 1 of 6"... The first box has `autocomplete="one-time-code"`, so browsers and phones offer the code received by SMS. The direction is always left to right.

| Key | Action |
|-----|--------|
| A character | Fills the box and moves to the next. |
| Backspace | Clears the box; on an empty box, moves to the previous one. |
| Arrow Left / Arrow Right | Moves to the previous / next box. |

## Behavior notes

- Focusing an empty box moves the focus to the first empty one, so the code is always typed in order.
- The automatic submit or event happens once per complete code: changing a character and completing it again fires it again.
- A value set on the server fills the boxes.
- The row has `wire:ignore`: a Livewire update does not reset what is being typed.

## Related

- [Input](input.md): a single text field, with masks.
- `tk:password`: a password field with strength rules.
- `tk:two-factor`: the two-factor authentication screens that use this field.
