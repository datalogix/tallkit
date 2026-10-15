# Modal

A dialog over the page, built on the native `<dialog>`: a centered box, a side panel (flyout) or a floating panel, with a title, a close button and focus kept inside while it is open. It opens from a trigger, a keyboard shortcut, JavaScript or the server. `tk:modal.confirm` is a ready-made "Are you sure?" dialog.

```
tk:modal
├── trigger (slot)
├── tk:modal.close
└── content

tk:modal.confirm
tk:modal.trigger
```

## Usage

```blade
<tk:modal name="invite" title="Invite a teammate" subtitle="They will get an email with a link.">
    <x-slot:trigger>
        <tk:button label="Invite" />
    </x-slot:trigger>

    <tk:form wire:submit="invite">
        <tk:input wire:model="email" />
    </tk:form>
</tk:modal>
```

Clicking Invite opens a centered white box over a dimmed page, with the title, the subtitle, the form and a close button in the top corner. The focus moves to the title. Escape, the close button or a click on the dimmed backdrop closes it, and the focus returns to Invite.

### Opening from the server or from JavaScript

```php
public function edit(int $id): void
{
    $this->editing = $id;

    $this->modal('edit-user')->show();
}

public function save(): void
{
    // ...
    $this->modal('edit-user')->close();
}
```

```blade
<tk:modal name="edit-user" title="Edit user">...</tk:modal>

<tk:button x-on:click="tallkit.modal('edit-user').show()" label="Edit" />
```

Inside Livewire, `$this->modal($name)->show()` and `->close()` open and close the modal of that name in this component; `$this->modals()->close()` closes every open modal. Outside Livewire, `TALLKit::modal($name)->show()` before a redirect opens it on the next page. In JavaScript, `tallkit.modal(name).show()` and `.close()`.

### Side panel

```blade
<tk:modal name="filters" variant="flyout" position="right" title="Filters">
    <x-slot:trigger>
        <tk:button icon="filter" label="Filters" />
    </x-slot:trigger>

    ...
</tk:modal>
```

A full-height panel that slides in from the right edge (left in right-to-left pages), 25rem wide from `md`. `position` also takes `left`, `top` and `bottom`. `floating` is the same panel with a margin and rounded corners.

### Asking for confirmation

```blade
<tk:modal.confirm variant="delete" confirm="deleteProject">
    <tk:button variant="danger" label="Delete project" />
</tk:modal.confirm>
```

The button opens a small dialog asking "Do you really want to delete this record?" with Cancel, focused, and a red Confirm. Confirm calls `deleteProject` and closes the dialog. The title, subtitle and both buttons can be changed with props and the `cancel:` and `confirm:` prefixes.

### Keyboard shortcut

```blade
<tk:modal name="search" shortcut="ctrl.k" variant="bare">
    ...
</tk:modal>
```

Ctrl+K opens the modal from anywhere on the page. The shortcut is written with Alpine's key modifiers, so `cmd.k` is the Command key on macOS. `bare` removes the box, padding and close button, for content that brings its own look.

## Props

### `tk:modal`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | generated | The name used to open and close it from JavaScript or the server. |
| `title` | string \| slot | `null` | The heading, which names the dialog. |
| `subtitle` | string \| slot | `null` | Text under the title, which describes the dialog. |
| `description`, `prepend`, `append`, `actions`, `content`, `separator` | | | Passed to the inner [section](#related): text, content around the title, actions beside it, and a line under it (`false` by default). |
| `variant` | string | box | `flyout` (a panel along an edge), `floating` (the same with a margin), `bare` (no box, padding or close button), or the default centered box. |
| `position` | string | `right` | For `flyout` and `floating`: `left`, `right`, `top` or `bottom`. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the text sizes. |
| `backdrop` | string \| bool | dimmed | `strong` (almost black), `subtle` (light), `ghost` or `false` (none). |
| `closable` | bool | `true` (`false` with `bare`) | Shows the close button. |
| `dismissible` | bool | `true` | `false` keeps the modal open on Escape and backdrop clicks; it closes only from a close button or code. |
| `persist` | bool \| string | `false` | Like `dismissible="false"`, but shakes the box on Escape or a backdrop click. A string is the CSS class of another animation. |
| `shortcut` | string | `null` | A key combination that opens it, in Alpine's key modifier syntax: `ctrl.k`, `cmd.k`, `slash`. A shortcut without `ctrl`, `cmd`, `meta` or `alt` is ignored while the user types in a field. |
| `trigger` | slot | `null` | The element that opens the modal when clicked. |

### `tk:modal.confirm`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `null` | `delete` changes the default texts and makes Confirm red. |
| `confirm` | string | `null` | The Livewire method called by Confirm. |
| `cancel` | string | `null` | The Livewire method called by Cancel. |
| `autoClose` | bool | `true` | Closes the dialog when Confirm is clicked. `false` leaves it open, for the action to close it. |
| `title` | string | `Are you sure?` | The question. |
| `subtitle` | string | `Do you really want to proceed with this action?` | The explanation under it. |
| `actions` | slot | `null` | Replaces the Cancel and Confirm buttons. |
| `size`, `description`, `prepend`, `append` | | | As on `tk:modal`. |

Every other attribute goes to the `tk:modal` (`name`, `shortcut`...). The default slot is the trigger.

### `tk:modal.trigger` and `tk:modal.close`

`tk:modal.trigger` opens the modal named by `name` when its content is clicked, and takes a `shortcut`; use it for a trigger placed away from the modal. `tk:modal.close` is a [`tk:button`](button.md) that closes the modal it is in. Any element with `data-tallkit-modal-close` does the same.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:modal` | default | The content. |
| `tk:modal` | `trigger` | The element that opens it. |
| `tk:modal` | `close` | Replaces the close button. |
| `tk:modal` | `title`, `subtitle`, `actions`... | As the props of the same name. |
| `tk:modal.confirm` | default | The element that opens it. |

## Events

Events are dispatched on the `<dialog>` and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:modal` | `opened` | `{ name }` | The modal opened. |
| `tk:modal` | `closed` | `{ name }` | The modal closed. |
| `tk:modal.confirm` | `confirmed` | | Confirm was clicked. Bubbles. |

With Alpine:

```blade
<tk:modal name="invite" x-on:opened="$refs.email.focus()" x-on:closed="$wire.resetForm()">...</tk:modal>
```

## Customization

Classes and attributes on the tag go to the `<dialog>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `trigger:` | `tk:modal` | The element around the trigger. |
| `close:` | `tk:modal` | The close button. |
| `section:` | `tk:modal` | The section inside the box, with its own prefixes (`title:`, `subtitle:`, `content:`...). |
| `actions:`, `cancel:`, `confirm:` | `tk:modal.confirm` | The row of buttons, Cancel and Confirm (`confirm:label="Delete"`). |

```blade
<tk:modal name="invite" title="Invite" class="max-w-2xl" section:class="p-8" close:tooltip="Dismiss">...</tk:modal>
```

## Accessibility

Follows the WAI-ARIA dialog pattern with the native modal `<dialog>`: the rest of the page is inert while it is open, the dialog is named by its title (`aria-labelledby`) and described by its subtitle (`aria-describedby`), and `tk:modal.confirm` has `role="alertdialog"`. On opening, the focus goes to the element with `autofocus`, or else to the title; on closing, it returns to the element that opened it.

| Key | Action |
|-----|--------|
| Tab / Shift+Tab | Moves through the controls inside the modal only. |
| Escape | Closes the modal (the innermost one, when modals are nested), unless `dismissible` is `false` or `persist` is set. |

## Behavior notes

- A modal inside another stays on top of it; Escape and clicks close only the innermost.
- The page does not scroll behind an open modal, and keeps its scrollbar space, so the layout does not jump.
- The dialog has `wire:ignore.self`: a Livewire update does not close an open modal.
- `$this->modal()` in a Livewire component targets only the modals that component renders, so two components can use the same name. `TALLKit::modal()` and `tallkit.modal()` target every modal with the name.

## Related

- [Danger zone](danger-zone.md): a box with a delete button that opens `tk:modal.confirm`.
- [Command](command.md): `tk:command.modal`, a search palette in a modal.
- [Dropdown](dropdown.md): a panel next to its trigger, without covering the page.
- `tk:section`: the layout of title, subtitle and content inside the box.
