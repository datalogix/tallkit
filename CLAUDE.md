# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

TALLKit (`datalogix/tallkit`) is a Laravel package of anonymous Blade components for the TALL stack (Tailwind 4, Alpine, Laravel 12/13, optional Livewire 4). There is no app here: components are exercised by the PHPUnit suite (Orchestra Testbench) and by the separate demo app in `../app`, which has one page per component.

## Commands

```bash
composer setup                 # composer install + pnpm install + pnpm build
composer test                  # runs lint (pint + eslint) then phpunit
composer lint                  # vendor/bin/pint && pnpm lint
vendor/bin/phpunit                                  # tests only
vendor/bin/phpunit --filter test_name               # single test
vendor/bin/phpunit tests/Unit/HelpersTest.php       # single file
vendor/bin/phpunit --testsuite Unit                 # Unit or Feature
pnpm build                     # builds dist/ (UMD tallkit.js + tallkit.min.js, ESM tallkit.esm.js, manifest.json)
pnpm watch                     # rebuilds both bundles on change
```

`dist/` is committed and served to apps as-is: run `pnpm build` after any change in `resources/js`. For timing measurements run PHP with `XDEBUG_MODE=off`.

## Architecture

**Components are anonymous Blade views** in `resources/views/<component>/<component>.blade.php` (sub-parts as sibling files, e.g. `accordion/item.blade.php`), registered under the `tallkit` namespace. Apps can override any view by mirroring its path in `resources/views/tallkit/`, which is registered first.

**`tk:` tag syntax** comes from two Blade precompilers registered in `TALLKitServiceProvider::bootTagCompiler()`:
- `View/Compilers/ComponentTagCompiler` turns `<tk:foo>` into `tallkit::foo` components (and supports `slot="name"` on self-closing tags).
- `View/Compilers/PropsCompiler` rewrites `@props(...)` **only in package views and their app overrides in `resources/views/tallkit/`** to fix forwarded bound props being escaped twice, kebab-case props not reaching their camelCase name, and a prop written on the tag beating the same prop forwarded through `:attributes` (a slot still wins). Values written on a tag are defaults the caller can replace; to force one, drop the key from the forwarded bag (`->except('icon')`).

**`TALLKit` class (`src/TALLKit.php`)** is a singleton exposed as the `TALLKit` facade/alias and used directly in views (`TALLKit::elementProps()`, `TALLKit::isColor()`, `TALLKit::dataKey()`...). Its logic lives in the `src/Concerns/InteractsWith*` traits, one per domain (field, color, icon, toast, modal, upload, ...). Global helpers in `src/helpers.php` (`has_content`, `string_value`, `in_livewire`, `unless_livewire`, `toast`, `alert`, `route_detect`).

**Attribute forwarding** between nested components goes through `ComponentAttributeBag` macros in `View/ComponentAttributeBagMixin.php` (`withElementProps`, `withFieldProps`, `withFieldControlProps`, `withProps`, `classes`, `dataKey`, `fieldContext`, ...). Prefixed attributes like `icon:size` or `content:class` target sub-elements. Most visual components ultimately render through `tk:element`. Classes are merged with tailwind-merge via `ClassBuilder` and cached across requests by `ClassCache` (persisted on app terminate).

**Request-scoped state** (generated ids, field bags, icon/avatar lookups) lives in `scoped` container bindings named `tallkit.*`, registered in the service provider.

**Livewire integration** is enabled only when Livewire is installed: `src/Livewire` adds component mixins (toast, modal, alert, notification, upload), the `DateRange` property synthesizer, notification actions and toast redirects.

**Assets**: `Assets/AssetManager` registers `@tallkitScripts` and the `/tallkit/tallkit.js` route (`AssetController` serves `dist/`, versioned by `dist/manifest.json`); `AssetInjector` injects the script tag before `</body>` according to `tallkit.inject_assets`. Config defaults are in `config/tallkit.php`; translations in `lang/`.

**JavaScript** (`resources/js`): `tallkit.js` is the UMD entry (reads `data-*` options from its script tag), `esm.js` exports an `Alpine.plugin`. The global object is defined in `core.js` and exposed as `window.tallkit` (also `TALLKit`, `TK`, `tk`). `alpine.js` loads Alpine from unpkg when absent, adds the `$tk`/`$tallkit` magics and the `tallkit` store, and auto-registers every function exported from `components/*.js` as `Alpine.data`. Shared behavior lives in `mixins/` and `utils/`.

**CSS** (`resources/css/tallkit.css`) is imported by the app's Tailwind entry and `@source`s the package views; colors are CSS-variable palettes (`tk-color-*`).

## Conventions

- Names derived from the prefix (`data-tallkit-*` attributes, `tallkit:*` events, `tallkit.*` storage keys) always come from the paired PHP/JS helpers (`TALLKit::PREFIX`, `dataKey`, `eventName`, ...), never string literals.
- `string|slot` props are tested with `has_content()` in views, not truthiness or `isset` (`false` is empty, `0` is content).
- `with*Props(get_defined_vars())` puts the view's own props back in the bag, so a value written on the tag after it (`:icon="..."`) is only forced with `->except()`.
- A `null` in a forwarded bag (from `merge` or written on the tag) beats the child's own default for that key: send a key only when it has a value (`merge($cond ? ['aria-current' => 'page'] : [])`).
- Each component is documented in `docs/<component>.md`; the README only indexes them. Project skills in `.claude/skills/` cover documenting (`document-component`), reviewing (`review-component`) and browser-testing (`browser-test`, against `../app`) a component.
- `docs/`, `tests/`, `.claude/` and JS tooling are `export-ignore`d in `.gitattributes`; keep new dev-only files listed there.
