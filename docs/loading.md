# Loading

An animated spinner in the current text color, for a wait the user should see: a request running, content being loaded. Buttons, inputs and loadable content show it on their own; use the tag directly anywhere else.

## Usage

```blade
<tk:loading />
```

A 24px spinning arc in the color of the surrounding text, announced to screen readers as "Loading..." (translated).

### Variants

```blade
<tk:loading variant="dots" />
<tk:loading variant="bar" size="lg" />
<tk:loading variant="ring" class="text-blue-500" />
```

Three bouncing dots, three pulsing bars in a larger size, and an expanding blue ring. The other variants are `infinity` and `ball`.

### While Livewire works

```blade
<div class="flex items-center gap-2">
    <tk:button wire:click="sync" label="Sync now" :loading="false" />
    <tk:loading wire:loading wire:target="sync" label="Syncing" size="sm" />
</div>
```

The spinner shows next to the button only while `sync` runs, and is announced as "Syncing".

## Props

### `tk:loading`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | spinner | The animation: `dots`, `bar`, `ring`, `ball`, `infinity`, or the default spinning arc. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. Sets the width and height. |
| `label` | string | `Loading...` | The accessible name, translated. |
| `announce` | bool | `true` | Makes the spinner a polite live region (`role="status"`), so its appearance is announced. `false` makes it a plain image (`role="img"`), for spinners next to text that already says what is happening. |

## Customization

Classes and attributes go to the `<span>`. The spinner is drawn with the text color, so a text color class changes its color; a size class changes its size.

```blade
<tk:loading class="size-10 text-zinc-400" />
```

## Accessibility

The spinner has an `aria-label`. By default it is a polite live region, so screen readers announce it when it appears; with `:announce="false"` it is an image that is read only when reached.

## Behavior notes

- The animations are SVG masks over the text color: they need no JavaScript and follow dark mode with the text.

## Related

- [Button](button.md): shows this spinner by itself while its action runs.
- `tk:loadable`: shows it while content loads, and an error if loading fails.
- `tk:skeleton`: gray placeholders in the shape of the content, for longer waits.
- `tk:progress`: a bar for a wait whose progress is known.
