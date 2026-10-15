# Content

A row that lines up an icon, a title with a description under it, and actions at the end. Use it for the header of a block, a settings row, an empty state or an item in a list; it has no border, background or padding of its own, so it goes inside whatever box holds it. `tk:alert`, `tk:section`, `tk:status` and `tk:avatar.profile` are built on it.

## Usage

```blade
<tk:content icon="folder" title="Documents" description="12 files, updated today." />
```

The icon sits at the start of the row. The title, in bold, and the muted description under it fill the rest of the width. With two lines of text the icon aligns with the top of the title instead of the middle of the block.

### Actions

```blade
<tk:content title="Two-factor authentication" description="Ask for a code from your phone when signing in.">
    <x-slot:actions>
        <tk:button size="sm" wire:click="enableTwoFactor">Enable</tk:button>
    </x-slot:actions>
</tk:content>
```

The `actions` slot is placed at the end of the row and keeps its width while the text wraps. Clicking Enable calls the Livewire action.

### Body text and lists

```blade
<tk:content title="Choose a password" :description="['At least 8 characters', 'One number', 'One symbol']">
    It is used to sign in on every device.
</tk:content>
```

The default slot renders as a paragraph under the title. An array `description` renders as a bulleted list, which comes after the default slot.

### Custom icon

```blade
<tk:content title="Ana Souza" description="ana@example.com">
    <x-slot:icon>
        <tk:avatar name="Ana Souza" />
    </x-slot:icon>
</tk:content>
```

The `icon` slot takes any content in place of an icon name, here the avatar with her initials, in the same spot at the start of the row.

### Rendering from data

```blade
<div class="divide-y divide-zinc-100">
    @foreach ($members as $member)
        <tk:content icon="user" :title="$member->name" :description="$member->email" class="py-3" wire:key="member-{{ $member->id }}">
            <x-slot:actions>
                <tk:badge>{{ $member->role }}</tk:badge>
            </x-slot:actions>
        </tk:content>
    @endforeach
</div>
```

One row per member, separated by a line, with the role at the end of each row.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string \| slot | `null` | The title, in bold, translated. |
| `description` | string \| array \| slot | `null` | Muted text under the title, translated. An array renders as a bulleted list. |
| `icon` | string \| slot | `null` | An icon name, or any content, at the start of the row. |
| `prepend` | slot | `null` | Content above the title. |
| `append` | slot | `null` | Content under the description and the body. |
| `actions` | slot | `null` | Content at the end of the row, such as buttons or a badge. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the font size and the spacing, and is passed to the icon, title and texts. |

## Slots

| Slot | Description |
|------|-------------|
| default | Text under the description, in the same muted style. |
| `actions` | Content at the end of the row. |
| `prepend`, `append` | Content above the title / at the end of the text column. |

`title`, `description` and `icon` also accept a slot in place of the prop.

## Customization

Classes and attributes go to the row's `<div>`. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `icon:` | The icon, or the element wrapping the `icon` slot. |
| `container:` | The column holding prepend, title, description, body and append. |
| `title:` | The title. It is a `<p>`; `title:level="2"` makes it an `<h2>`. `title:icon` and `title:badge` add an icon and a badge to it. |
| `description:` | The description text and the paragraph of the default slot. |
| `list:` | The list, when `description` is an array. `list:item:` reaches each item's text. |
| `actions:` | The element wrapping the actions. |

```blade
<tk:content title="Billing" title:level="2" description="Plan and invoices." description:class="text-xs" actions:class="gap-1">
    <x-slot:actions>
        <tk:button size="sm">Change plan</tk:button>
    </x-slot:actions>
</tk:content>
```

## Accessibility

The row is a plain `<div>` with no role and no keyboard interaction. The title is a `<p>`: pass `title:level` to make it a real heading when the row heads a part of the page. The icon is hidden from screen readers (`aria-hidden`); `icon:aria-label` names it and gives it `role="img"`. The `icon` slot is wrapped in an element hidden the same way: `icon:aria-label` removes the `aria-hidden` and puts the label on that wrapper, which has no role, so when the content must be announced give it a name of its own.

## Behavior notes

- When the row has no icon, title, description, prepend, append, actions or default slot, nothing renders, not even the `<div>` with its attributes.
- The icon and the actions are centered on the text when the column has a single part (only a title, for example), and aligned to its top when it has two or more.
- The default slot is wrapped in a `<p>`, like the description. Give it inline content: a `<div>` or a list inside it ends the paragraph early in the browser.
- The `description:` attributes go to both the description and the default slot's paragraph; an `id` stays only on the description.
- A string `title` or `description` with line breaks keeps them. `description="0"` renders `0`; `false` renders nothing.
- Inside Livewire it has no state of its own: every update renders it again from the props.

## Related

- [Card](card.md): a bordered, padded box with the same title, description and actions in its header.
- [Alert](alert.md): a colored message box built on this row, with a close button and a timer.
- [Avatar](avatar.md): `tk:avatar.profile` puts the person's avatar, name and email in this row.
- `tk:section`: a header row like this one, with the content of the section under it.
- `tk:heading`, `tk:text`: the title or the description alone, without the row.
