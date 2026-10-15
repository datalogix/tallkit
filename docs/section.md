# Section

A titled block of a page: a title with an optional icon and badge, a subtitle, a description, actions at the end of the title row, a line, and the content under it. Forms, modals, settings pages and the auth screens use it for their layout.

## Usage

```blade
<tk:section title="Profile" subtitle="This information is public." title:level="2">
    <tk:input name="name" />
    <tk:input name="bio" />
</tk:section>
```

A `<section>` with "Profile" in the heading style, "This information is public." in muted text under it, a line, and the two fields with even spacing.

### Actions, icon and badge

```blade
<tk:section title="API keys" icon="key" badge="Beta" subtitle="Keys give full access to your account." title:level="2">
    <x-slot:actions>
        <tk:button icon="plus" label="New key" wire:click="create" />
    </x-slot:actions>

    <tk:table :rows="$keys" />
</tk:section>
```

The icon before the title, a "Beta" badge after it, and the "New key" button at the end of the title row. The line under the header is left out when the content is a card or a table, which bring their own border.

### Content without a line

```blade
<tk:section title="Danger zone" :separator="false" description="These actions cannot be undone.">
    <tk:danger-zone />
</tk:section>
```

A description paragraph under the subtitle, and no line between the header and the content.

## Props

### `tk:section`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string \| slot | `null` | The title, translated. |
| `subtitle` | string \| slot | `null` | Muted text under the title. |
| `description` | string \| slot | `null` | A paragraph under the subtitle. |
| `actions` | slot | `null` | Controls at the end of the title row. |
| `prepend` | slot | `null` | Content before the title. |
| `append` | slot | `null` | Content after the description, in the header. |
| `content` | string \| slot | `null` | Content before the slot. |
| `separator` | bool | auto | The line between the header and the content. By default it is drawn when the header has a title with other details, or actions, and there is content; never above a card or a table. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the text sizes and the spacing. |

`icon` and `badge` on the tag go to the title.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:section` | default | The content. |
| `tk:section` | `title`, `subtitle`, `description`, `actions`, `prepend`, `append`, `content` | As the props of the same name. |

## Customization

Classes and attributes on the tag go to the `<section>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `header:` | `tk:section` | The header row. |
| `title:` | `tk:section` | The title, a [`tk:heading`](heading.md) (`title:level="2"`, `title:size`...). |
| `subtitle:` | `tk:section` | The subtitle. |
| `container:`, `list:` | `tk:section` | The element around the title and texts, and the list they are in. |
| `actions:` | `tk:section` | The actions area. |
| `separator:` | `tk:section` | The line. |
| `content:` | `tk:section` | The content area. |

```blade
<tk:section title="Billing" title:level="2" title:size="lg" class="rounded-lg border p-6" content:class="grid grid-cols-2 gap-4">...</tk:section>
```

## Accessibility

A native `<section>`. The title is styled as a heading but rendered as a paragraph unless `title:level` is set: give it a level that fits the page outline (`title:level="2"`) so screen reader users can navigate to it.

## Behavior notes

- Without a title, details or actions, no header and no line are rendered: only the content.
- A string `content` is translated.

## Related

- [Card](card.md): the same layout in a bordered box.
- [Form](form.md): `tk:form.section` is a form laid out as a section.
- [Page](page.md): a section with a side menu.
- [Fieldset](fieldset.md): a group of fields with a legend, inside a form.
