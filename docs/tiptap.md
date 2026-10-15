# Tiptap

A rich text editor built on [Tiptap](https://tiptap.dev) 3, with a toolbar in the style of the other components, as a form field: label, help text and validation error around it, and its content saved as HTML in a hidden field. The Tiptap modules are loaded on demand, only those the toolbar needs.

## Usage

```blade
<tk:tiptap name="body" label="Body" />
```

A "Body" label over a bordered box with a toolbar: bold, italic, underline, strike, headings 1 to 3, text and highlight colors, font size, sub and superscript, alignment, link, lists, image and video, table, quote, and code. Each change is saved as HTML in a hidden field named `body`, sent with the form.

### Bound to Livewire

```blade
<tk:tiptap wire:model="body" toolbar="text heading list link" />
```

```php
public string $body = '<p>Hello <strong>world</strong></p>';
```

The editor opens with the HTML of `$body`, with only the formatting, heading, list and link buttons, and each change sets the property. Setting `$body` on the server replaces the content shown.

### Images

```blade
<tk:tiptap wire:model="body" toolbar="text media" :upload="['directory' => 'posts']" />
```

The image button, and images pasted or dropped, upload to the server and are inserted by URL; inserted images can be resized by dragging, keeping their proportions. `:upload="false"` keeps them inline.

## Props

### `tk:tiptap`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `toolbar` | string | all groups | The toolbar groups, separated by spaces: `text`, `heading`, `color`, `size`, `script`, `align`, `link`, `list`, `media`, `table`, `quote`, `code`. `full` turns on all of them, `none` hides the toolbar. |
| `upload` | array \| false | on | How images and videos are uploaded, as on [`tk:quill`](quill.md#props): `disk`, `directory`, `types`, `maxSize`, `guest`. `false` keeps them inline. |
| `version` | string | `3.30.3` | The Tiptap version loaded from esm.sh. |
| `options` | array | `[]` | Tiptap editor options, merged over the component's. |
| `scripts` | array | `[]` | URLs of extra ES modules to load, for extensions. |
| `styles` | array | `[]` | URLs of stylesheets. |

Also accepts the [field props](shared-props.md#field-props) and the [field control props](shared-props.md#field-control-props). `wire:model` and `x-model` bind the HTML as a string. `data-options` is an attribute: a JavaScript expression merged over the options, for functions and extensions.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:tiptap` | default | The initial HTML, in place of `value`. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:tiptap` | `rendered` | `{ editor }`, the Tiptap instance | The editor is ready. Dispatched on the hidden field; does not bubble. |

```blade
<tk:tiptap name="body" @rendered="editor = $event.detail.editor" />
```

## Customization

Classes on the tag go to the field control; other attributes go to the hidden field. The parts take prefixes: `editor:` for the editor box and `toolbar:` for the toolbar, plus the prefixes of the [field props](shared-props.md#field-props).

```blade
<tk:tiptap name="body" editor:class="[&_.ProseMirror]:min-h-64" />
```

## Accessibility

The editing area is a `role="textbox"` named by the label. The toolbar is a WAI-ARIA toolbar named "Formatting" (translated), with translated button names and one button in the tab order at a time.

| Key | Action |
|-----|--------|
| Tab | Moves between the toolbar and the editing area. |
| Arrow Right / Arrow Left | Moves between the toolbar buttons. |
| Ctrl/Cmd+B, I, U | Bold, italic, underline. |

## Behavior notes

- Tiptap and its extensions are imported from esm.sh at the chosen `version`, only for the groups in the toolbar.
- `disabled` and `readonly` lock the editor and the toolbar, also when toggled later.
- Inside a Livewire component, `value` and the slot are ignored: the bound property is the value.

## Related

- [Quill](quill.md), [TinyMCE](tinymce.md): other HTML editors.
- [Editor.js](editorjs.md): a block editor that stores JSON.
- [Color picker](color-picker.md): used by the color buttons of the toolbar.
