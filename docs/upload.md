# Upload

A file field with a drop area, previews of the chosen files, progress while they upload through Livewire, limits on type, size and count, and a preview modal for images, videos and PDFs. It comes in several layouts: a dropzone, a button, a list, a gallery of tiles and a round avatar.

## Usage

```blade
<tk:upload wire:model="photo" label="Photo" accept="image/*" />
```

```php
use Livewire\WithFileUploads;

public $photo;
```

A dashed box reading "Drag or click to select". Dropping an image on it, or choosing one, shows a tile with its thumbnail, name and size, and a progress bar while Livewire uploads it; `$photo` then holds the temporary file. A hint under the box tells the accepted types and the size limit, and the tile has buttons to preview and remove the file.

### Several files

```blade
<tk:upload wire:model="docs" multiple max-files="5" max-size="10240" accept=".pdf,.docx" label="Documents" />
```

```php
public array $docs = [];
```

Up to five files of up to 10 MB each. Each file is a tile; they can be reordered by dragging, or with Alt and an arrow key, and `$docs` follows the order. Files over the limits are refused with a message.

### Layouts

```blade
<tk:upload wire:model="avatar" variant="avatar" />
<tk:upload wire:model="attachments" variant="button" multiple />
<tk:upload wire:model="files" variant="list" multiple />
<tk:upload wire:model="photos" variant="gallery" multiple accept="image/*" />
```

A round avatar with a camera icon that shows the chosen photo; a "Select files" button with each file as a chip under it; an "Add files" button over a list of files with their sizes; and a grid of image tiles.

### Files already stored

```blade
<tk:upload name="photo" :value="$user->photo_path" disk="s3" stored="avatars" />
```

A path (or a list of paths) on the disk is shown as an existing file, with its preview. With `stored`, only paths under that directory are looked up, so a path sent back by the browser cannot point anywhere else on the disk.

## Props

### `tk:upload`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `dropzone` | `dropzone`, `button`, `list`, `gallery` or `avatar`. |
| `multiple` | bool | `false` | Lets several files be chosen. Off for `avatar`. |
| `accept` | string | `null` | Accepted types, as the native attribute: `image/*`, `.pdf,.docx`... |
| `maxSize` | int | per type | The size limit in KB. Without it, the configured limit for each kind of file. |
| `maxFiles` | int | `null` | How many files can be chosen, with `multiple`. |
| `droppable` | bool | `true` | Accepts files dropped on the area. |
| `sortable` | bool | with `multiple` | Lets the files be reordered. Not for `button` and `avatar`. |
| `hint` | string \| false | from the limits | The text under the area. `false` removes it. |
| `disk` | string | the configured disk | The disk where stored paths are looked up. |
| `stored` | string \| bool | `false` | Shows stored paths only when they are under this directory (`true`: the upload directory). `false` shows any path given. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `value`, `size`, `color`...). `wire:model` binds the uploaded file (or an array of them) through Livewire's file uploads; a native form sends the files under the field's `name`.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:upload` | default | Content inside the drop area, in place of its default text and icon. |

## Customization

Classes and attributes go to the field; the parts take prefixes, such as `tile:` for each file's tile, chip or list item, plus the prefixes of the [field props](shared-props.md#field-props).

```blade
<tk:upload wire:model="docs" multiple class="max-w-lg" tile:class="bg-zinc-50" />
```

## Accessibility

The drop area is a button that opens the file dialog, with a name ("Add files", or the label). Files added, removed, rejected or moved are announced in a live region. Reordering works from the keyboard with Alt and the arrow keys, and each tile's buttons have names ("Preview", "Remove").

## Behavior notes

- Removing a file uploaded through Livewire also removes the temporary upload.
- Inside a Livewire component, the bound property is the value; `value` shows stored files when there is no binding.
- Paths sent back by the browser are only looked up when the server rendered them before, so a crafted path cannot reveal another file.

## Related

- [Input](input.md): `type="file"` renders this tag.
- [Avatar](avatar.md): shows a person's photo.
- [Quill](quill.md), [Tiptap](tiptap.md), [TinyMCE](tinymce.md): upload images placed in the text.
