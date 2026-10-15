# Error

A validation message in red with an alert icon, read from Laravel's error bag by field name. Every form field already shows its own, so this tag is for messages placed by hand: under a custom control, or a summary of all errors at the top of a form.

```
tk:error
tk:error.group
```

`tk:error` shows one field's message; `tk:error.group` lists every message of the bag in a red alert.

## Usage

```blade
<tk:error name="email" />
```

Nothing while `email` has no validation error. After a failed validation, a red line with an alert icon and the first message for `email`, e.g. "The email field is required.". It disappears again when the error is cleared.

### Fields with nested names

```blade
<tk:error name="items[]" />
<tk:error name="address[city]" />
```

The name is written as in the input's `name` attribute and converted to the error key: `address[city]` reads `address.city`. When the name has no message of its own, the first message of its children is shown, so `items[]` (or `items`) shows the error of `items.0`, `items.1`...

### A summary of all errors

```blade
<form wire:submit="save">
    <tk:error.group title="Please fix the following" />

    <tk:input name="email" />
    <tk:input name="password" type="password" />
</form>
```

Nothing while the bag is empty. After a failed validation, a red alert with the title and a list of every message, from every field. `tk:form` adds the same alert with its `errorGroup` prop.

### A message of your own

```blade
<tk:error message="The file is too large." icon="file-alert-outline" />

<tk:error>
    Your session expired. <a href="/login" class="underline">Sign in again</a>.
</tk:error>
```

Without `name`, the message is the `message` prop or the slot content, shown in the same style.

## Props

### `tk:error`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The field whose first validation message is shown. `field[key]` and `field[]` are converted to dot keys. |
| `message` | string | from the bag | The message. When given, the bag is not read. |
| `bag` | string | `default` | The error bag to read. Inside a `tk:form` with `errorBag`, that bag. |
| `icon` | string \| false | `alert-outline` | The icon before the message. `false` removes it. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font and icon size. |

### `tk:error.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `bag` | string | `default` | The error bag to list. |
| `title` | string \| slot | `null` | The heading of the alert. |
| `prepend` | slot | `null` | Content before the title. |
| `append` | slot | `null` | Content after the list. |
| `actions` | slot | `null` | Controls at the end of the row. |

Every other attribute is passed to the [`tk:alert`](alert.md) it renders (`closable`, `variant`, `border`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:error` | default | The message, when there is no `name` and no `message`. |

## Customization

Classes and attributes on `tk:error` go to its `<div>`; the icon takes them through a prefix. `tk:error.group` takes the prefixes of [`tk:alert`](alert.md#customization).

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon:` | `tk:error` | The icon. |

```blade
<tk:error name="email" class="mt-1" icon:class="text-red-700" />
```

## Accessibility

`tk:error` is a polite live region (`role="status"`, `aria-live="polite"`); `tk:error.group` is an alert (`role="alert"`). The fields link their own message to the control with `aria-describedby` and set `aria-invalid`; a `tk:error` placed by hand is not linked to anything, so give it an `id` and point the control's `aria-describedby` to it.

## Behavior notes

- Only the first message of the field is shown; `tk:error.group` shows all of them.
- Nothing is rendered while there is no message, so the element is added to the page when the error appears.
- Inside Livewire the messages come from the component's error bag, so they show after `$this->validate()` or `addError()` without a page reload.

## Related

- [Alert](alert.md): the box `tk:error.group` is built on, for messages that are not validation errors.
- `tk:form`: shows the summary for you with `errorGroup`.
- `tk:field`: the wrapper that gives each form control its label, help text and error.
