# Google

Tags for Google services in the page `<head>`: Google Fonts, Google Analytics (gtag.js), Google Tag Manager and Consent Mode. [`tk:html`](#related) already places them from its props, so most apps never write these tags; use them directly in a layout of your own.

```
tk:google.fonts
tk:google.gtag
tk:google.gtm
tk:google.consent
```

## Usage

```blade
<head>
    <tk:google.fonts :families="['Inter:wght@400;600', 'JetBrains+Mono']" display="swap" />
    <tk:google.consent />
    <tk:google.gtag />
</head>
```

The fonts stylesheet is added by a small script, so it does not block the first paint, with `dns-prefetch` and `preconnect` hints for Google's font servers. The consent tag sets every Google consent signal to denied until the user decides. The gtag tag loads Google Analytics with the id from `config('services.google.gtag')`; with no id configured it renders nothing.

### Google Tag Manager

```blade
<head>
    <tk:google.consent />
    <tk:google.gtm id="GTM-XXXXXXX" />
</head>
<body>
    <tk:google.gtm id="GTM-XXXXXXX" noscript />
    ...
</body>
```

The first loads the container script in the `<head>`; the second is the `<noscript>` iframe Google asks for right after `<body>` opens. Without `id`, both read `config('services.google.gtm')`.

### Recording the user's consent

```blade
<tk:button x-on:click="$tallkit.consent('granted')" label="Accept cookies" />
<tk:button x-on:click="$tallkit.consent('denied')" label="Reject" />
```

`consent()` updates Google's consent signals, stores the choice in the browser and dispatches `tallkit:consent` on the document with `{ state }`. On the next pages `tk:google.consent` reads the stored choice and applies it before any Google tag runs, so the banner does not have to ask again.

### Fonts with a stylesheet link

```blade
<tk:google.fonts families="https://fonts.googleapis.com/css2?family=Inter&display=swap" use-stylesheet />
```

`families` also takes a full Google Fonts URL. `use-stylesheet` writes a plain `<link rel="stylesheet">` instead of the script, for pages with a strict Content Security Policy or no JavaScript.

## Props

### `tk:google.fonts`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `families` | array \| string | `null` | The families, as in the Google Fonts URL (`Inter:wght@400;700`), or a full Google Fonts URL. |
| `display` | string | `null` | The `font-display` value (`swap`, `optional`...). With `preload` and no `display`, `swap`. |
| `prefetch` | bool | `true` | Adds a `dns-prefetch` hint for the font files. |
| `preconnect` | bool | `true` | Adds `preconnect` hints for both Google font servers. |
| `preload` | bool | `false` | Adds a `preload` hint for the stylesheet. |
| `useStylesheet` | bool | `false` | Writes a `<link rel="stylesheet">` in place of the script that adds it. |
| `noscript` | bool | `false` | Renders only a `<noscript>` with the stylesheet link, for the end of the page. |
| `nonce` | string | `null` | The CSP nonce for the script. |

### `tk:google.gtag`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string \| bool | `true` | The measurement id (`G-XXXXXXX`). `true` reads `config('services.google.gtag')`; with no id nothing is rendered. |
| `nonce` | string | `null` | The CSP nonce for the scripts. |

### `tk:google.gtm`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string \| bool | `true` | The container id (`GTM-XXXXXXX`). `true` reads `config('services.google.gtm')`; with no id nothing is rendered. |
| `noscript` | bool | `false` | Renders the `<noscript>` iframe in place of the script. |
| `nonce` | string | `null` | The CSP nonce for the script. |

### `tk:google.consent`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `nonce` | string | `null` | The CSP nonce for the script. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `document` | `tallkit:consent` | `{ state }`, `granted` or `denied` | `tallkit.consent()` was called. |

With Alpine:

```blade
<div x-data="{ asked: true }" x-on:tallkit:consent.document="asked = false">...</div>
```

## Behavior notes

- `tk:google.consent` must come before `tk:google.gtag` and `tk:google.gtm`, so Google reads the default (denied) state before it starts. It renders once per page however many times it is written.
- The consent defaults deny ad storage, ad user data, ad personalization and analytics storage, and wait 500 ms for an update. `consent('granted')` grants all four; any other value denies them.
- The stored choice is kept in `localStorage`; when storage is blocked, the defaults stay denied on each page.
- Without `families`, `tk:google.fonts` requests a URL with no family; always pass at least one.

## Related

- `tk:html`: the page shell, which places these tags from its `googleFonts`, `gtag`, `gtm` and `consent` props.
