# Terms

The "I agree to the terms" checkbox of a sign-up form, with links to the Terms of Service, Terms of Use and Privacy Policy that the app has. The sentence is built from the documents found and translated.

```
tk:terms.acceptance
```

## Usage

```blade
<tk:terms.acceptance name="terms" required />
```

With routes named `terms-of-service` and `privacy-policy`, a checkbox labelled "I agree to the Terms of Service and Privacy Policy", where each document name is a link opening in a new tab. Without any of those routes, "I accept the terms and conditions". `required` stops the form from being sent unchecked.

### Your own links

```blade
<tk:terms.acceptance name="terms" terms-of-use="https://example.com/terms" privacy-policy="https://example.com/privacy" />
```

"I agree to the Terms of Use and Privacy Policy", linking to those URLs.

### As a toggle

```blade
<tk:terms.acceptance wire:model="accepted" toggle />
```

The same sentence in a toggle button instead of a checkbox.

## Props

### `tk:terms.acceptance`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `termsOfService` | string | the `terms-of-service` route | The Terms of Service URL. |
| `termsOfUse` | string | the `terms-of-use` route | The Terms of Use URL. |
| `privacyPolicy` | string | the `privacy-policy` route | The Privacy Policy URL. |
| `toggle` | bool | `false` | Renders a `tk:toggle` instead of a [`tk:checkbox`](checkbox.md). |

The routes are also looked up with the `terms.` prefix (`terms.privacy-policy`). Every other attribute goes to the checkbox (or toggle): `name`, `wire:model`, `required`, `color`, `help`...

## Customization

As [`tk:checkbox`](checkbox.md#customization).

```blade
<tk:terms.acceptance name="terms" required size="sm" help="You can withdraw your consent at any time." />
```

## Accessibility

A labelled native checkbox; the links inside the label are reachable with Tab and open in a new tab with `rel="noopener noreferrer"`.

## Behavior notes

- The sentence has a translation for each combination of documents, so it reads naturally in other languages.

## Related

- [Checkbox](checkbox.md): the checkbox it renders.
- [Page](page.md): `tk:page.auth.sign-up` places it in the sign-up screen.
