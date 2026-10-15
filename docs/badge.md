# Badge

A small label for a status, a count or a tag. It can be a link or a button, and it can carry a close button to be removed. Many components take a `badge` prop that renders one next to their label.

```
tk:badge
└── tk:badge.close
```

`closable` renders the close button for you; `tk:badge.close` is only written to place or customize one by hand.

## Usage

```blade
<tk:badge>New</tk:badge>
```

A neutral gray pill with the text, as a `<span>`.

### Color and shape

```blade
<tk:badge color="green" icon="check">Paid</tk:badge>
<tk:badge color="red" solid>Overdue</tk:badge>
<tk:badge color="blue" border rounded size="lg">Beta</tk:badge>
```

By default a color gives a soft background with darker text. `solid` fills the badge with the full color, `border` outlines it and `rounded` makes it a full pill.

### Link or action

```blade
<tk:badge href="/orders?status=paid" color="green">Paid</tk:badge>
<tk:badge wire:click="filter('paid')" color="green">Paid</tk:badge>
<tk:badge x-on:click="open = true">3 filters</tk:badge>
```

With `href` or `route` the badge is an `<a>`. With a click handler (`wire:click`, `x-on:click` or `@click`) it is a `<button>`. Both get a hover background and are reachable with the keyboard.

### Closable

```blade
<tk:badge closable x-on:dismissed="$wire.dismissTip('shipping')">Free shipping this week</tk:badge>
```

A close button sits at the end of the badge. Clicking it fades the badge out and fires `dismissed`.

### Tags from data

```blade
@foreach ($tags as $tag)
    <tk:badge wire:key="tag-{{ $tag->id }}" href="/tags/{{ $tag->slug }}" closable close:wire:click="removeTag({{ $tag->id }})">
        {{ $tag->name }}
    </tk:badge>
@endforeach
```

```php
public function removeTag(int $id): void
{
    $this->post->tags()->detach($id);
}
```

Each tag links to its page and has its own close button; `close:` attributes go to that button, so it calls the action that removes the tag on the server. Clicking the close never follows the link.

## Props

### `tk:badge`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `null` | The text, translated. The default slot does the same without translating. |
| `color` | string | neutral gray | A palette color (`red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`, `slate`, `gray`, `zinc`, `stone`, plus the ones in `tallkit.colors`), `accent` or `inverse`. |
| `solid` | bool | `false` | Fills the badge with the full color and light text, instead of the soft background. |
| `border` | bool \| string | `false` | Draws a border: `true`, `solid`, `dashed` or `dotted`. |
| `rounded` | bool | `false` | Makes the corners fully round. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets font size, padding and icon size. |
| `closable` | bool \| string | `false` | Adds a close button. A string is the icon name for it. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `iconTrailing`, `suffix`, `kbd`...), and what makes an element a link or a button: `href`, `route`, `external`, `tooltip`, `disabled`...

### `tk:badge.close`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `close` | The button icon. |
| `size` | string | `md` | Sets the icon size. |

Also accepts the [element props](shared-props.md#element-props).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:badge` | default | The badge content. A `tk:badge.close` written here makes the badge closable without the `closable` prop. |
| `tk:badge.close` | default | The button content, in place of the icon. |

## Events

Only a closable badge has events. They are dispatched on the badge's own element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:badge` | `before-dismiss` | `{ reason }` | The badge is about to close. Cancelable: `preventDefault()` keeps it. |
| `tk:badge` | `dismissed` | `{ reason }` | The fade finished and the badge is gone. |
| `tk:badge` | `restored` | | A Livewire update brought back a closed badge with new content. |

`reason` is `manual` for the close button and `programmatic` otherwise. Dispatching `tallkit:dismiss` on the badge closes it; a `reason` in its detail is passed along.

With Alpine:

```blade
<tk:badge closable x-on:before-dismiss="if (! confirm('Remove?')) $event.preventDefault()">Draft</tk:badge>
```

Calling a Livewire action:

```blade
<tk:badge closable x-on:dismissed="$wire.hideNotice()">Notice</tk:badge>
```

## Customization

Classes and attributes on the tag go to the badge element. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `close:` | `tk:badge` | The close button. |
| `icon:` | `tk:badge` | The icon. |
| `content:` | `tk:badge` | The element wrapping the text. |

The other prefixes of the element props work too (`icon-trailing:`, `suffix:`, `kbd:`...).

```blade
<tk:badge color="blue" class="uppercase" icon="star" icon:class="text-yellow-500" closable close:tooltip="Unpin">Pinned</tk:badge>
```

## Accessibility

A plain badge is a `<span>` with no role: it is read as part of the text around it. As a link or a button it is a real `<a>` or `<button>`, in the tab order.

The close button is a `<button>` named after the badge, "Remove: Paid" (translated), with a "Close" tooltip. In a badge that is both a link and closable, the two controls are siblings, never one inside the other, and each is a separate tab stop. When the badge closes with the focus on it, the focus moves to the next focusable element on the page. With `prefers-reduced-motion` the badge closes without the fade.

| Key | Action |
|-----|--------|
| Tab | Moves to the badge (when it is a link or a button), then to its close button. |
| Enter | Follows the link, runs the action or, on the close button, closes the badge. |
| Space | Runs the action or closes the badge; it does nothing on a link. |

## Behavior notes

- A closable badge that is also a link or a button renders as a `<span>` holding the two controls. `class`, `id`, `data-*` and the event listeners stay on that outer element; `href`, the click handler, `tooltip`, `aria-label` and the element props go to the inner link.
- The whole badge is the click target of the link, except the close button, which sits on top of it.
- Outside Livewire a closed badge is removed from the page. Inside a Livewire component it is hidden and stays hidden across updates, until an update changes its content: then it shows again and `restored` fires.
- To remove on the server what the badge stands for, call the action from the close button (`close:wire:click`), as in the tags example; the badge leaves with the next render.
- The close button's name comes from `label`, or from the text of the slot. When the slot has no static text (an `x-text`, for example), the name is just "Close"; give it a better one with `close:aria-label`.
- In a badge with `href`, use `closable` rather than a `tk:badge.close` of your own: a close button written in the slot ends up inside the link, and with `wire:navigate` (the default for links) clicking it closes the badge and still navigates.
- A long text is cut with an ellipsis when the badge has no room, and the close button stays visible.

## Related

- `tk:button`: for an action that is not a label; a badge with a click handler is for filters and tags.
- [Alert](alert.md): a dismissible message with title, text and actions, in the page flow.
