# TALLKit

A set of Blade components for the TALL stack (Tailwind, Alpine.js, Laravel, Livewire).

## Installation

```bash
composer require datalogix/tallkit
```

The package registers `TALLKit\TALLKitServiceProvider` automatically via Laravel package discovery. Include the compiled assets in your app (see `dist/tallkit.js` / `dist/tallkit.min.js`) and the `resources/css` stylesheets in your Tailwind build.

## Usage

Components are anonymous Blade views registered under the `tk:` tag namespace:

```blade
<tk:button variant="solid" icon="check">
    Save
</tk:button>

<tk:alert type="success" title="Done">
    Your changes were saved.
</tk:alert>
```

The standard Laravel `<x-tallkit::*>` syntax also works if you prefer it.

## Development

```bash
composer install
pnpm install

composer test   # Pint + PHPUnit
pnpm lint       # ESLint
pnpm build      # Bundle resources/js into dist/
```

## Roadmap

- slider
- upload
- paginator (add select per page)

### Add

- auth (2fa, register by google/github/)
- Notifications
- Editor
- Kanban
- Pillbox
- Color Picker
- Date Picker
- Time Picker
