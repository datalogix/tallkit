# Quill

A rich text editor built on [Quill](https://quilljs.com) 2, as a form field: label, help text and validation error around it, and its content saved as HTML in a hidden field. Images pasted, dropped or inserted are uploaded to the server and kept as links.

## Usage

```blade
<tk:quill name="body" label="Body" />
```

A bordered box under the "Body" label, with a toolbar on top: bold, italic, underline, strike, headings, text and background colors, size, sub and superscript, alignment and indentation, link, lists, image and video, quote, code block and a button to clear formatting. Each change is saved as HTML in a hidden field named `body`, sent with the form; an empty editor sends `''`.

### Bound to Livewire

```blade
<tk:quill wire:model="body" label="Body" toolbar="text heading list link" />
```

```php
public string $body = '<p>Hello <strong>world</strong></p>';
```

The editor opens with the HTML of `$body`, and each change sets it, e.g. `'<p>Hello <strong>world</strong> again</p>'`. Setting `$body` on the server replaces the content shown. `toolbar` keeps only the listed groups.

### Uploads

```blade
<tk:quill wire:model="body" :upload="['directory' => 'posts', 'maxSize' => ['image' => 4096]]" />
<tk:quill wire:model="note" :upload="false" />
```

Images go through TALLKit's upload route to the `public` disk, by default under `tallkit-uploads/`, and only their URL is stored in the HTML. The first editor stores them under `posts/` and accepts images up to 4 MB. With `:upload="false"` images stay in the HTML as data URLs.

## Props

### `tk:quill`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `toolbar` | string | all groups | The toolbar groups, separated by spaces: `text`, `heading`, `color`, `size`, `script`, `align` (alignment, indentation, direction), `link`, `list`, `media` (image and video), `quote`, `code`. `full` turns on all of them, `none` none. A "clear formatting" button is always added. |
| `upload` | array \| false | on | How images are uploaded: `disk`, `directory`, `types` (`image`, `video`), `maxSize` in KB (one number, or one per type), `guest` for pages without a signed-in user. `false` keeps images inline. |
| `options` | array | `[]` | Quill configuration, merged over the one the component builds (`placeholder`, `formats`, `modules`...). |
| `scripts` | array | `[]` | URLs of scripts loaded before the editor starts, for Quill modules. |
| `styles` | array | `[]` | URLs of stylesheets loaded with Quill's. |

Also accepts the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props). `wire:model` and `x-model` bind the HTML as a string. `data-options` is an attribute: a JavaScript expression merged over the configuration, for options that are functions.

## Slots

The [field props](shared-props.md#field-props) that take a slot (`label`, `help`...).

## Events

`rendered` is dispatched on the hidden field, the element the tag's attributes go to, and does not bubble: listen on the tag itself. `completed` and `failed` are dispatched on the editor box and are listened to with the `editor:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:quill` | `rendered` | `{ editor }`, the Quill instance | The editor is ready. |
| `tk:quill` | `completed` | | Quill and its stylesheet are loaded. |
| `tk:quill` | `failed` | | A script could not be loaded, or Quill threw while starting. |

```blade
<tk:quill name="body" @rendered="editor = $event.detail.editor" />
```

## Customization

Classes on the tag go to the field control; other attributes go to the hidden field. The editor box, with its border and focus ring, takes the `editor:` prefix; the parts around the field take the prefixes of the [field props](shared-props.md#field-props).

```blade
<tk:quill name="body" editor:class="[&_.ql-editor]:min-h-64" help="Paste images straight into the text." />
```

## Accessibility

The editing area is a `role="textbox"` with `aria-multiline`, named by the label and described by the help text and the error, with `aria-invalid` when the field is invalid. The toolbar buttons have translated names ("Bold", "Heading"...). Tab moves the focus out of the editor instead of inserting a tab.

## Behavior notes

- Quill, its stylesheet and DOMPurify are loaded from jsDelivr when the first editor needs them. When `window.Quill` already exists, as when the app bundles it, it is not requested again.
- HTML set from outside (the initial value, a Livewire update, a form reset) is sanitized with DOMPurify before it is shown.
- `disabled` and `readonly` lock the editor and the toolbar, and toggling either later switches them.
- `wire:model.blur` sends the value when the focus leaves the whole editor, toolbar included.
- Inside a Livewire component, `value` and the slot are ignored: the bound property is the value.
- The upload route is reached through a signed link that expires after a day (`tallkit.upload.url_ttl`). `php artisan tallkit:prune-uploads` deletes uploaded files that nothing uses anymore.

## Related

- `tk:tiptap`, `tk:tinymce`: other HTML editors, with their own toolbars.
- [Editor.js](editorjs.md): a block editor that stores JSON.
- `tk:textarea`: plain text with no formatting.
