# Notification

A bell button with the unread count that opens a panel of the user's notifications, read from Laravel's database notifications, split into Unread and Read tabs and grouped by day. Inside a Livewire component, each notification can be marked as read or removed, and the panel can refresh itself by polling or by Laravel Echo.

```
tk:notification
└── tk:notification.panel
    └── tk:notification.list
        └── tk:notification.item ...
```

## Usage

```blade
<tk:notification grouped />
```

A bell button; with unread notifications, a blue dot on it shows the count (up to 99), and its accessible name says "Notifications (3 unread)". Clicking it opens a panel titled "Notifications" with Unread and Read tabs. Each notification shows an icon colored by its type, the title, the message, the time ("5 minutes ago") and a link when it has a `url`. With `grouped`, they are under "Today", "Yesterday", "This week" and "Earlier".

The notifications are the authenticated user's `notifications()`, newest first, up to 50. Each one's `data` gives `title`, `message`, `type` (`success`, `error`, `warning`, `info`), `icon` and `url`:

```php
public function toArray(object $notifiable): array
{
    return ['title' => 'Order shipped', 'message' => 'Order #1042 is on the way.', 'type' => 'success', 'url' => route('orders.show', 1042)];
}
```

### Actions and live updates

```blade
{{-- In the view of a Livewire component, such as the header's --}}
<tk:notification grouped echo />
```

Rendered inside a Livewire component, each unread notification gets a "Mark as read" button, each read one a "Remove" button, and the Unread tab a "Mark all as read" button; they update the database and the item fades out. With `echo`, a new notification broadcast to the user refreshes the panel at once (Laravel Echo must be on the page); `interval="30"` refreshes it every 30 seconds instead.

### Inline, from your own data

```blade
<tk:notification variant="inline" :tabs="false" compact :items="$activity" />
```

The panel is placed in the page, without the bell, and lists `$activity`: arrays or objects with the same keys (`id`, `title`, `message`, `type`, `url`, `created_at`, `read_at`). `compact` drops the icons and tightens the spacing.

## Props

### `tk:notification`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | the user's | The notifications to show. Without it, the authenticated user's database notifications. |
| `limit` | int | `50` | How many of the user's notifications are loaded. |
| `guard` | string | default guard | The auth guard of the user. |
| `variant` | string | dropdown | `inline` renders the panel in place, without the bell. |
| `tabs` | bool | `true` | Splits the panel in Unread and Read tabs. `false` shows only the unread ones. |
| `grouped` | bool | `false` | Groups the notifications by day. |
| `compact` | bool | `false` | A narrower panel, without icons, with less spacing. |
| `markAll` | bool | `true` | Shows "Mark all as read" in the Unread tab, inside Livewire. |
| `interval` | int | `null` | Refreshes the panel every that many seconds, inside Livewire. |
| `echo` | bool | `false` | Refreshes the panel when a notification is broadcast to the user, inside Livewire, with Laravel Echo. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |

The inner tags (`tk:notification.panel`, `.list`, `.item`) are rendered by this one; their parts are reached through the prefixes below.

## Customization

Classes and attributes on the tag go to its wrapper. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `trigger:` | `tk:notification` | The bell button (`trigger:variant="ghost"`). |
| `dropdown:`, `popover:` | `tk:notification` | The dropdown and its panel (`popover:class="max-w-md"`). |
| `section:` | `tk:notification` | The panel section, with its title. |
| `tab-unread:`, `tab-read:` | `tk:notification` | The two tabs (`tab-unread:label="New"`). |
| `list-unread:`, `list-read:` | `tk:notification` | The two lists (`list-unread:empty="All caught up"`). |
| `mark-all:` | `tk:notification` | The "Mark all as read" button. |

```blade
<tk:notification grouped popover:class="max-w-md" list-unread:empty="All caught up" mark-all:label="Clear" />
```

## Accessibility

The bell's accessible name includes the unread count, and the panel is a dropdown with tabs that follow the WAI-ARIA tabs pattern. Each notification with a `url` is a link reached with Tab; the action buttons have names ("Mark as read", "Remove notification") through their tooltips.

## Behavior notes

- The actions only touch the authenticated user's own notifications: marking as read only unread ones, removing only read ones. A guard sent from the browser is used only when it is one of the app's guards.
- With `items`, the actions still call the same methods, which act on the user's database notifications with those ids; outside that case they only hide the item.
- Without a logged-in user and without `items`, the panel shows its empty messages ("No new notifications").
- A `url` with an unsafe scheme (`javascript:`...) is ignored.

## Related

- [Dropdown](dropdown.md): the panel the bell opens.
- `tk:toast`: short messages that appear and go away on their own.
- [Alert](alert.md): a message placed in the page.
