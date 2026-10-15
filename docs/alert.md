# Alert

A message box inside the page flow: a status, a warning or an error, with an optional title, actions, close button and auto-dismiss timer. Use it for messages that belong to a place on the page; for a floating message that is not tied to the layout use `tk:toast`.

```
tk:alert
└── tk:alert.close

tk:alert.session
```

`tk:alert` renders its own close button with `closable`; `tk:alert.close` is only written to place one by hand. `tk:alert.session` renders the alerts flashed to the session.

## Usage

```blade
<tk:alert type="success" title="Profile saved" message="Your changes are live." />
```

A green box with a check icon, the title and the message. It stays on the page until the next render.

### Closable, with a timer

```blade
<tk:alert type="info" message="A new version is available." closable duration="8s" progress="bottom" />
```

The alert collapses after 8 seconds, or when the close button is clicked. A bar at the bottom edge shrinks as the time runs out, and the countdown stops while the pointer or the focus is on the alert.

### Content and actions

```blade
<tk:alert type="warning" title="Your trial ends in 3 days">
    Add a payment method to keep your projects.

    <x-slot:actions>
        <tk:button href="/billing" label="Add payment method" />
    </x-slot:actions>
</tk:alert>
```

The default slot is the body, under the title. `actions` sits at the end of the row, before the close button when there is one.

### A list of messages

```blade
<tk:alert type="danger" title="The order could not be placed" :message="['The card was declined.', 'The shipping address is incomplete.']" />
```

An array in `message` renders as a list, one item per entry.

### Flashing from PHP

```php
public function save(): void
{
    $this->alert()->success('Profile saved.', title: 'Done');

    $this->redirect('/profile');
}
```

```blade
<tk:alert.session closable />
```

`alert()` stores the alert in the session under `status`, and `tk:alert.session` renders it on the next page. It is available as `$this->alert()` in a Livewire component, as the `alert()` helper and as `TALLKit::alert()`. Called with no arguments it returns a proxy with one method per type (`alert()->danger('...')`); called with arguments it takes `message`, `type`, `variant`, `icon`, `border`, `title`, `closable`, `duration`, `size`, `name`, `progress` and `pauseOnHover`. Several calls in the same request stack, and each one renders as its own alert. `tk:form` already includes a `tk:alert.session`.

## Props

### `tk:alert`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | neutral | `info`, `success`, `warning` or `danger` (`error` is an alias). Sets the color, the default icon and how the alert is announced. |
| `variant` | string | `soft` | `soft` fills the box with the type color, `outline` keeps it transparent with a colored border, `subtle` uses a neutral background with a colored icon. |
| `title` | string \| slot | `null` | The heading above the message. |
| `message` | string \| array \| slot | `null` | The text. An array renders as a list. |
| `icon` | string \| bool | by type | An icon name, or `false` for no icon. |
| `border` | bool \| string | `false`, `true` with `outline` | `true` draws a border all around; `top`, `left`, `right` or `bottom` draws a thick one on that side and squares the corners. |
| `closable` | bool \| string | `false` | Adds a close button. A string is the icon name for it. |
| `duration` | int \| string \| bool | off | Closes the alert after this time: milliseconds (`5000`), or a string with a unit (`5s`, `500ms`). `true` picks a time from the length of the text, between 3 and 9 seconds. |
| `progress` | bool \| string | `false` | Shows the remaining time: `true` as a fill behind the content, `top` or `bottom` as a bar on that edge. Without `duration` it turns the timer on, as `duration` `true`. |
| `pauseOnHover` | bool | `true` | Stops the timer while the pointer is over the alert. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets padding, font size and icon size. |
| `prepend` | slot | `null` | Content before the title. |
| `append` | slot | `null` | Content after the message. |
| `actions` | slot | `null` | Controls at the end of the row. |

### `tk:alert.session`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `status` | The session key to read. |
| `title` | string \| slot | `null` | A title for every alert it renders. |
| `prepend` | slot | `null` | Passed to every alert. |
| `append` | slot | `null` | Passed to every alert. |
| `actions` | slot | `null` | Passed to every alert. |

Every other attribute is passed to each alert (`closable`, `variant`, `class`...), and overrides the same key in the flashed value. The value in the session can be a string (the message), an array of `tk:alert` props, or a list of either.

### `tk:alert.close`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `close` | The button icon. |
| `size` | string | `md` | Sets the icon size. |

Also accepts the [element props](shared-props.md#element-props) (`label`, `suffix`, `kbd`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:alert` | default | The body, under the title and the `message`. |
| `tk:alert` | `title` | The heading, in place of the `title` prop. |
| `tk:alert` | `message` | The text, in place of the `message` prop. |
| `tk:alert` | `prepend` | Content before the title. |
| `tk:alert` | `append` | Content after the message. |
| `tk:alert` | `actions` | Controls at the end of the row, before the close button. |
| `tk:alert.session` | default | The body of every alert it renders. |
| `tk:alert.close` | default | The button content, in place of the icon. |

## Events

Events are dispatched on the alert's own element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:alert` | `before-dismiss` | `{ reason }` | The alert is about to close. Cancelable: `preventDefault()` keeps it open. |
| `tk:alert` | `dismissed` | `{ reason }` | The closing animation finished. |
| `tk:alert` | `restored` | | A Livewire update brought back a closed alert with new content. |

`reason` is `manual` (close button), `timeout` (the `duration` ran out) or `programmatic`.

The alert also listens to events dispatched on it:

| Event | Detail | Effect |
|-------|--------|--------|
| `tallkit:dismiss` | `{ reason }`, optional | Closes the alert. |
| `tallkit:pause` | | Stops the timer. |
| `tallkit:resume` | | Starts it again. |

With Alpine:

```blade
<tk:alert message="Unsaved changes." closable @before-dismiss="if (dirty) $event.preventDefault()" />
```

Calling a Livewire action:

```blade
<tk:alert message="Welcome aboard." closable @dismissed="$wire.hideWelcome()" />
```

## Customization

Classes and attributes on a tag go to its root element. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon:` | `tk:alert` | The icon. |
| `container:` | `tk:alert` | The element wrapping title, message and body. |
| `title:` | `tk:alert` | The heading. It is a `<p>`; `title:level="2"` makes it an `<h2>`. |
| `message:` | `tk:alert` | The message text and the body. |
| `list:` | `tk:alert` | The list, when `message` is an array. |
| `actions:` | `tk:alert` | The element wrapping the actions and the close button. |
| `close:` | `tk:alert` | The close button. |
| `progress:` | `tk:alert` | The progress track; `progress:bar:class` reaches the bar. |
| `icon:` | `tk:alert.close` | The button icon. |

```blade
<tk:alert type="info" title="Heads up" title:level="2" message="Maintenance tonight at 22:00." message:class="italic" closable close:class="self-start" />
```

Any element inside the alert with the `data-tallkit-dismissible` attribute closes it on click, which is how a custom button dismisses it:

```blade
<tk:alert message="We use cookies.">
    <x-slot:actions>
        <tk:button label="Got it" data-tallkit-dismissible />
    </x-slot:actions>
</tk:alert>
```

## Accessibility

The alert is a live region, with `aria-atomic="true"` so it is read as a whole. `danger` and `warning` get `role="alert"` and `aria-live="assertive"`; the other types get `role="status"` and `aria-live="polite"`. Pass `role` and `aria-live` to override them, for an alert that is part of the page and not a notification.

The close button is a `<button>` named "Close" (translated), with a tooltip. The timer stops while the focus is inside the alert, and when the alert closes with the focus inside, the focus moves to the next focusable element on the page. With `prefers-reduced-motion` the alert closes without the collapse animation.

## Behavior notes

- `progress` only shows with a timer: with `duration="0"` it is ignored.
- A number in `duration` is milliseconds: `duration="5"` closes the alert almost at once. Write `5s` for seconds.
- The timer also stops while the browser tab is hidden. It only runs again when nothing is pausing it: pointer, focus, tab and `tallkit:pause` each count on their own.
- Outside Livewire a closed alert is removed from the page. Inside a Livewire component it is hidden and stays hidden across updates, until an update changes its content: then it shows again, the timer restarts and `restored` fires.
- A timer in progress keeps running through a Livewire update.
- In an alert nested inside another, the close button closes only the inner one.
- `message:` attributes do not reach the list of an array `message`; use `list:`.
- A flashed alert lasts one request, so a `tk:alert.session` inside a Livewire component is gone on the next update.

## Related

- `tk:toast`: a floating message that stacks in a corner of the screen, for feedback not tied to a place on the page.
- [Danger zone](danger-zone.md): an alert with a delete button and a confirmation dialog, for destructive actions.
- `tk:error.group`: an alert listing the validation errors of a form.
