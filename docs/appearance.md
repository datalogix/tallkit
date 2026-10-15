# Appearance

Light and dark mode: the script that applies the user's choice before the page paints, and the controls that change it. `tk:html` already includes `tk:appearance`, so most apps only place one of the controls: a toggle button, a three-way selector, or the account menu that carries one of them.

```
tk:appearance

tk:appearance.toggle
tk:appearance.selector

tk:appearance.menu
└── tk:appearance.menu-item

tk:appearance.view
```

`tk:appearance.menu` renders its own `tk:appearance.menu-item`; the tag is only written to put the theme row in a menu built by hand.

## Usage

```blade
<tk:appearance.toggle />
```

A button with a sun in light mode and a moon in dark mode. Clicking it switches to the other mode, with the new colors spreading in a circle from the button, and the choice is remembered in the browser.

### Choosing between system, light and dark

```blade
<tk:appearance.selector />
```

Three icon buttons in a group: System, Light and Dark, with the current one highlighted. System follows the operating system and keeps following it when it changes.

### Account menu with the theme inside

```blade
<tk:appearance.menu
    name="Ada Lovelace"
    :items="[
        ['label' => 'Profile', 'href' => '/profile'],
        ['label' => 'Settings', 'href' => '/settings'],
    ]"
/>
```

An avatar that opens a menu. The first row is "Theme", with the selector at its end, followed by a separator and the items. Picking a theme does not close the menu. With no `items` and no slot content there is no menu, and the tag renders the toggle button alone.

### A control of your own

```blade
<div x-data>
    <button type="button" x-on:click="$tallkit.appearance.apply('dark')">Dark</button>
    <button type="button" x-on:click="$tallkit.appearance.apply('system')">Follow the system</button>

    <span x-text="$tallkit.appearance.mode"></span>
</div>
```

`$tallkit.appearance` (or `$tk.appearance`) is what the controls use. `apply()` takes `system`, `light` or `dark`; `mode` is reactive and holds the current choice; `isDark()` and `isLight()` tell what is on screen, which in `system` mode depends on the operating system.

### Without `tk:html`

```blade
<head>
    <tk:appearance nonce="abc123" />
</head>
```

In a layout that does not use `tk:html`, put the tag in the `<head>`, before the stylesheets. It prints a small inline script that adds the `dark` class to `<html>` before the first paint, so a dark page never flashes light.

## Props

### `tk:appearance`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `nonce` | string | none | The CSP nonce set on the inline `<style>` and `<script>`. |

In `tk:html` the tag is controlled by the `appearance` prop: `true` (default) includes it with the page nonce, a string is used as the nonce, and `false` leaves it out.

### `tk:appearance.toggle`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `animate` | bool \| array | `true` | The circular reveal when switching. `false` switches instantly; an array is passed as the animation options, e.g. `['duration' => 600]`, over the defaults of 300 ms and `ease-in`. |

Also accepts the [element props](shared-props.md#element-props) (`label`, `suffix`, `kbd`...), and every other attribute goes to the `tk:button` it renders, with `variant="subtle"` and the tooltip "Toggle light and dark mode" as defaults.

### `tk:appearance.selector`

Has no props of its own. Attributes go to the `tk:button.group` it renders, so its props (`size`, for instance) apply.

### `tk:appearance.menu`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `control` | bool \| string | automatic | Which theme control to render. By default the selector row inside the menu, or the toggle button when there is no menu. `toggle` renders the toggle button beside the menu and no row inside it; `selector` renders only the row; `false` renders no theme control. |
| `items` | array | none | The menu items, as in `tk:menu`. |

Every other attribute goes to the `tk:avatar.menu` it renders (`name`, `src`, `profile`, `size`...).

### `tk:appearance.menu-item`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string \| slot | `Theme` | The text of the row, translated. |
| `icon` | string \| slot | `palette-outline` | The icon before the label. |

Also accepts the other [element props](shared-props.md#element-props).

### `tk:appearance.view`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `livewire` | string | none | The name of a Livewire component to mount in each half, after the slot. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:appearance.toggle` | default | Content of the button, between the two icons, in place of `label`. |
| `tk:appearance.menu` | default | Menu items written by hand. |
| `tk:appearance.view` | default | The content to preview, rendered once in each half. |

## Customization

Classes and attributes on a tag go to its root element, except on `tk:appearance`, which only takes prefixed attributes. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `style:` | `tk:appearance` | The inline `<style>` element. |
| `script:` | `tk:appearance` | The inline `<script>` element. |
| `system:` | `tk:appearance.selector` | The System button. |
| `light:` | `tk:appearance.selector` | The Light button. |
| `dark:` | `tk:appearance.selector` | The Dark button. |
| `selector:` | `tk:appearance.menu-item` | The selector at the end of the row. |
| `toggle:` | `tk:appearance.menu` | The toggle button. |
| `menu-item:` | `tk:appearance.menu` | The theme row inside the menu. |

`tk:appearance.menu` also takes the prefixes of `tk:avatar.menu` (`trigger:`, `menu:`, `avatar:`...).

```blade
<tk:appearance.selector size="sm" class="mx-auto" system:tooltip="Same as my device" />
```

## Accessibility

The selector follows the WAI-ARIA radio group pattern: the group has `role="radiogroup"` with the label "Theme", each button `role="radio"` with `aria-checked`, and only the checked one is in the tab order. The buttons have no text, so each takes its accessible name from its tooltip. The toggle is a plain button named by its tooltip, "Toggle light and dark mode", unless it has a `label`.

| Key | Action |
|-----|--------|
| Arrow Right / Arrow Down | Selector: applies the next mode and moves focus to it, wrapping from Dark to System. |
| Arrow Left / Arrow Up | Selector: applies the previous mode and moves focus to it, wrapping from System to Dark. |
| Tab | Moves into the selector, to the checked button, and out of it. |
| Enter, Space | Toggle: switches between light and dark. |

Arrow Left and Arrow Right are swapped in a right-to-left page. In the menu opened with the keyboard, focus lands on the "Theme" row and Tab moves from it into the selector.

## Behavior notes

- The choice is kept in `localStorage`, under `tallkit.appearance`, as `light` or `dark`. Choosing System removes the key, and a browser with nothing stored follows the operating system.
- Dark mode is the `dark` class on `<html>`, which also gets `color-scheme: dark`, so native scrollbars and form controls follow.
- The toggle only knows light and dark. Used while in `system` mode, it switches to the opposite of what is on screen and stores that, so the page stops following the system until System is picked in a selector or `apply('system')` is called.
- The reveal animation of the toggle uses the View Transitions API. Where the browser does not support it, or the user asked for reduced motion, the switch is instant.
- All controls on the page reflect the same state: changing the mode in one updates every selector, and a change made in another tab of the same site is applied to this one.
- The state lives in the browser and is untouched by Livewire updates; the server never knows the mode.
- `tk:appearance.menu` with `control="selector"` and no `items` or slot content renders nothing.
- `tk:appearance.view` is a full page of its own (it renders `tk:html`, without the appearance script): two halves side by side, "Dark" and "Light", with the same content in each, for comparing a component in both modes.

## Related

- `tk:avatar.menu`: the account menu alone, with no theme control.
- `tk:layout.sidebar` and `tk:layout.header`: page layouts that already place `tk:appearance.menu` in the header; their `appearance` prop is its `control`.
