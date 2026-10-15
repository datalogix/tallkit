# Textarea

A multi-line text field with its label, description, help text and validation error. It can grow with its content, count characters against a limit, and carry a copy button.

## Usage

```blade
<tk:textarea name="bio" label="Bio" />
```

A "Bio" label over a box three rows tall that the user can resize vertically. Inside a Livewire component with a `$bio` property, the field is bound to it.

### Growing with the text

```blade
<tk:textarea wire:model="message" rows="2" max-rows="8" />
```

Two rows at first, growing one row at a time as lines are typed, up to eight; then it scrolls. `rows="auto"` instead lets the browser size it to the content with no limit (`field-sizing: content`).

### Character limit

```blade
<tk:textarea name="summary" maxlength="280" help="Shown in search results." />
```

A "0 / 280" counter under the box, updated as the user types; the browser stops input at 280 characters. `counter` shows the count without a limit.

### Copy button

```blade
<tk:textarea name="token" :value="$token" readonly copyable />
```

A copy button in the corner of the box, which writes the text to the clipboard.

## Props

### `tk:textarea`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `rows` | int \| string | `3` | The height in rows. `auto` sizes it to the content. |
| `maxRows` | int | `null` | Grows the box with the text from `rows` up to this many rows. |
| `maxlength` | int | `null` | The character limit, with a counter. |
| `counter` | bool | with `maxlength` | Shows the character count. |
| `resize` | string | `vertical` | `none`, `vertical`, `horizontal` or `both`. Off with `maxRows` or `rows="auto"`. |
| `copyable` | bool \| string | `false` | Adds a copy button. A string is its accessible name. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `description`, `help`, `placeholder`, `value`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `prepend`, `append`, `loading`...). `wire:model` and `x-model` bind the text.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:textarea` | default | The initial text, in place of `value`. |

## Customization

Classes on the tag go to the box when there is anything in it besides the textarea, otherwise to the textarea. Other attributes go to the `<textarea>`. Besides the prefixes of the field props and field control props:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `textarea:` | `tk:textarea` | Classes for the `<textarea>` itself (`textarea:class`). |
| `counter:` | `tk:textarea` | The character counter. |
| `copyable:` | `tk:textarea` | The copy button. |
| `area:` | `tk:textarea` | The element around everything, present with a counter or `maxRows`. |

```blade
<tk:textarea name="notes" textarea:class="font-mono text-sm" counter:class="text-xs" />
```

## Accessibility

The label points to the textarea, and `aria-describedby` lists the description, the help text, the error and the counter, so screen readers can read the remaining characters.

## Behavior notes

- The counter and the height follow Livewire updates and form resets.
- Inside a Livewire component, `value` and the slot are ignored: the bound property is the value.

## Related

- [Input](input.md): a single line of text.
- [Composer](composer.md): a message box with actions, for chats and comments.
- [Quill](quill.md), [Editor.js](editorjs.md): formatted text.
