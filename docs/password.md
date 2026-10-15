# Password

A password field with the usual defaults filled in: named `password`, labelled "Password", `autocomplete="current-password"`, the label as placeholder, and a button that shows and hides what was typed. It is a [`tk:input`](input.md) of type `password`.

## Usage

```blade
<tk:password />
```

A "Password" label over a masked input with "Password" as placeholder and an eye button at the end. Inside a Livewire component with a `$password` property, the field is bound to it. Password managers offer the saved password.

### New password

```blade
<tk:password name="password" autocomplete="new-password" label="New password" help="At least 8 characters." />
<tk:password name="password_confirmation" autocomplete="new-password" label="Confirm password" />
```

`autocomplete="new-password"` makes password managers offer a generated password instead of a saved one. Every default can be replaced on the tag.

## Props

### `tk:password`

Takes every prop of [`tk:input`](input.md#props) and the [field props](shared-props.md#field-props). Its defaults:

| Prop / attribute | Default |
|------------------|---------|
| `name` | `password` |
| `type` | `password` |
| `autocomplete` | `current-password` |
| `placeholder` | `true` (the label) |
| `viewable` | `true` |

## Slots

The slots of [`tk:input`](input.md#slots), such as `labelAppend` for a "Forgot your password?" link:

```blade
<tk:password>
    <x-slot:labelAppend>
        <tk:link href="/forgot-password" label="Forgot your password?" />
    </x-slot:labelAppend>
</tk:password>
```

## Customization

As [`tk:input`](input.md#customization), including `viewable:` for the show-password button.

```blade
<tk:password viewable:tooltip="Show password" input:class="font-mono" />
```

## Accessibility

A labelled `<input type="password">`. The show-password button has a name and `aria-pressed`, and switching it changes only the input's type, so the value and the focus stay.

## Behavior notes

- Old input from a failed submit never fills a password field.

## Related

- [Input](input.md): the input it is built on.
- [Page](page.md): the sign-in and sign-up screens that use it.
