# Composer

A message box: a textarea that grows as the user types, inside a bordered box that also holds action buttons and optional content above and below it. Use it for chat messages, comments and replies, where Enter can send the form; for a plain multi-line field use `tk:textarea`.

## Usage

```blade
<tk:composer name="message" placeholder="Write a message..." />
```

A box labelled "Message" with a two-row textarea inside. As the user types more lines the textarea grows, up to 10 rows, and then scrolls. The form sends the text under `message`.

### Sending with Enter

```blade
<form wire:submit="send">
    <tk:composer wire:model="message" submit placeholder="Write a message...">
        <x-slot:actions-trailing>
            <tk:button type="submit" icon="paper-airplane" aria-label="Send" />
        </x-slot:actions-trailing>
    </tk:composer>
</form>
```

```php
public string $message = '';

public function send()
{
    // ...

    $this->message = '';
}
```

The send button sits at the bottom right of the box. With `submit`, Enter submits the form, which runs `send()`, while Shift+Enter starts a new line; Enter does nothing while the box holds only spaces. Setting `$message` to `''` on the server empties the textarea. With `submit="ctrl+enter"` Enter adds a line and Ctrl+Enter (Cmd+Enter on a Mac) submits instead.

### Inline

```blade
<form wire:submit="send">
    <tk:composer wire:model="message" inline submit :label="false" placeholder="Reply...">
        <x-slot:actions-leading>
            <tk:button icon="plus" aria-label="Attach" />
        </x-slot:actions-leading>

        <x-slot:actions-trailing>
            <tk:button type="submit" icon="paper-airplane" aria-label="Send" />
        </x-slot:actions-trailing>
    </tk:composer>
</form>
```

A single row: the leading actions on the left, a one-row textarea in the middle and the trailing actions on the right. The textarea still grows as lines are added.

### Header and footer

```blade
<tk:composer name="comment" label="Comment">
    <x-slot:header>
        <tk:badge size="sm">Replying to Ana</tk:badge>
    </x-slot:header>

    <x-slot:footer>
        <tk:text size="sm" variant="subtle">Markdown is supported.</tk:text>
    </x-slot:footer>

    <x-slot:actions-leading>
        <tk:button size="sm" icon="paperclip" aria-label="Attach" />
    </x-slot:actions-leading>

    <x-slot:actions-trailing>
        <tk:button size="sm" type="submit" label="Comment" />
    </x-slot:actions-trailing>
</tk:composer>
```

Inside the box, from top to bottom: the badge, the textarea, the footer text, and a row with the attach button on the left and the submit button on the right. The header and the footer are not shown with `inline`.

### Your own control

```blade
<tk:composer name="title" id="title" label="Title">
    <x-slot:input>
        <tk:input id="title" :label="false" placeholder="A short title" />
    </x-slot:input>
</tk:composer>
```

The `input` slot replaces the textarea with another TALLKit control, here a one-line input, kept in sync with the composer's value. It must be a TALLKit control such as `tk:input`: a plain `<input>` is not bound. Give it the composer's `id` so the label points to it.

## Props

### `tk:composer`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `submit` | bool \| string \| array | `false` | Submits the enclosing form from the keyboard. `true` or `enter`: Enter submits and Shift+Enter adds a line. `ctrl+enter`: Ctrl+Enter or Cmd+Enter submits. An array (`['enter', 'ctrl+enter']`) accepts both. |
| `rows` | int | `2`, `1` with `inline` | The rows the textarea starts with. |
| `maxRows` | int | `10` | The textarea grows up to this many rows as the user types, then scrolls. |
| `inline` | bool | `false` | Puts the actions and the textarea on one row. The header and the footer are not shown. |
| `header` | slot | `null` | Content above the textarea, inside the box. |
| `footer` | slot | `null` | Content below the textarea, inside the box. |
| `actionsLeading` | slot | `null` | Content at the bottom left of the box, or on the left with `inline`. |
| `actionsTrailing` | slot | `null` | Content at the bottom right of the box, or on the right with `inline`. |

Also accepts the [field props](shared-props.md#field-props) (`label`, `help`, `placeholder`, `size`...) and the [field control props](shared-props.md#field-control-props) (`icon`, `loading`...), and `disabled` and `readonly`. `size` sets the font size, padding and rounding of the box.

`wire:model` and `x-model` bind the text as a string.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:composer` | default | The initial text, in place of `value`. Ignored inside Livewire. |
| `tk:composer` | `header`, `footer`, `actions-leading`, `actions-trailing` | As the props of the same name. |
| `tk:composer` | `input` | Replaces the textarea with another TALLKit control. |

## Events

The composer dispatches no events of its own. The native events of the textarea bubble to the tag: `keydown`, and `input` and `change`, which reach it on every change of the text.

```blade
<tk:composer wire:model="message" x-on:input.debounce.1s="$wire.typing()" />
```

## Customization

Classes and attributes on the tag go to the box (`role="group"`); `class` is also added to the element that wraps the textarea. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `textarea:` | `tk:composer` | The `tk:textarea` inside, with its own props and prefixes. Its `class` goes to a wrapper around the textarea: use `textarea:textarea:class` for the textarea itself. |
| `header:` | `tk:composer` | The element around the header. |
| `footer:` | `tk:composer` | The element around the footer. |
| `actions-leading:` | `tk:composer` | The element around the leading actions. |
| `actions-trailing:` | `tk:composer` | The element around the trailing actions. |
| `hidden:` | `tk:composer` | The hidden input that holds the value sent with the form. |

The [field props](shared-props.md#field-props) and [field control props](shared-props.md#field-control-props) prefixes (`label:`, `help:`, `control:`...) also work.

```blade
<tk:composer
    name="message"
    class="shadow-sm"
    textarea:textarea:class="font-mono"
    actions-trailing:class="items-end"
>
    <x-slot:actions-trailing>
        <tk:button type="submit" label="Send" />
    </x-slot:actions-trailing>
</tk:composer>
```

## Accessibility

The box has `role="group"` and is labelled by the field label, which also points to the textarea through `for`, so clicking the label focuses it. The textarea carries `aria-describedby` for the description, the help text and the error, and `aria-invalid` when the field is invalid. The action buttons are ordinary buttons in the tab order after the textarea: give icon-only ones an `aria-label`.

| Key | Action |
|-----|--------|
| Enter | With `submit`, submits the form; otherwise adds a line. |
| Shift+Enter | Adds a line. |
| Ctrl+Enter, Cmd+Enter | With `submit="ctrl+enter"`, submits the form. |

A key pressed while an input method is composing text (Japanese, Chinese...) never submits.

## Behavior notes

- Submitting goes through the form's `requestSubmit()`, so `wire:submit`, `onsubmit` and the browser's validation run as for a click on a submit button. Outside a form the submit key does nothing, and does not add a line either.
- The box is rendered once (`wire:ignore`): a Livewire update does not change the header, the footer or the actions already on the page. The text still follows `wire:model` both ways.
- Inside Livewire, `value` and the default slot are ignored: give the initial text through `wire:model`.
- Outside Livewire, the text is sent with the form by a hidden input named after `name`; the textarea itself has no name. A form reset brings back the text the composer started with.

## Related

- `tk:textarea`: a plain multi-line field, without the box and the actions.
- `tk:input`: a one-line text field.
