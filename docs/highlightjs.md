# Highlight.js

A block of source code with syntax highlighting by [highlight.js](https://highlightjs.org). It loads the library and a GitHub-style theme on demand, switches the theme with the light and dark color scheme, and detects the language when none is given.

## Usage

```blade
<tk:highlightjs language="php" :code="$snippet" />
```

A spinner while the library loads, then the code in a `<pre><code>` block, colored as PHP. The code is shown as text: HTML inside it is never rendered.

### Code in the slot

```blade
<tk:highlightjs>
&lt;button class="btn"&gt;Save&lt;/button&gt;
</tk:highlightjs>
```

The slot is the code. Blade would render HTML written there as elements, so escape it (`&lt;`, `&gt;`, `&amp;`): the entities are decoded before highlighting, and the block shows `<button class="btn">Save</button>`. Without `language`, the language is detected from the code, here HTML.

### Rendering from data

```blade
@foreach ($snippets as $snippet)
    <tk:highlightjs :code="$snippet->body" :language="$snippet->language" wire:key="snippet-{{ $snippet->id }}" />
@endforeach
```

One highlighted block per snippet, each in its own language.

## Props

### `tk:highlightjs`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `code` | string | `null` | The code to show. When not given, the slot is used. |
| `language` | string | auto | The language name or alias (`php`, `js`, `html`, `bash`...). Without it, the language is detected. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:highlightjs` | default | The code, with HTML entities decoded, when there is no `code`. |

## Events

`completed` and `failed` are dispatched on the wrapper around the block, do not bubble, and are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:highlightjs` | `completed` | | The library is loaded and the code is shown. |
| `tk:highlightjs` | `failed` | | The library could not be loaded. |

```blade
<tk:highlightjs :code="$snippet" loadable:x-on:completed="ready = true" />
```

## Customization

Classes and attributes on the tag go to the `<code>` element. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:highlightjs` | The wrapper around the block, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:highlightjs` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:highlightjs` | The error message. |

```blade
<tk:highlightjs :code="$snippet" language="js" class="rounded-lg p-4 text-sm" />
```

The theme gives the `<code>` its colors and background; classes on the tag add to them.

## Accessibility

The code is plain text inside `<pre><code>`, so screen readers read it as written; the colors are only visual.

## Behavior notes

- The library and its theme are loaded from jsDelivr (`highlightjs/cdn-release@11`) by the first block on the page. When `window.hljs` already exists, as when the app bundles it, the script is not requested again.
- The CDN build includes the common languages. With a language it does not know, the code is shown without colors and highlight.js logs an error in the console.
- The theme is GitHub in light mode and GitHub Dark in dark mode, switched for every block on the page when the `dark` class of `<html>` changes.
- The code is highlighted when the block is first rendered; a Livewire update that changes `code` does not highlight it again.

## Related

- `tk:pretty-print-json`: formats and colors JSON data.
