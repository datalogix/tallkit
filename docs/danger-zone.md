# Danger zone

A red box with a warning and a Delete button that asks for confirmation in a dialog before running a destructive action. Use it at the end of a settings or edit page, for deleting the record, closing the account and the like.

## Usage

```blade
<tk:danger-zone modal:confirm="deleteAccount" />
```

```php
public function deleteAccount(): void
{
    auth()->user()->delete();

    $this->redirect('/');
}
```

A red box titled "Danger Zone", with a message saying the record and its data will be lost for good, and a red Delete button under it. Delete opens a dialog asking "Do you really want to delete this record?" with Cancel and Confirm; Confirm calls `deleteAccount` and closes the dialog, Cancel or Escape closes it without doing anything. Every default text is translated.

### Your own texts

```blade
<tk:danger-zone
    title="Delete project"
    message="The project, its tasks and its files are removed for everyone."
    trigger:label="Delete project"
    trigger:icon="trash"
    modal:title="Delete this project?"
    modal:subtitle="Its 12 tasks go with it."
    modal:confirm:label="Delete project"
    modal:confirm="deleteProject"
/>
```

`title` and `message` change the box. `trigger:` reaches the Delete button, here with another label and an icon. `modal:` reaches the dialog: its title, the subtitle under it, and the confirm button through `modal:confirm:`, whose label stays "Confirm" unless you change it.

### Your own content

```blade
<tk:danger-zone title="Close account" message="You can export your data before closing the account.">
    <div class="mt-4 flex gap-2">
        <tk:button href="/export">Export data</tk:button>
        <tk:button variant="danger" wire:click="closeAccount">Close account</tk:button>
    </div>
</tk:danger-zone>
```

Content in the default slot takes the place of the Delete button and the dialog: the box shows the title, the message and the two buttons, and nothing asks for confirmation.

### Rendering from data

```blade
@foreach ($projects as $project)
    <tk:danger-zone
        :title="'Delete '.$project->name"
        :modal:confirm="'deleteProject('.$project->id.')'"
        wire:key="project-{{ $project->id }}"
    />
@endforeach
```

```php
public function deleteProject(int $id): void
{
    Project::findOrFail($id)->delete();
}
```

One box per project, each with its own dialog. Confirming in the second box calls `deleteProject(2)`.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `Danger Zone` | The heading of the box, translated. |
| `message` | string \| array | `By deleting this record, all associated data will be permanently lost and cannot be recovered.` | The text under the title, translated. An array renders as a list. |
| `prepend` | slot | `null` | Content before the title. |
| `actions` | slot | `null` | Controls at the end of the row. |

The box is a `tk:alert`, and the other [alert props](alert.md#props) pass to it (`type`, `variant`, `icon`, `border`, `closable`, `size`...); `type` defaults to `danger`.

## Slots

| Slot | Description |
|------|-------------|
| default | Replaces the Delete button and the dialog. |
| `prepend` | Content before the title. |
| `actions` | Controls at the end of the row. |

## Events

The event bubbles from the confirm button through the box, so it can be heard on the tag or on any element around it.

| Event | Detail | When |
|-------|--------|------|
| `confirmed` | | The confirm button of the dialog was clicked. |

The dialog's own `opened` and `closed` do not reach the tag. With `closable`, the box dispatches the [alert events](alert.md#events) (`before-dismiss`, `dismissed`).

With Alpine:

```blade
<div x-data="{ removed: false }">
    <tk:danger-zone @confirmed="removed = true" />

    <p x-show="removed">Removed.</p>
</div>
```

Calling a Livewire action:

```blade
<tk:danger-zone @confirmed="$wire.archive()" />
```

## Customization

Classes and attributes go to the box, the root element of the alert. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `trigger:` | The Delete button, as props and attributes of `tk:button` (`trigger:label`, `trigger:icon`, `trigger:size`...). |
| `modal:` | The dialog, as props and attributes of `tk:modal.confirm`: `modal:title`, `modal:subtitle`, `modal:confirm` (the Livewire action), `modal:cancel`, `modal:name`, and its own prefixes `modal:confirm:`, `modal:cancel:` and `modal:actions:`. |
| `title:`, `message:`, `icon:`, `container:`, `actions:`, `close:` | The parts of the box, as in [`tk:alert`](alert.md#customization). |

```blade
<tk:danger-zone
    class="mt-10"
    border="left"
    title:level="2"
    trigger:class="mt-6"
    trigger:size="sm"
    modal:confirm="deleteAccount"
    :modal:auto-close="false"
/>
```

The box gets a thick red border on the left and its title is an `<h2>`. With `:modal:auto-close="false"` the dialog stays open after Confirm, so the action can close it when it is done; the bound form is needed, since the string `"false"` keeps it closing.

## Accessibility

The box has `role="group"` and `aria-labelledby` pointing to its title, and `aria-live="off"`: unlike a `tk:alert` of type `danger`, it is part of the page and is not announced when the page loads. Pass `role` to change it.

The dialog follows the WAI-ARIA alert dialog pattern: `role="alertdialog"`, named by its title and described by its subtitle. When it opens the focus goes to Cancel, so pressing Enter by accident does not confirm; when it closes the focus returns to the Delete button.

| Key | Action |
|-----|--------|
| Enter, Space | On the Delete button, opens the dialog. |
| Tab | Moves from Cancel to Confirm. |
| Enter | Runs the focused button: Cancel closes the dialog, Confirm runs the action and closes it. |
| Escape | Closes the dialog without confirming. |

## Behavior notes

- The box and the Delete button are red by default; `type` and `trigger:variant` change them.
- `title` and `message` take only strings. A `title` or `message` slot is ignored, and the default text shows in its place.
- `modal:` and `trigger:` attributes do nothing when the default slot has content, since there is no dialog or button to receive them.
- The confirm button is red, but its label is "Confirm" (translated); pass `modal:confirm:label` for a label that names the action.
- The title id is the tag's `id` followed by `-title`, or a generated one when there is no `id`.
- Inside Livewire the Delete button still opens its dialog after an update, and each box in a `@foreach` keeps its own dialog.

## Related

- [Alert](alert.md): a message box with no destructive action attached.
- `tk:modal.confirm`: the same confirmation dialog around any trigger, for a destructive action that does not need a section of its own.
