# Editor.js

A block editor built on [Editor.js](https://editorjs.io), as a form field: label, help text and validation error around it, and its content saved as Editor.js JSON in a hidden field. Use it when the content is stored as structured blocks (paragraphs, headings, lists, images...) rather than HTML.

## Usage

```blade
<tk:editorjs name="body" label="Body" />
```

A bordered box under the "Body" label. Once the library loads, clicking inside starts a paragraph; the `+` button at the start of an empty line adds a heading, a list, an image, a table, a quote or code. Every change is saved as JSON in a hidden field named `body`, sent with the form.

### Bound to Livewire

```blade
<tk:editorjs wire:model="body" label="Body" toolbar="text heading list" />
```

```php
public string $body = '{"blocks":[{"type":"paragraph","data":{"text":"Hello"}}]}';
```

The editor opens with a "Hello" paragraph. Each change sets `$body` to the editor's JSON, e.g. `'{"time":...,"blocks":[{"type":"paragraph","data":{"text":"Hello world"}}],"version":"2.31.7"}'`, and an editor with no blocks sets it to `''`. Setting `$body` on the server replaces the content shown. `toolbar` limits the tools to inline formatting, headings and lists.

### Your own tools

```blade
<tk:editorjs
    name="body"
    toolbar="text list"
    :scripts="['https://cdn.jsdelivr.net/npm/@editorjs/checklist@1']"
    data-options="{ tools: { checklist: { class: window.Checklist } } }"
/>
```

`scripts` loads more plugins from a URL before the editor starts, and `data-options` is a JavaScript object merged over the Editor.js configuration, where the plugin is registered. `options` does the same from PHP, for settings that are not functions.

## Props

### `tk:editorjs`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `toolbar` | string | all groups | The tool groups, separated by spaces: `text` (bold, italic, underline, inline code), `heading`, `color` (marker), `link`, `list`, `media` (image by URL and embeds), `table`, `quote` (quote, warning, delimiter) and `code` (code and raw HTML). `full` turns on all of them and `none` none. |
| `options` | array | `[]` | Editor.js configuration, merged over the one the component builds (`placeholder`, `autofocus`, `minHeight`...). |
| `scripts` | array | `[]` | URLs of scripts loaded before the editor starts, for plugins of your own. |
| `styles` | array | `[]` | URLs of stylesheets loaded with them. |

Also accepts the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props). `wire:model` and `x-model` bind the editor's content as a JSON string, `''` when it has no blocks.

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object merged over the configuration, after `options`.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:editorjs` | default | The initial content, as Editor.js JSON, in place of `value`. |

## Events

`rendered` is dispatched on the hidden field, the element the tag's attributes go to, and does not bubble: listen on the tag itself. `completed` and `failed` are dispatched on the editor box and are listened to with the `editor:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:editorjs` | `rendered` | `{ editor }`, the Editor.js instance | The editor is ready. |
| `tk:editorjs` | `completed` | | The editor and its plugins are loaded and ready. |
| `tk:editorjs` | `failed` | | A script could not be loaded, or Editor.js threw while starting. |

With Alpine:

```blade
<tk:editorjs name="body" @rendered="editor = $event.detail.editor" editor:x-on:failed="broken = true" />
```

Calling a Livewire action:

```blade
<tk:editorjs wire:model="body" @rendered="$wire.editorReady()" />
```

## Customization

Classes on the tag go to the field control around the editor; other attributes go to the hidden field. To reach the other parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `editor:` | `tk:editorjs` | The editor box: border, padding and focus ring. |

The parts around the field take the prefixes of the [field props](shared-props.md#field-props).

```blade
<tk:editorjs name="body" editor:class="min-h-64 bg-zinc-50" help="Type / to see the blocks." />
```

## Accessibility

The label is linked to the editing area with `aria-labelledby` on its `role="group"` element, and the hidden field carries `aria-describedby` and `aria-invalid` for the help text and the validation error. Keyboard interaction inside the editor (Tab to the block menu, `/` for blocks, the inline toolbar on selection) is Editor.js's own.

## Behavior notes

- Editor.js and the plugins of the chosen groups are loaded from jsDelivr when the first editor needs them. When `window.EditorJS` already exists, as when the app bundles it, the core is not requested again.
- The value must be Editor.js data: JSON with a `blocks` list. Anything else, such as HTML, starts the editor empty and logs a warning in the console.
- `disabled` and `readonly` open the editor in read-only mode, and toggling either attribute later switches the mode.
- `wire:model.blur` sends the value when the focus leaves the whole editor, not when it moves between blocks.
- Outside Livewire, resetting the form puts back the content the field had when the page loaded.
- Inside a Livewire component, `value` and the slot are ignored: the bound property is the value.
- The texts of the editor's own interface (block menu, tool names, placeholders) are translated with the app's language files.

## Related

- `tk:quill`, `tk:tiptap`, `tk:tinymce`: rich text editors that store HTML instead of blocks.
- `tk:textarea`: plain text with no formatting.
