# Toast

Short messages that slide in at a corner of the screen and go away on their own: "Saved", "Payment failed". They are sent from PHP (a Livewire action, a controller before a redirect) or from JavaScript, and shown by the toast area that [`tk:html`](html.md) already places on every page.

```
tk:toast
tk:toast.flashed
```

`tk:toast` is the area where the toasts appear; `tk:toast.flashed` shows toasts flashed before a redirect on pages that persist the area.

## Usage

```php
public function save(): void
{
    // ...
    $this->toast()->success('Profile saved.');
}
```

A small card at the bottom right with a green check icon and "Profile saved.", announced to screen readers. It closes by itself after a few seconds (longer for longer texts), with a bar showing the time left; hovering or focusing it pauses the timer, and it can be swiped away or closed with its button.

### From a controller or JavaScript

```php
toast()->error('The file is too large.', 'Upload failed');

return redirect()->route('files.index');
```

```blade
<tk:button x-on:click="$tallkit.toast().info('Copied to the clipboard.')" label="Copy" />
```

Outside Livewire, the toast is flashed to the session and shown on the next page. In JavaScript, `tallkit.toast()` (or `$tallkit.toast()` in Alpine) takes the same arguments.

### Options and actions

```php
$this->toast(
    message: 'Message archived.',
    type: 'info',
    duration: 8000,
    position: 'top-center',
    actions: [['label' => 'Undo', 'method' => 'unarchive']],
);
```

An info toast at the top center for 8 seconds, with an Undo button that calls `unarchive` on the component that sent it.

### Loading and promises

```blade
<tk:button x-on:click="$tallkit.toast().promise($wire.export(), { loading: 'Exporting...', success: 'Export ready', error: 'Export failed' })" label="Export" />
```

A toast with a spinner while the promise runs, which turns into a success or an error toast when it settles. `toast().loading(message)` shows a spinner toast that stays until closed with `toast().close(id)`.

## Options

Accepted by `toast()` in PHP and JavaScript, positionally or by name. `toast()->success()`, `->error()`, `->info()`, `->warning()` set the `type`.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `message` | string \| Htmlable | `null` | The text. A string is escaped; an `HtmlString` or a view is rendered as HTML. |
| `title` | string \| Htmlable | `null` | A bold line above the message. |
| `type` | string | `null` | `success`, `error`, `warning`, `info` or `loading`: the icon and color, and how it is announced (errors and warnings assertively). |
| `duration` | int \| false | by length | Milliseconds before it closes; `false` keeps it until closed. |
| `position` | string | `bottom-right` | `top-left`, `top-center`, `top-right`, `bottom-left`, `bottom-center`, `bottom-right` (`top` and `bottom` mean right). |
| `progress` | bool | `true` | The bar showing the time left. |
| `size` | string | `md` | The size of the card. |
| `invert` | bool | `false` | Dark card in light mode and light in dark mode. |
| `actions` | array | `null` | Buttons, each with a `label` and one of: `method` (with `params`, called on the Livewire component that sent the toast), `event` (a Livewire event dispatched with `params`), or `href` (a link). In JavaScript, `onClick` (a function) too. |
| `id` | string | generated | Updates the toast with the same id instead of adding another, and is what `close(id)` takes. |

## Props

### `tk:toast`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `flashed` | bool | `true` | Shows the toasts flashed to the session when the page loads. |

### `tk:toast.flashed`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `nonce` | string | `null` | The CSP nonce of its script. |

## Customization

`tk:html` passes `toast:` attributes to the area. On `tk:toast`, classes go to the full-screen layer, and the parts take prefixes:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `position:` | `tk:toast` | Each corner's stack. |
| `container:`, `content:` | `tk:toast` | Each toast card, and its text area. |
| `title:`, `message:`, `icon`, `actions:`, `close:`, `progress:` | `tk:toast` | The parts of each toast. |

```blade
<tk:html toast:container:class="rounded-xl shadow-lg" toast:position:class="gap-3">...</tk:html>
```

## Accessibility

Each toast is announced through hidden live regions: polite for success, info and loading, assertive for errors and warnings. The timer pauses while the pointer is over the toast or the focus is in it, and while the page is in the background. The close button has an accessible name.

## Behavior notes

- Toasts sent from a Livewire action are shown in the same response, without a reload; toasts sent during a request outside Livewire wait for the next page.
- With `wire:navigate` the area persists between pages (`tk:html` uses `@persist`), so a toast stays visible while the user navigates.
- At most five toasts stack in each corner; older ones give way to new ones.

## Related

- [Alert](alert.md): a message that stays in the page.
- [Notification](notification.md): the user's stored notifications, in a panel.
- [Form](form.md): shows a toast after a Livewire submit.
