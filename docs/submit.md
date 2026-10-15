# Submit

A form's submit button: a [`tk:button`](button.md) with `type="submit"` and a "Send" label, translated. While the form is being sent it shows a spinner and blocks further clicks.

## Usage

```blade
<form wire:submit="save">
    <tk:input wire:model="name" />

    <tk:submit label="Save" variant="primary" />
</form>
```

A primary "Save" button. Clicking it, or pressing Enter in the field, submits the form; until the request ends, the label gives place to a spinner, the button keeps its width and is disabled, so the form is not sent twice.

### Default label

```blade
<tk:submit />
```

A "Send" button (translated). [`tk:form`](form.md) adds one like it, full width, when the form has no submit button of its own.

## Props

### `tk:submit`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `Send` | The button text, translated. The default slot takes its place. |

Also accepts every prop of [`tk:button`](button.md#props) (`variant`, `size`, `icon`, `loading`...) and the [element props](shared-props.md#element-props).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:submit` | default | The button content, in place of `label`. |

## Customization

As [`tk:button`](button.md#customization): classes and attributes go to the `<button>`.

```blade
<tk:submit label="Create account" class="w-full" icon-trailing="arrow-right" />
```

## Accessibility

A native `<button type="submit">`. The spinner is hidden from screen readers.

## Behavior notes

- Outside Livewire, a submitted form disables its submit buttons until the page changes, so a double click does not send it twice; they come back after 10 seconds, or when the user returns to the page with the browser's back button.
- A button disabled when the page renders (`disabled`, `:disabled`, `x-bind:disabled`) shows no spinner.
- `:loading="false"` turns the spinner off.

## Related

- [Button](button.md): the button it is built on.
- [Form](form.md): adds a submit button by itself.
