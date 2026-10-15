# List

A bulleted or numbered list, from an array of texts or from `<li>` elements written in the slot. Use it for short lists in body text, such as requirements or steps.

## Usage

```blade
<tk:list :items="['At least 8 characters', 'One number', 'One symbol']" />
```

A `<ul>` with three bulleted items, each in the body text style. The bullets sit inside the text column, aligned with the paragraphs around it.

### Numbered

```blade
<tk:list marker="decimal" :items="['Create an account', 'Confirm your email', 'Invite your team']" />
```

An `<ol>` numbered 1, 2, 3.

### Items written by hand

```blade
<tk:list marker="none" class="space-y-2">
    @foreach ($files as $file)
        <li wire:key="file-{{ $file->id }}">
            <tk:link :href="$file->url" :label="$file->name" />
        </li>
    @endforeach
</tk:list>
```

The slot takes `<li>` elements with any content; here a link per file, with no marker and some space between items.

## Props

### `tk:list`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | `null` | The item texts, each translated. |
| `marker` | string | `disc` | `disc` for bullets, `decimal` for numbers (an `<ol>`), `none` for no marker. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:list` | default | `<li>` elements, rendered before the `items`. |

## Customization

Classes and attributes go to the `<ul>` or `<ol>`. To reach the items from `items`, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `li:` | `tk:list` | Each `<li>`. |
| `item:` | `tk:list` | The text inside each item, a `tk:text` (`item:variant="subtle"`, `item:icon="check"`...). |

```blade
<tk:list marker="none" :items="['Unlimited projects', 'Priority support']" item:icon="check" item:class="text-green-700" />
```

## Accessibility

A native list, so screen readers announce the number of items. With `marker="none"` it also has `role="list"`, because Safari stops announcing lists whose markers are removed by CSS.

## Behavior notes

- With no `items` and no slot content, nothing is rendered.
- Each item from `items` is translated, like the labels of the other components.

## Related

- [Breadcrumb](breadcrumb.md): a list of links to the current page's parents.
- `tk:text`: the text each item is rendered with.
