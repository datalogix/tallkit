# Pretty Print JSON

Formatted, colored JSON, for showing data such as an API response, a payload or a settings object. It loads [pretty-print-json](https://pretty-print-json.js.org) on demand and follows the light and dark color scheme.

## Usage

```blade
<tk:pretty-print-json :data="$order->toArray()" />
```

A spinner while the library loads, then the data indented in a `<pre>`, with keys, strings, numbers and booleans in different colors. Strings are shown as text: HTML inside them is never rendered.

### JSON text

```blade
<tk:pretty-print-json>{"status": "paid", "items": 3}</tk:pretty-print-json>
```

The slot is parsed as JSON and formatted the same way. Text that is not valid JSON is shown as it is, escaped.

### Library options

```blade
<tk:pretty-print-json :data="$payload" :options="['quoteKeys' => true, 'trailingCommas' => false]" />
```

`options` are passed to the library's `toHtml()`: `indent`, `lineNumbers`, `linkUrls`, `quoteKeys`, `trailingCommas`...

## Props

### `tk:pretty-print-json`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | mixed | `null` | The data to show: an array, an object or a JSON string. When not given, the slot is used. |
| `options` | array | `null` | Options for pretty-print-json's `toHtml()`. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:pretty-print-json` | default | JSON text, when there is no `data`. |

## Events

`completed` and `failed` are dispatched on the wrapper, do not bubble, and are listened to with the `loadable:` prefix: `loadable:x-on:completed`.

## Customization

Classes and attributes on the tag go to the `<pre>`. The wrapper, spinner and error take the `loadable:` prefixes, as on [`tk:loadable`](loadable.md#customization).

```blade
<tk:pretty-print-json :data="$payload" class="text-xs rounded-lg p-4 overflow-auto max-h-96" />
```

## Accessibility

Plain text in a `<pre>`, read as written; the colors are only visual.

## Behavior notes

- The library and its stylesheet are loaded from jsDelivr (`pretty-print-json@3`) by the first block on the page. When `window.prettyPrintJson` already exists, nothing is requested.
- The colors switch with the `dark` class of `<html>`.
- The data is formatted when the block is first rendered; a Livewire update that changes it does not format it again.

## Related

- [Fetchable](fetchable.md): requests a URL and shows the JSON response with this tag.
- [Highlight.js](highlightjs.md): highlighted source code in other languages.
