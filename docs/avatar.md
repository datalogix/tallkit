# Avatar

A person shown as a photo, with their initials or an icon when there is no photo. Use it wherever a user is mentioned: a list of members, a comment, the account menu. It also comes as a group of overlapping avatars, as a profile row with the name beside the avatar, and as a menu opened by the avatar.

```
tk:avatar
tk:avatar.group
└── tk:avatar ...
tk:avatar.profile
tk:avatar.menu
```

## Usage

```blade
<tk:avatar name="Ada Lovelace" />
```

A round avatar with the initials "AL". With no photo to show, the initials come from the name.

### The signed-in user

```blade
<tk:avatar />

<tk:avatar guard="web" />
```

With no `name`, `email`, `username` or `user`, the avatar shows the authenticated user: their `image` when the model has one, otherwise their initials. `guard` picks the auth guard to read the user from. With nobody signed in it shows a generic user icon.

### Photo, initials and icon

```blade
<tk:avatar name="Ada Lovelace" src="/photos/ada.jpg" />

<tk:avatar name="Ada Lovelace" initials="A" color="auto" />

<tk:avatar icon="bell-outline" color="blue" square :user="false" />
```

The first shows the photo, and falls back to "AL" if the image fails to load. The second shows "A" on a background color picked from the name, always the same for the same name. The third is not a person: `:user="false"` keeps the signed-in user out of it, leaving a square blue tile with a bell.

### Group

```blade
<tk:avatar.group :items="$project->members" max="4" size="sm" aria-label="Members" />
```

The avatars overlap in a row. Each item is a user object, a name, or an array of avatar props (`['name' => 'Ada', 'src' => '/ada.jpg']`). With more items than `max`, the first four show and a last avatar reads "+3", with "3 more" as its tooltip.

### Profile

```blade
<ul>
    @foreach ($users as $user)
        <li wire:key="user-{{ $user->id }}">
            <tk:avatar.profile :user="$user">
                <x-slot:actions>
                    <tk:button size="sm" wire:click="remove({{ $user->id }})">Remove</tk:button>
                </x-slot:actions>
            </tk:avatar.profile>
        </li>
    @endforeach
</ul>
```

Each row has the avatar, the name with the email under it, and the button at the end. Pass `name`, `email` and `description` instead of `user` when there is no object to read them from.

### Account menu

```blade
<tk:avatar.menu
    :items="[
        ['label' => 'Profile', 'href' => '/profile'],
        ['label' => 'Sign out', 'href' => '/logout'],
    ]"
/>
```

The signed-in user's avatar, as a button. Clicking it opens a menu that starts with their profile (avatar, name and email), followed by a separator and the items. With `profile`, the button itself shows the profile row and the menu has only the items.

## Props

### `tk:avatar`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `user` | object \| array \| bool | the authenticated user | Where `name`, `email`, `username` and `image` are read from. `false` shows nobody. |
| `guard` | string | the default guard | The auth guard the authenticated user comes from. |
| `name` | string | from the user | The person's name: gives the initials and the accessible name. |
| `email` | string | from the user | Gives the initials when there is no name, and is what the photo is looked up by. |
| `username` | string | from the user | Used like `email`, when there is none. |
| `src` | string | from the user | The URL of the photo. Without it, the user's `image`, then a photo looked up by email or username. |
| `initials` | string | from the name | Text shown in place of the derived initials. |
| `icon` | string \| bool | `false` | An icon in place of the initials. `true` uses the `user` icon, which is also what shows when there is nothing to derive initials from. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |
| `square` | bool | `false` | A rounded square instead of a circle. |
| `color` | string | neutral gray | The background. `auto` picks a color from the name; a color name (`red`, `blue`, `emerald`...) uses its pastel; `accent`, `inverse`, `filled`, `outline`, `ghost` and `subtle` are neutral variants. |
| `alt` | string | from the name | The accessible name. An empty string hides the avatar from screen readers. |
| `tooltip` | string \| bool | `false` | A tooltip. `true` shows the name. |
| `ttl` | int | 30 days | How long a looked-up photo URL is cached, in seconds. |

`size` and `square` are inherited from `tk:avatar.group`. With `href`, `route` or `action` the avatar is a link or a button, as in `tk:link` and `tk:button`.

### `tk:avatar.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | none | The avatars: each item is a user object, a name, or an array of `tk:avatar` props. |
| `max` | int | all | How many avatars to show. The rest are counted in a last "+N" avatar. |
| `size` | string | `md` | The size of every avatar, and how much they overlap. |
| `square` | bool | `false` | Rounded squares instead of circles. |

### `tk:avatar.profile`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `description` | string \| array \| slot | the email | The text under the name. An array renders a list. |
| `showEmail` | bool | `true` | `false` leaves the email out when there is no `description`. |
| `image` | string | from the user | The URL of the photo. |
| `size` | string | `md` | The size of the avatar and of the texts. |
| `prepend` | slot | none | Content above the name. |
| `append` | slot | none | Content under the description. |
| `actions` | slot | none | Content at the end of the row, such as buttons. |

Takes `user`, `guard`, `name`, `email` and `username` as `tk:avatar` does. Every other attribute goes to the avatar (`color`, `square`, `icon`...).

### `tk:avatar.menu`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | none | The menu items, as in `tk:menu`. |
| `profile` | bool | `false` | Shows the profile row in the button, instead of the avatar alone, and leaves it out of the menu. |
| `size` | string | `md` | The size of the avatar, the profile and the menu. |
| `keepOpen` | bool | `false` | Keeps the menu open after an item is chosen. |
| `animation` | string \| bool | the menu's | The animation of the menu, as in `tk:menu`. |

Every other attribute goes to the avatar and to the profile (`name`, `email`, `src`, `color`...).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:avatar` | default | Content in place of the initials. |
| `tk:avatar.group` | default | Avatars written by hand, after the ones from `items`. |
| `tk:avatar.profile` | default | Text under the description. |
| `tk:avatar.profile` | `prepend`, `append`, `actions` | As the props of the same name. |
| `tk:avatar.menu` | default | Menu items written by hand, after the ones from `items`. |
| `tk:avatar.menu` | `prepend` | Content between the profile and the items. |

## Customization

Classes and attributes on `tk:avatar` and `tk:avatar.group` go to the root element. On `tk:avatar.profile` they go to the avatar, and on `tk:avatar.menu` to the avatar and to the profile. To reach another element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `image:` | `tk:avatar` | The `<img>`. |
| `initials:` | `tk:avatar` | The element with the initials. `initials:single` shows one letter instead of two. |
| `icon:` | `tk:avatar` | The icon. |
| `color:seed` | `tk:avatar` | With `color="auto"`, the text the color is picked from, in place of the name. |
| `tooltip:` | `tk:avatar` | The options of the tooltip (`tooltip:position`, `tooltip:delay`...). |
| `content:` | `tk:avatar.profile` | The row. |
| `container:` | `tk:avatar.profile` | The element wrapping the name and the description. |
| `name:` | `tk:avatar.profile` | The name. |
| `description:` | `tk:avatar.profile` | The description. |
| `list:` | `tk:avatar.profile` | The list, when `description` is an array. |
| `actions:` | `tk:avatar.profile` | The element wrapping the actions. |
| `dropdown:` | `tk:avatar.menu` | The dropdown: `dropdown:position`, `dropdown:align`. |
| `trigger:` | `tk:avatar.menu` | The button. |
| `avatar:` | `tk:avatar.menu` | The avatar in the button. |
| `profile:` | `tk:avatar.menu` | The profile, in the button or in the menu. |
| `menu:` | `tk:avatar.menu` | The menu. |
| `menu-separator:` | `tk:avatar.menu` | The separator under the profile. |

```blade
<tk:avatar name="Ada Lovelace" src="/photos/ada.jpg" class="ring-2 ring-white" image:class="grayscale" />

<tk:avatar.profile name="Ada Lovelace" email="ada@example.com" content:class="p-2" name:class="font-semibold" />

<tk:avatar.menu dropdown:align="end" trigger:class="ms-auto" :items="[['label' => 'Sign out', 'href' => '/logout']]" />
```

## Accessibility

An avatar with a name is exposed as an image: `role="img"` with the name in `aria-label`, taken from `alt`, then `name`, the tooltip text, `email` or `username`. The `<img>` inside is decorative. `alt=""` hides the whole avatar from screen readers, which is what `tk:avatar.profile` does, since the name is written beside it. As a link or a button the avatar keeps its own role and takes the name as its label.

`tk:avatar.group` has `role="group"`; give it an `aria-label` saying what the people are. The button of `tk:avatar.menu` is labelled "Account menu", translated, unless it shows the profile, and it carries `aria-haspopup` and `aria-expanded`.

| Key | Action |
|-----|--------|
| Enter, Space | On the menu button, opens the menu and moves focus to its first item. |
| Escape | Closes the menu and returns focus to the button. |

Inside the menu the keys are those of `tk:menu`.

## Behavior notes

- Who is shown: an avatar given a `name`, `email` or `username` shows only that person. Given none of them it shows the authenticated user, and `initials` or slot content alone do not change that: they become the fallback under the signed-in user's photo. Pass `:user="false"` for an avatar that is not a person.
- The photo is the first of `src`, the user's `image`, and a lookup by `email` or `username`. The initials or the icon are rendered under it and show if the image fails to load; that state is kept across Livewire updates.
- The lookup asks unavatar.io for a photo, sending it the email or username. It runs after the response is sent, so the first render shows the initials and the photo appears on a later one. The answer is cached for `ttl`, including "no photo"; a failed request is tried again after an hour. Set `tallkit.avatar.unavatar` to `false` to turn it off.
- Initials are the first letters of the first and last words of the name ("Ana Maria da Silva" gives "AS"). A single word gives its first two letters ("Maria" gives "Ma"). An email uses the part before the `@`, split on dots, dashes and underscores.
- `icon` wins over the initials, and slot content takes the place of derived initials.
- In `tk:avatar.group`, a `size` or `square` set on an item is overridden by the group's.

## Related

- [Appearance](appearance.md): `tk:appearance.menu` is this account menu with the theme selector in it.
- `tk:upload`: its `avatar` variant lets the user pick and change the photo.
- `tk:icon`: an icon alone, when there is no person or tile to show.
