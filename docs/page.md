# Page

Page-level building blocks: `tk:page`, a titled page with a side menu of sections (a settings area, for instance); `tk:page.error`, the content of an error page; and `tk:page.auth.*`, ready-made sign-in, sign-up and account screens with the fields, texts and links already in place.

```
tk:page
tk:page.error
tk:page.auth.login, .sign-up, .forgot-password, .reset-password, ...
```

## Usage

```blade
<tk:page
    title="Settings"
    subtitle="Manage your account."
    :menu="[
        ['label' => 'Profile', 'href' => '/settings/profile'],
        ['label' => 'Password', 'href' => '/settings/password'],
        ['label' => 'Notifications', 'href' => '/settings/notifications'],
    ]"
>
    <tk:form wire:submit="save">...</tk:form>
</tk:page>
```

A titled page with a vertical menu on the left, 220px wide, the current section highlighted, and the content beside it. Below `md`, the menu turns into a select at the top that navigates when changed.

### An error page

```blade
{{-- resources/views/errors/404.blade.php --}}
<tk:html>
    <tk:layout.error>
        <tk:page.error code="404" />
    </tk:layout.error>
</tk:html>
```

A large, faint "404" behind the text "We couldn't find that page" and a short explanation, with a button back to the home page. The texts change with the code (400, 401, 403, 419, 429, 500, 502, 503, 504) and are translated.

### Sign-in screens

```blade
<tk:layout.auth>
    <tk:page.auth.login wire:submit="login" :oauth="['google', 'github']" />
</tk:layout.auth>
```

"Sign in to your account" with email and password fields, a "Forgot your password?" link, Remember me, a full-width Sign in button, buttons to continue with Google and GitHub, and a link to sign up. The links appear when routes with the usual names exist (`forgot-password`, `register`...). Every screen is a [`tk:form.section`](form.md), so `wire:submit`, `action` or `route` decide where it is sent.

## Props

### `tk:page`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `menu` | array | `null` | The section links, as `tk:nav` items (`label`, `href` or `route`, `icon`...). |
| `breakpoint` | string | `md` | From this breakpoint up the menu is a side column; below it, a select. `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |

Also accepts the props of `tk:section` (`title`, `subtitle`, `description`, `size`...).

### `tk:page.error`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `code` | int \| string | `null` | The HTTP status, shown large behind the text and choosing the default texts. |
| `title` | string | by code | The heading. |
| `description` | string | by code | The explanation under it. |
| `homeUrl` | string | the `home` route, or `/` | The target of the "home" button. |
| `size` | string | `md` | The text size. |

### `tk:page.auth.*`

| Tag | Shows | Props |
|-----|-------|-------|
| `login` | Identifier, password, remember me, sign in, OAuth buttons, sign-up link. | `identifier`, `oauth`, `forgotPasswordUrl`, `signUpUrl`, `emailLoginUrl`, `size` |
| `sign-up` | Name, identifier, email (when the identifier is not one), password and confirmation, the fields of its slot, terms checkbox, OAuth buttons, sign-in link. | `identifier`, `requiresEmail`, `terms`, `oauth`, `loginUrl`, `size` |
| `forgot-password` | Identifier and a "send link" button. | `identifier`, `loginUrl`, `size` |
| `reset-password` | The token, identifier, new password and confirmation. | `token`, `identifier`, `identifierValue`, `size` |
| `confirm-password` | The password, for a secure area. | `oauth`, `size` |
| `verify-email` | A "resend" button and a log-out link. | `logoutUrl`, `size` |
| `accept-terms` | Each document to accept, its content in a scrolling box, its PDF in a frame or a link to it, with a checkbox of its own; a "continue" button and a log-out link. | `documents` (`key`, `label`, `url`, `html`, `pdf`), `logoutUrl`, `size` |
| `email-login` | Identifier to receive a sign-in link or code (`mode`: `link`, `code`, `both`); with `confirmEmail`, a confirmation step; with `codeStep`, the code field. | `identifier`, `mode`, `codeStep`, `confirmEmail`, `loginUrl`, `size` |
| `two-factor` | The code (and a recovery code toggle); for email and SMS, a resend button. | `method` (`totp`, `email`, `sms`), `resendUrl`, `size` |
| `two-factor-setup` | Enable or disable two-factor, the QR code and secret, recovery codes and trusted devices. | State: `enabled`, `method`, `secret`, `uri`, `qrSvg`, `secretUnreadable`, `canDisable`, `awaitingContinue`, `recoveryCodes`, `recoveryCodesCount`, `canManageRecoveryCodes`, `trustedDevices`. Names of the Livewire methods it calls: `prepare`, `enable`, `disable`, `continue`, `regenerate`, `revokeDevice`, `revokeAllDevices`. |
| `accept-invitation` | New password and confirmation; or, by `status`, an expired or used invitation. | `email`, `status`, `loginUrl`, `size` |
| `profile` | With `avatar`, a photo; the name, then a language and a time zone when given, then the slot, for fields of your own. | `avatar`, `avatarDisk`, `avatarDirectory`, `avatarMaxSize`, `locales`, `timezones` (nested arrays are groups), `size` |
| `change-email` | The current address and a new one. | `currentEmail`, `size` |
| `change-password` | Current password (with `currentPassword`), new password and confirmation. | `currentPassword`, `reason`, `logoutUrl`, `size` |
| `delete-account` | A warning and a field to type a confirmation word. | `confirmation`, `size` |
| `data-export` | A button to download, or e-mail with `queued`, the account's data. | `queued`, `size` |
| `oauth-complete-registration` | The identifier missing after an OAuth sign-up. | `provider`, `email`, `identifier`, `size` |
| `account` | A list of links to the account screens. | `items` (`label`, `url`, `danger`) |
| `sessions`, `devices` | A table of signed-in devices with sign-out buttons. | `items`, `signOut` / `revoke`, `revokeAll` |
| `connections` | Linked OAuth accounts with link and unlink buttons. | `items`, `unlink` |
| `activity` | A list of recent account events. | `items` |
| `oauth` | "Continue with" buttons for the configured providers. | `items`, `route`, `label`, `separator`, `error` |
| `identifier` | The identifier field alone. | `identifier`, `size` |

`identifier` is the sign-in field: `email` (default), `username`, `login`, `cpf` or `cnpj`; it sets the field's name, label, placeholder, mask and autocomplete, and any of them given with the `identifier:` prefix takes their place (`identifier:name="registration" identifier:label="Registration"`). `oauth` takes provider names; without it, the providers with a `client_id` in `config('services')` are shown. The URL props default to routes with the usual names when they exist (`login`, `logout`, `forgot-password`, `register`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:page` | default | The content beside the menu. |
| `tk:page` | `header`, `actions` | Content beside the title, and actions at the end of the title row. |
| `tk:page.auth.*` (forms) | default | Content at the top of the form, before the fields. |

## Customization

Classes and attributes on `tk:page` go to its section. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:page` | The row holding the menu and the content. |
| `menu:`, `nav:`, `select:` | `tk:page` | The menu column, the nav in it, and the select shown on small screens. |
| `area:` | `tk:page` | The content area. |
| `code:`, `content:`, `home:` | `tk:page.error` | The large code, the texts and the home button. |

Each field and button of an auth screen takes a prefix with its name: `identifier:`, `password:`, `submit:`, `remember:`, `forgot-password:`, `sign-up:`, `oauth:`...

```blade
<tk:page.auth.login wire:submit="login" submit:label="Enter" submit:variant="primary" password:help="At least 8 characters." />
```

## Accessibility

The screens are forms of labelled fields with the right `autocomplete` tokens (`email`, `username`, `current-password`, `new-password`, `one-time-code`), so browsers and password managers fill them. The large code on the error page is hidden from screen readers.

## Behavior notes

- The texts of every screen are translated, and each one can be changed with its prefix (`submit:label`...) or with `title` and `subtitle` on the tag.
- The auth screens do not authenticate anyone: they post their fields to your action, with the names Laravel's own auth features expect (`email`, `password`, `password_confirmation`, `remember`, `token`, `code`...).

## Related

- [Layout](layout.md): `tk:layout.auth` and `tk:layout.error` around these screens.
- [Form](form.md): the form the auth screens are built on.
- [OTP](otp.md): the code field of the two-factor screens.
- `tk:two-factor`: the parts of the two-factor setup.
