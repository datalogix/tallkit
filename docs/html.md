# Html

The whole page shell, from `<!DOCTYPE html>` to `</html>`: language and text direction, the usual meta tags, favicon, Vite assets, the light and dark mode script, Google tags, the toast area and TALLKit's scripts. Use it as the root of every layout, with the page content in the slot.

```
tk:html
tk:html.meta
```

`tk:html.meta` renders the SEO and social sharing tags (description, Open Graph, Twitter card); `tk:html` places it when `meta` is set.

## Usage

```blade
<tk:html title="Dashboard">
    <tk:main>
        Page content
    </tk:main>
</tk:html>
```

A page in the app's language (`<html lang="pt-BR">`, `dir="rtl"` for Arabic, Persian, Hebrew and Urdu), with charset, viewport, CSRF token, the favicon found in `public/`, the title, and `resources/css/app.css` and `resources/js/app.js` through Vite when they exist. The body has a white background (near black in dark mode) and the base text color. At the end of the body come the toast area and TALLKit's scripts.

### SEO and sharing

```blade
<tk:html
    :title="$post->title"
    :meta="['description' => $post->excerpt, 'image' => $post->cover_url, 'type' => 'article']"
>
    ...
</tk:html>
```

`meta` adds the description, keywords, author, robots, a canonical link without tracking parameters (`utm_*`, `gclid`...), and the Open Graph and Twitter card tags, taking the title from `title`. `meta` set to `true` uses only the defaults from `config('app.*')`.

### Fonts, analytics and extra assets

```blade
<tk:html
    google-fonts="Inter:wght@400;600"
    gtag="G-XXXXXXX"
    :styles="['https://cdn.example.com/widget.css']"
    :scripts="['https://cdn.example.com/widget.js']"
    theme-color="#0ea5e9"
>
    ...
</tk:html>
```

Google Fonts with the Inter family, Google Analytics with Consent Mode (every signal denied until `tallkit.consent()` is called, see [Google](google.md)), an extra stylesheet in the `<head>`, an extra script at the end of the `<body>`, and the browser's theme color.

### Your own head content

```blade
<tk:html>
    <x-slot:head>
        <link rel="manifest" href="/manifest.json">
    </x-slot:head>

    ...
</tk:html>
```

The `head` slot goes into the `<head>`, after the title. `@push('styles')` and `@push('scripts')` from any view also land in the `<head>` and at the end of the `<body>`.

## Props

### `tk:html`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `config('app.name')` | The page title. |
| `lang` | string | the app's locale | The page language, also used for `dir`. |
| `charset` | string \| false | `utf-8` | The charset meta tag. |
| `viewport` | string \| false | `width=device-width, initial-scale=1` | The viewport meta tag. |
| `favicon` | string \| bool | `true` | The favicon URL. `true` looks for `favicon.ico`, `favicon.svg` or `favicon.png` in `public/`. |
| `themeColor` | string | `null` | The `theme-color` meta tag. |
| `csrfToken` | bool | `true` | The `csrf-token` meta tag, when the session is started. |
| `metaTags` | array | `[]` | More `<meta name="..." content="...">` tags, as name => content. |
| `meta` | bool \| array | `false` | Renders `tk:html.meta`. An array is passed to it as props. |
| `vite` | array | `resources/css/app.{css,scss,sass}`, `resources/js/app.{js,ts}` | The Vite entry points; only the files that exist are used. |
| `viteBuildDirectory` | string | `build` | The Vite build directory. |
| `googleFonts` | string \| array | `null` | The Google Fonts families (a string), or the props of `tk:google.fonts`. |
| `gtag` | string \| bool | `true` | The Google Analytics id. `true` reads `config('services.google.gtag')`. |
| `gtm` | string \| bool | `true` | The Google Tag Manager id. `true` reads `config('services.google.gtm')`. |
| `consent` | bool | `true` | Adds Google Consent Mode, with every signal denied by default, when there is a gtag or GTM id. |
| `typekit` | string | `null` | An Adobe Fonts (Typekit) kit id. |
| `styles` | array | `[]` | Stylesheet URLs for the `<head>`. |
| `scripts` | array | `[]` | Script URLs for the end of the `<body>`. |
| `stackStyles` | string \| false | `styles` | The Blade stack rendered in the `<head>`. |
| `stackScripts` | string \| false | `scripts` | The Blade stack rendered at the end of the `<body>`. |
| `components` | array | `[]` | Component names rendered at the end of the `<body>`, such as global modals. |
| `toast` | bool | `true` | Renders the toast area. Inside a Livewire app it persists across `wire:navigate` and shows flashed toasts. |
| `livewire` | bool | auto | Whether the app uses Livewire; detected when not given. |
| `appearance` | bool \| string | `true` | Renders the light and dark mode script. A string is used as its CSP nonce. |
| `nonce` | string | Vite's CSP nonce | The CSP nonce for the inline scripts and the tags it renders. |

### `tk:html.meta`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `config('app.name')` | The title for search engines and sharing. |
| `description` | string | `config('app.description')` | The page description. |
| `keywords` | string | `config('app.keywords')` | The keywords meta tag. |
| `author` | string | `config('app.author')` | The author meta tag. |
| `robots` | string | `index, follow` | The robots meta tag. |
| `canonical` | string \| bool | `true` | The canonical URL. `true` uses the current URL with its query string, minus tracking parameters. |
| `url` | string | the current URL | The URL for Open Graph and Twitter. |
| `image` | string | `meta-image` in `public/` | The sharing image. Looks for `meta-image.png`, `.jpg` or `.jpeg` in `public/`, `public/imgs` and `public/images`. |
| `type` | string | `website` | The Open Graph type. |
| `card` | string | `summary_large_image` | The Twitter card type. |
| `locale` | string | the app's locale | The Open Graph locale. |
| `siteName` | string | `config('app.name')` | The Open Graph site name. |
| `twitterSite` | string | `null` | The site's Twitter handle. |
| `twitterCreator` | string | `null` | The author's Twitter handle. |

Every tag whose value is empty is left out.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:html` | default | The page content, in the `<body>`. |
| `tk:html` | `head` | Extra content for the `<head>`. |

## Customization

Classes and attributes on the tag go to the `<body>`. To reach the other parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `html:` | `tk:html` | The `<html>` element (`html:class="scroll-smooth"`). |
| `head:` | `tk:html` | The `<head>` element. |
| `meta:` | `tk:html` | The `tk:html.meta` props. |
| `google-fonts:` | `tk:html` | The `tk:google.fonts` props (`google-fonts:display="swap"`). |
| `toast:` | `tk:html` | The toast area (`toast:class="z-50"`). |
| `components:` | `tk:html` | Attributes passed to every component listed in `components`. |

```blade
<tk:html class="bg-zinc-50" html:class="scroll-smooth" toast:class="z-50">
    ...
</tk:html>
```

## Accessibility

`lang` and `dir` are always set, from the app's locale, so screen readers pick the right voice and the layout mirrors for right-to-left languages.

## Behavior notes

- Vite assets are included only when the dev server is running or the build manifest exists, so a page renders even before the first build.
- With GTM and `consent`, the `<noscript>` GTM iframe is left out, since it would track without consent.
- The `dir` is `rtl` for the locales in `config('app.rtl_locales')`, by default Arabic, Persian, Hebrew and Urdu.

## Related

- [Appearance](appearance.md): the light and dark mode controls that work with the script it renders.
- [Google](google.md): the Google tags it places.
- `tk:layout`: ready-made page layouts with header and sidebar.
