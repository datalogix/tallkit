# TinyMCE

A rich text editor built on [TinyMCE](https://www.tiny.cloud) 8 (self-hosted, GPL), as a form field: label, help text and validation error around it, and its content saved as HTML in a hidden field. It follows the app's language and the light and dark color scheme, and uploads images to the server.

## Usage

```blade
<tk:tinymce name="body" label="Body" />
```

A "Body" label over a TinyMCE editor with a toolbar: undo and redo, text formatting, block types, colors, font size, sub and superscript, alignment, links, lists, images and media, tables, quote, and source code. Each change is saved as HTML in a hidden field named `body`, sent with the form.

### Bound to Livewire

```blade
<tk:tinymce wire:model="body" toolbar="text list link" />
```

```php
public string $body = '<p>Hello <strong>world</strong></p>';
```

The editor opens with the HTML of `$body`, with only the text, list and link buttons, and each change sets the property. Setting `$body` on the server replaces the content shown.

### Your own configuration

```blade
<tk:tinymce
    name="body"
    :options="['height' => 500, 'menubar' => true]"
    data-options="{ setup: (editor) => editor.on('init', () => console.log('ready')) }"
/>
```

`options` is merged over the configuration the component builds; `data-options` does the same from JavaScript, for functions.

## Props

### `tk:tinymce`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `toolbar` | string | all groups | The toolbar groups, separated by spaces: `text`, `heading` (block types), `color`, `size`, `script`, `align`, `link`, `list`, `media`, `table`, `quote`, `code`. `full` turns on all of them, `none` hides the toolbar. Undo and redo are always there. |
| `upload` | array \| false | on | How images are uploaded, as on [`tk:quill`](quill.md#props): `disk`, `directory`, `types`, `maxSize`, `guest`. `false` keeps images inline. |
| `options` | array | `[]` | TinyMCE configuration, merged over the component's. |
| `scripts` | array | `[]` | URLs of scripts loaded with TinyMCE, for plugins. |
| `styles` | array | `[]` | URLs of stylesheets loaded with it. |

Also accepts the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props). `wire:model` and `x-model` bind the HTML as a string.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:tinymce` | default | The initial HTML, in place of `value`. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:tinymce` | `rendered` | `{ editor }`, the TinyMCE instance | The editor is ready, also after it is created again for a color scheme change. Dispatched on the hidden field; does not bubble. |

```blade
<tk:tinymce name="body" @rendered="editor = $event.detail.editor" />
```

## Customization

Classes on the tag go to the field control; other attributes go to the hidden field. The editor box takes the `editor:` prefix, and the parts around the field the prefixes of the [field props](shared-props.md#field-props).

```blade
<tk:tinymce name="body" :options="['height' => 400]" help="Paste images straight into the text." />
```

## Accessibility

TinyMCE's own: the editing area is an `iframe` with a labelled body, the toolbar is a WAI-ARIA toolbar (Alt+F10 moves to it), and Alt+0 opens its keyboard help.

## Behavior notes

- TinyMCE 8 is loaded from jsDelivr with the `gpl` license key, and its translation from `tinymce-i18n`, when the first editor needs them. When `window.tinymce` already exists, as when the app bundles it, it is not requested again.
- The language follows the app's locale; a language with no translation stays in English.
- The skin follows the `dark` class of `<html>`: when it changes, the editor is created again with the other skin, keeping its content.
- `disabled` and `readonly` switch the editor to read-only mode, also when toggled later.
- Inside a Livewire component, `value` and the slot are ignored: the bound property is the value.

## Related

- [Quill](quill.md), `tk:tiptap`: other HTML editors, lighter.
- [Editor.js](editorjs.md): a block editor that stores JSON.
- [Textarea](textarea.md): plain text with no formatting.
