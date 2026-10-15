# Two-factor

The pieces of a two-factor authentication setup screen: the QR code with the secret to type in an authenticator app, and the list of recovery codes with a copy button. [`tk:page.auth.two-factor-setup`](page.md) puts them together; use them directly in a screen of your own.

```
tk:two-factor.qr
tk:two-factor.recovery-codes
```

## Usage

```blade
<tk:two-factor.qr :svg="$qrSvg" :secret="$secret" :uri="$otpauthUri" />
```

The QR code centered, and under it, side by side on wide screens, "Use this secret in your authenticator app:" with the secret in a box and a "Copy secret" button, and "OTPAuth URI (for QR generation):" with the URI and a "Copy URI" button. All texts are translated.

### Recovery codes

```blade
<tk:two-factor.recovery-codes :codes="$user->recoveryCodes()" />
```

"Recovery codes (store them safely):", the codes one per line in a monospaced font, and a "Copy codes" button that copies all of them, one per line.

## Props

### `tk:two-factor.qr`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `svg` | string | `null` | The QR code SVG markup, rendered as is. |
| `secret` | string | `null` | The secret key, shown with a copy button. |
| `uri` | string | `null` | The `otpauth://` URI, shown with a copy button. |

Each part is left out when its value is empty.

### `tk:two-factor.recovery-codes`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `codes` | array | `[]` | The recovery codes. With none, nothing is rendered. |

## Customization

Classes and attributes go to the outer element. The parts take prefixes:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `qr:` | `tk:two-factor.qr` | The QR code. |
| `secret:label:`, `secret:value:`, `secret:copy:` | `tk:two-factor.qr` | The secret's text, box and copy button (`uri:` the same for the URI). |
| `label:`, `list:`, `copy:` | `tk:two-factor.recovery-codes` | The text, the list and the copy button. |

```blade
<tk:two-factor.qr :svg="$qrSvg" :secret="$secret" qr:class="p-4 bg-white rounded-lg" secret:copy:label="Copy" />
```

## Accessibility

The QR code is an image named "QR code for authenticator app setup", and the secret is shown as text, for users who cannot scan it. The copy buttons announce when the text was copied.

## Behavior notes

- The SVG is rendered as given: pass only markup your app generated.

## Related

- [Page](page.md): `tk:page.auth.two-factor-setup` and `tk:page.auth.two-factor`, the full screens.
- [OTP](otp.md): the code field.
- [Copyable](copyable.md): the copy buttons.
