# Tab

Tabs that switch between panels of content on the same page: a row (or column) of tab buttons, and one visible panel at a time. The selected tab can be bound to Livewire or Alpine.

```
tk:tab.group
├── tk:tab.tabs
│   └── tk:tab ...
└── tk:tab.panels
    └── tk:tab.panel ...
```

## Usage

```blade
<tk:tab.group>
    <tk:tab.tabs>
        <tk:tab name="profile" label="Profile" />
        <tk:tab name="billing" label="Billing" badge="2" />
        <tk:tab name="team" label="Team" />
    </tk:tab.tabs>

    <tk:tab.panels>
        <tk:tab.panel name="profile">Profile settings</tk:tab.panel>
        <tk:tab.panel name="billing">Billing settings</tk:tab.panel>
        <tk:tab.panel name="team">Team settings</tk:tab.panel>
    </tk:tab.panels>
</tk:tab.group>
```

Three tabs over a panel. Profile starts selected and its panel is shown; clicking Billing, or moving to it with the arrow keys, shows the billing panel instead. A tab and a panel are paired by `name`.

### Bound to Livewire

```blade
<tk:tab.group wire:model="section">...</tk:tab.group>
```

```php
public string $section = 'billing';
```

The group opens on the tab named by `$section`, and selecting another tab sets it, e.g. to `'team'`. Without a bound value, `selected` on a tab picks the initial one; without either, the first tab.

### Styles and orientation

```blade
<tk:tab.group variant="segmented">...</tk:tab.group>
<tk:tab.group variant="line">...</tk:tab.group>
<tk:tab.group orientation="vertical" variant="pills">...</tk:tab.group>
```

`segmented` puts the tabs in a gray pill with the selected one raised in white; `line` underlines the selected tab; `pills` makes rounded tabs, filled when selected. `vertical` stacks the tabs in a column beside the panels, moved with the up and down arrows.

## Props

### `tk:tab.group`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | boxed | `line`, `pills`, `segmented`, or the default tabs with a border joining the selected one to the panel. Inherited by the tabs. |
| `orientation` | string | `horizontal` | `vertical` stacks the tabs in a column. |
| `selectFirst` | bool | `true` | Selects the first tab when nothing else is selected. `false` starts with no panel shown. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Inherited by the tabs. |

`wire:model` and `x-model` bind the `name` of the selected tab.

### `tk:tab`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | generated | Pairs the tab with the panel of the same name, and is the bound value. |
| `label` | string \| slot | `null` | The tab text, translated. |
| `badge` | string \| slot | `null` | A badge after the label. |
| `selected` | bool | `false` | Selects the tab at first, when there is no bound value. |

Also accepts the [element props](shared-props.md#element-props) (`icon`, `suffix`...) and `disabled`, which skips the tab.

### `tk:tab.panel`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The tab it belongs to. |
| `selected` | bool | `false` | Shows the panel before the script starts, to avoid a flash of the wrong one. |

`tk:tab.tabs` and `tk:tab.panels` take `size`, `variant` and `orientation` from the group and only wrap their children.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:tab.group` | default | The tabs and the panels. |
| `tk:tab.tabs` | default | The tabs. |
| `tk:tab.panels` | default | The panels. |
| `tk:tab` | default | The tab content, in place of `label`. |
| `tk:tab.panel` | default | The panel content. |

## Events

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:tab.group` | `changed` | `{ name }` | The user selected another tab. Dispatched on the group; does not bubble. |

```blade
<tk:tab.group x-on:changed="$wire.loadTab($event.detail.name)">...</tk:tab.group>
```

## Customization

Classes and attributes on each tag go to its element. `tk:tab.tabs` is inside a horizontally scrollable wrapper, reached with `scrollable:`.

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `scrollable:` | `tk:tab.tabs` | The scrollable wrapper around the tab list. |

```blade
<tk:tab.tabs class="gap-4" scrollable:class="-mx-4 px-4">...</tk:tab.tabs>
```

## Accessibility

Follows the WAI-ARIA tabs pattern with automatic activation: `role="tablist"` with `aria-orientation`, tabs with `role="tab"`, `aria-selected` and `aria-controls`, and panels with `role="tabpanel"` labelled by their tab. Only the selected tab is in the tab order, and the selected panel is focusable.

| Key | Action |
|-----|--------|
| Arrow Right / Arrow Left | Moves to and selects the next / previous tab, wrapping around and skipping disabled ones. Reversed in right-to-left pages. |
| Arrow Down / Arrow Up | The same, with `orientation="vertical"`. |
| Home / End | Moves to and selects the first / last tab. |
| Tab | Moves from the selected tab into its panel. |

## Behavior notes

- Do not set `id` on a tab or a panel: the ids are generated from `name` to link them, and a custom one breaks the link.
- The group has `wire:ignore.self`: a Livewire update keeps the selected tab.
- Nested tab groups are independent: the keys and the selection of one do not reach the other.

## Related

- [Nav](nav.md): links that go to other pages.
- [Accordion](accordion.md): sections that open in place, several at once.
- [Stepper](stepper.md): the steps of a process.
