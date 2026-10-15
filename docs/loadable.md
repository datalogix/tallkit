# Loadable

A container with four states, loading, done, empty and failed, that shows a spinner, the content, a "Nothing to show" text or an error message for each. The charts, editors and [`tk:fetchable`](fetchable.md) are built on it; use it for content of your own that loads in the browser.

## Usage

```blade
<tk:loadable x-init="load(async () => { await new Promise((r) => setTimeout(r, 1000)) })">
    <p>Loaded!</p>
</tk:loadable>
```

A spinner for one second, then "Loaded!". `load(callback)` shows the spinner, runs the callback, and shows the content when it finishes, or the error message with the error's text when it throws.

### Loading data

```blade
<div x-data="{ total: null }">
    <tk:loadable x-init="load(async () => { total = (await (await fetch('/api/report')).json()).total })">
        <p>Total: <strong x-text="total"></strong></p>
    </tk:loadable>
</div>
```

The callback stores what it fetched in the outer Alpine scope, and the content reads it once loading finishes. A failed request (a network error, or a JSON parse error) shows the error message instead.

### Your own states

```blade
<tk:loadable x-init="load(fetchItems)">
    <x-slot:loading>
        <tk:skeleton class="h-24" />
    </x-slot:loading>

    <x-slot:error>
        <p>Could not load. <button x-on:click="load(fetchItems)">Try again</button></p>
    </x-slot:error>

    <x-slot:empty>
        <p>No items yet.</p>
    </x-slot:empty>

    <ul>...</ul>
</tk:loadable>
```

Each named slot replaces the default content of that state.

## Props

### `tk:loadable`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `silent` | bool | `false` | Skips the spinner: the previous content stays until the new one is ready. |

`x-data` on the tag replaces the default `loadable` Alpine component with another registered one that provides the same methods, as the chart and editor components do.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:loadable` | default | The content, shown when loading finished. Only its first root element is rendered. |
| `tk:loadable` | `completed` | Replaces the default slot. |
| `tk:loadable` | `loading` | Replaces the spinner. |
| `tk:loadable` | `error` | Replaces the error message. `error` holds the error. |
| `tk:loadable` | `empty` | Replaces the "Nothing to show" text, shown after `clear()`. |

## Events

Events are dispatched on the `tk:loadable` element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:loadable` | `started` | | Loading started (not with `silent`). |
| `tk:loadable` | `completed` | | Loading finished. |
| `tk:loadable` | `failed` | | Loading threw an error. |

```blade
<tk:loadable x-init="load(fetchItems)" x-on:failed="$wire.reportError()">...</tk:loadable>
```

## Customization

Classes and attributes go to the wrapper `<div>`. To reach the default states, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loading:` | `tk:loadable` | The spinner ([`tk:loading`](loading.md)): `loading:variant="dots"`. |
| `error:` | `tk:loadable` | The error message ([`tk:error`](error.md)). |
| `empty:` | `tk:loadable` | The "Nothing to show" text. |

```blade
<tk:loadable x-init="load(fetchItems)" class="min-h-32" loading:variant="dots" loading:class="mx-auto">...</tk:loadable>
```

## Accessibility

The spinner and the error message are polite live regions, so the start and the failure of loading are announced.

## Behavior notes

- Each state is rendered with `x-if`: the content does not exist in the page until loading finishes, so Alpine expressions inside it can read data the callback set.
- Only the first root element of each slot is rendered; wrap several elements in one.
- A new `load()` cancels the result of the previous one: only the last finishes the loading.
- The wrapper has `wire:ignore`: a Livewire update does not touch what was loaded.

## Related

- [Fetchable](fetchable.md): loads a URL and shows the response, built on this tag.
- [Loading](loading.md): the spinner alone.
- [Chart](chart.md): charts that load their library through this tag.
