# Element

The building block behind buttons, links, badges, headings and texts: a label with an icon, a prefix, a suffix, a trailing icon, a badge and a keyboard hint around it, rendered as a `<span>`, a link or a button. Use it directly for an inline item that needs those parts but none of the styling of the higher-level tags.

```
tk:element
tk:element.wrapper
```

`tk:element.wrapper` renders a `tk:element` only when something would show, and nothing otherwise.

## Usage

```blade
<tk:element icon="calendar" label="Due tomorrow" suffix="2 tasks" />
```

A `<span>` with a calendar icon, the text "Due tomorrow" and a small muted "2 tasks" after it, side by side and vertically centered.

### As a link or a button

```blade
<tk:element href="/inbox" icon="inbox" label="Inbox" badge="12" />

<tk:element action="archive" icon="archive" label="Archive" kbd="E" />
```

With `href` (or `route`) the element is an `<a>` that navigates with `wire:navigate`, and gets `aria-current="page"` and `data-current` while its URL is the current page. With `action` (or `type`) it is a `<button type="button">` that calls the Livewire method on click. The first shows a badge with 12 after the label; the second, an `E` key hint.

### Rendering only when there is something to show

```blade
<tk:element.wrapper :label="$user->nickname" class="text-sm" />
```

When `$user->nickname` is empty nothing is rendered, not even an empty `<span>`; otherwise the same `<span>` as `tk:element`. Any other part with content, such as an `icon`, makes it render too.

## Props

### `tk:element`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `as` | string | `span` | The tag to render. `href` turns it into `a`, and `type` or `action` into `button`. |
| `href` | string | `null` | The link URL. `javascript:`, `vbscript:` and `data:` URLs are dropped. |
| `route` | string | `null` | A route name, turned into `href`. |
| `parameters` | array | `null` | The route parameters. |
| `external` | bool \| string | `false` | Opens the link in a new tab (`target="_blank"`) and turns off `wire:navigate`. A string is used as the `target`. `target="_blank"` on the tag has the same effect. |
| `navigate` | bool | auto | `wire:navigate` on the link. On by default, except for external links and links to a file (`.pdf`, `.zip`...). `true` forces it, `false` turns it off. |
| `current` | bool \| string | from the URL | Marks the link as the current page. `true` sets `aria-current="page"`, a string sets that value (`step`, `location`...), `false` never marks it. |
| `exact` | bool | `false` | Counts the link as current only on its own URL, not on the pages under it. |
| `matchQuery` | bool | `true` | Compares the query string of `href` too, when it has one. |
| `type` | string | `button` | The button type, for a `<button>`. |
| `action` | string | `null` | A Livewire method called on click, as `wire:click`. Makes the element a button. |
| `tooltip` | string \| slot | `null` | A tooltip, translated. On a link or button with no text it is the accessible name too. |
| `ariaLabel` | string \| bool | from the label | The accessible name. On a link or button it defaults to `label`, or to the tooltip when there is no label and no slot content. `false` leaves it out. |
| `info` | string \| slot | `null` | A help icon after the label, with this text as its tooltip. Replaces `iconTrailing`. |
| `kind` | string | `null` | Adds `data-tallkit-<kind>` to the element, for styling and for scripts. Used by the components built on it. |

Also accepts the [element props](shared-props.md#element-props): `prepend`, `icon`, `iconDot`, `prefix`, `label`, `suffix`, `iconTrailing`, `badge`, `kbd`, `append`.

### `tk:element.wrapper`

Takes the same props as `tk:element` and passes them on. It renders when the slot or any of `label`, `icon`, `prefix`, `suffix`, `iconTrailing`, `info`, `badge`, `prepend`, `append` or `kbd` has content.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:element` | default | The content, in place of `label`. |
| `tk:element` | `iconEmpty` | Content in the place of the icon, shown only when there is no `icon`. |
| `tk:element` | `iconTrailingEmpty` | Content in the place of the trailing icon, shown only when there is no `iconTrailing` and no `info`. |
| `tk:element.wrapper` | default | The content, in place of `label`. |

`icon`, `prefix`, `label`, `suffix`, `iconTrailing`, `badge` and `kbd` also accept a slot with any markup.

## Customization

Classes and attributes on the tag go to the element itself. Each part takes attributes through its prefix:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `icon:` | `tk:element` | The icon. |
| `icon-wrapper:` | `tk:element` | The element around the icon, when it has an `iconDot`. |
| `icon-dot:` | `tk:element` | The dot over the icon. |
| `content:` | `tk:element` | A `<span>` wrapping the label, added only when this prefix is used. |
| `prefix:` | `tk:element` | The prefix text. |
| `suffix:` | `tk:element` | The suffix text. |
| `icon-trailing:` | `tk:element` | The trailing icon. |
| `info:` | `tk:element` | The help icon. |
| `badge:` | `tk:element` | The badge. |
| `kbd:` | `tk:element` | The keyboard hint. |
| `tooltip:` | `tk:element` | The tooltip options: `tooltip:position`, `tooltip:align`, `tooltip:delay`, `tooltip:kbd`... |

```blade
<tk:element icon="star" label="Featured" icon:class="text-yellow-500" content:class="truncate max-w-40" />
```

## Accessibility

A link is a real `<a>` and an action a real `<button>`, so neither needs a role. A disabled link (`disabled` with `href`) has no `href` and carries `role="link"` and `aria-disabled="true"`. The current page link gets `aria-current`. The tooltip is linked with `aria-describedby`, unless it repeats the accessible name. An element with only an icon has no name of its own: give it a `tooltip` or an `aria-label`.

## Behavior notes

- `href` wins over `as`, and `type` or `action` win over `as` when there is no `href`.
- A `label` with line breaks keeps them, as `<br>`.
- On a link or a button, a text `label` also becomes the `aria-label`, so the badge, the suffix and the keyboard hint are not read as part of the name.
- `aria-current` is not set on a `<span>` or a `<p>`, even when `current` is true.

## Related

- [Button](button.md): a styled button or link with variants, sizes and a loading spinner.
- [Badge](badge.md): a small colored label, which can also be a link or be closable.
- `tk:link`, `tk:text`, `tk:heading`: styled text built on this tag.
