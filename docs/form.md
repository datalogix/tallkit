# Form

A `<form>` that takes care of the wiring around the fields: the Livewire action or the URL, the CSRF token and method spoofing, a submit button, the session flash message, the error summary, focus on the first invalid field and a toast after submitting. `tk:form.card` and `tk:form.section` are the same form laid out inside a card or a titled section.

```
tk:form
tk:form.card
tk:form.section
```

## Usage

```blade
<tk:form wire:submit="save">
    <tk:input wire:model="name" label="Name" />
    <tk:input wire:model="email" label="Email" />
</tk:form>
```

```php
public string $name = '';

public string $email = '';

public function save(): void
{
    $this->validate(['name' => 'required', 'email' => 'required|email']);

    // ...
}
```

Two fields and a full-width "Send" button under them. Submitting with empty fields shows the validation messages under each field, moves the focus to Name, and shows an error toast. Fields with errors are cleared of their red state as soon as the form is sent again.

### Success message

```blade
<tk:form wire:submit="save" success-message="Profile saved.">
    <tk:input wire:model="name" label="Name" />

    <tk:submit label="Save" />
</tk:form>
```

After a submit without validation errors a success toast says "Profile saved.". The form already has a submit button, so it does not add its own.

### A classic form, without Livewire

```blade
<tk:form route="profile.update" method="put">
    <tk:input name="name" label="Name" />
    <tk:upload name="avatar" />
</tk:form>
```

A `<form method="POST">` to the `profile.update` route, with the CSRF token and `_method=PUT`. A file input inside makes it `multipart/form-data`. After a redirect back with errors, the page loads with the focus on the first invalid field, and a flashed `status` message shows as an alert at the top.

### In a card or a section

```blade
<tk:form.section
    wire:submit="save"
    title="Profile"
    description="How others see you on the site."
>
    <tk:input wire:model="name" label="Name" />
</tk:form.section>

<tk:form.card wire:submit="save" title="Password">
    <tk:input type="password" wire:model="password" label="New password" />
</tk:form.card>
```

The first is a section with the title and description beside the fields (above them on small screens); the second, the fields inside a card with the title in its header. Both behave as `tk:form`.

## Props

### `tk:form`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `action` | string | from `wire:submit` | Inside Livewire, the method called on submit (`wire:submit` is set for you, `submit` when there is neither). Outside it, the URL or route name the form posts to; the current URL by default. |
| `route` | string | `null` | A route name for the URL, outside Livewire. |
| `method` | string | `post` | `get`, `post`, `put`, `patch` or `delete`, outside Livewire. The last three are sent as `POST` with a `_method` field. |
| `enctype` | string | auto | The encoding. Set to `multipart/form-data` when the form holds a file input. |
| `submit` | bool | `true` | Adds a full-width "Send" button at the end when the form has no submit button of its own. `false` never adds it. |
| `alert` | bool \| slot | `true` | Shows the flashed session message (`status`) as an alert at the top. `false` removes it; a slot is the alert's body. |
| `errorGroup` | bool \| string | `false` | Shows a summary of all validation errors at the top. `only` shows only the summary, hiding the messages under the fields. |
| `errorBag` | string | `default` | The error bag the fields inside read their messages from. |
| `focusError` | bool | `true` | Moves the focus to the first invalid field after a failed submit, or on page load when there are errors. |
| `clearErrorsOnSubmit` | bool | `true` | Clears the error state of the fields when the form is sent, inside Livewire. |
| `toast` | bool \| string | `error` | Which toasts show after a Livewire submit: `error`, `success`, `true` for both, `false` for none. With `successMessage` the default is `true`. |
| `errorMessage` | string | `There was an error submitting the form.` | The text of the error toast. |
| `successMessage` | string | `Form submitted successfully.` | The text of the success toast; setting it also turns that toast on. |

### `tk:form.card`

Every prop of `tk:form`, plus the card's: `title`, `subtitle`, `description`, `header`, `footer`, `prepend`, `append`, `actions` and `size`. See [Card](card.md).

### `tk:form.section`

Every prop of `tk:form`, plus the section's: `title`, `subtitle`, `description`, `prepend`, `append`, `actions`, `separator` and `size`.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:form` | default | The fields and buttons. |
| `tk:form` | `alert` | The body of the session alert. |
| `tk:form.card`, `tk:form.section` | default | The fields and buttons. |
| `tk:form.card`, `tk:form.section` | `alert` | The body of the session alert. |

The card and section slots (`header`, `footer`, `actions`...) work as on those tags.

## Customization

Classes and attributes on `tk:form` go to the `<form>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `submit:` | `tk:form` | The added submit button (`submit:label="Save"`). |
| `alert:` | `tk:form`, `tk:form.card`, `tk:form.section` | The session alert (`alert:name="billing"` reads another key). |
| `error-group:` | `tk:form` | The error summary. |
| `card:` | `tk:form.card` | The card, and its own prefixes (`card:title:`...). |
| `section:` | `tk:form.section` | The section, and its own prefixes. |

```blade
<tk:form wire:submit="save" class="max-w-lg" submit:label="Save" submit:variant="primary" alert:name="profile">
    <tk:input wire:model="name" label="Name" />
</tk:form>
```

## Accessibility

A native `<form>` with a native submit button, so Enter in a field submits it. After a failed submit the focus moves to the first invalid field, scrolled into the middle of the screen (without smooth scrolling when the user prefers reduced motion), where its error is announced through `aria-describedby`.

## Behavior notes

- Inside Livewire, the error and success toasts follow the submit action only: other requests made by the page do not show them.
- A submit counts as failed when the response has validation errors or any field inside is marked invalid.
- The "Send" button is added only when the form has an action and no element with `type="submit"` in its content.
- Outside Livewire, `GET` forms carry no CSRF token.

## Related

- `tk:submit`: the submit button the form adds, to place it yourself.
- [Alert](alert.md): `tk:alert.session` is the flash message at the top.
- [Error](error.md): `tk:error.group` is the summary shown with `errorGroup`.
- [Fieldset](fieldset.md): groups fields under a title inside a form.
