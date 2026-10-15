# TALLKit

A set of Blade components for the TALL stack (Tailwind, Alpine.js, Laravel, Livewire).

## Requirements

- PHP 8.2 or later
- Laravel 12 or 13
- Tailwind CSS 4
- Livewire 4, optional: needed for `wire:model` on the fields, uploads, toasts and modals opened from components, notifications and `DateRange` properties

## Installation

```bash
composer require datalogix/tallkit
```

The service provider is registered automatically by Laravel package discovery.

### Styles

Import the package stylesheet in your Tailwind entry file, after `tailwindcss`:

```css
@import "tailwindcss";
@import "../../vendor/datalogix/tallkit/resources/css/tallkit.css";

@custom-variant dark (&:where(.dark, .dark *));
```

The stylesheet registers the package views as a Tailwind source, so the classes the components use end up in your build. The `dark` variant line makes dark mode follow the `dark` class instead of the system preference.

### Scripts

Nothing to add: the script tag is injected before `</body>` on every HTML page that renders a component.

The script uses the Alpine.js already on the page, such as the one Livewire ships. When there is none, it loads Alpine and the plugins it needs (collapse, focus, persist, resize, mask, sort) from unpkg; set `load_alpine` to `false` in the config to prevent that.

To place the tag yourself, set `inject_assets` to `false` and add the directive to your layout:

```blade
@tallkitScripts
```

A CSP nonce set through `Vite::useCspNonce()` is applied to the tag automatically; `@tallkitScripts(['nonce' => $nonce])` passes one explicitly.

If your app bundles Alpine without exposing `window.Alpine`, register the package as an Alpine plugin instead:

```js
import Alpine from "alpinejs";
import tallkit from "../../vendor/datalogix/tallkit/dist/tallkit.esm.js";

Alpine.plugin(tallkit);
Alpine.start();
```

## Usage

Components are anonymous Blade views registered under the `tk:` tag namespace:

```blade
<tk:button variant="primary" icon="check">
    Save
</tk:button>

<tk:alert type="success" title="Done">
    Your changes were saved.
</tk:alert>
```

The standard Laravel `<x-tallkit::*>` syntax also works if you prefer it.

## Components

Each component is documented in its own file under [`docs/`](docs/).

- [Accordion](docs/accordion.md)
- [Address](docs/address.md)
- [Alert](docs/alert.md)
- [ApexCharts](docs/apexcharts.md)
- [Appearance](docs/appearance.md)
- [Aside](docs/aside.md)
- [Autocomplete](docs/autocomplete.md)
- [Avatar](docs/avatar.md)
- [Badge](docs/badge.md)
- [Brand](docs/brand.md)
- [Breadcrumb](docs/breadcrumb.md)
- [Button](docs/button.md)
- [Calendar](docs/calendar.md)
- [Card](docs/card.md)
- [Carousel](docs/carousel.md)
- [Chart](docs/chart.md)
- [Chart.js](docs/chartjs.md)
- [Checkbox](docs/checkbox.md)
- [Clearable](docs/clearable.md)
- [Color picker](docs/color-picker.md)
- [Combobox](docs/combobox.md)
- [Command](docs/command.md)
- [Composer](docs/composer.md)
- [Container](docs/container.md)
- [Content](docs/content.md)
- [Context](docs/context.md)
- [Copyable](docs/copyable.md)
- [Credit card](docs/credit-card.md)
- [Danger zone](docs/danger-zone.md)
- [Date picker](docs/date-picker.md)
- [Display](docs/display.md)
- [Dropdown](docs/dropdown.md)
- [ECharts](docs/echarts.md)
- [Editor.js](docs/editorjs.md)
- [Element](docs/element.md)
- [Error](docs/error.md)
- [Fetchable](docs/fetchable.md)
- [Field](docs/field.md)
- [Fieldset](docs/fieldset.md)
- [Footer](docs/footer.md)
- [Form](docs/form.md)
- [Frappe Charts](docs/frappe-charts.md)
- [Full Calendar](docs/full-calendar.md)
- [Google](docs/google.md)
- [Header](docs/header.md)
- [Heading](docs/heading.md)
- [Highlight.js](docs/highlightjs.md)
- [Html](docs/html.md)
- [Icon](docs/icon.md)
- [Input](docs/input.md)
- [Kbd](docs/kbd.md)
- [Label](docs/label.md)
- [Layout](docs/layout.md)
- [Legend](docs/legend.md)
- [Link](docs/link.md)
- [List](docs/list.md)
- [Listbox](docs/listbox.md)
- [Loadable](docs/loadable.md)
- [Loading](docs/loading.md)
- [Main](docs/main.md)
- [Menu](docs/menu.md)
- [Modal](docs/modal.md)
- [Money](docs/money.md)
- [Nav](docs/nav.md)
- [Notification](docs/notification.md)
- [OTP](docs/otp.md)
- [Page](docs/page.md)
- [Pagination](docs/pagination.md)
- [Password](docs/password.md)
- [Popover](docs/popover.md)
- [Pretty Print JSON](docs/pretty-print-json.md)
- [Progress](docs/progress.md)
- [Quill](docs/quill.md)
- [Radio](docs/radio.md)
- [Section](docs/section.md)
- [Select](docs/select.md)
- [Separator](docs/separator.md)
- [Sidebar](docs/sidebar.md)
- [Similar](docs/similar.md)
- [Skeleton](docs/skeleton.md)
- [Slider](docs/slider.md)
- [Spacer](docs/spacer.md)
- [Status](docs/status.md)
- [Stepper](docs/stepper.md)
- [Submit](docs/submit.md)
- [Switch](docs/switch.md)
- [Tab](docs/tab.md)
- [Table](docs/table.md)
- [Terms](docs/terms.md)
- [Text](docs/text.md)
- [Textarea](docs/textarea.md)
- [Time](docs/time.md)
- [Time picker](docs/time-picker.md)
- [TinyMCE](docs/tinymce.md)
- [Tiptap](docs/tiptap.md)
- [Toast](docs/toast.md)
- [Toggle](docs/toggle.md)
- [Transition](docs/transition.md)
- [Two-factor](docs/two-factor.md)
- [Upload](docs/upload.md)

## Configuration

Publish the config file to change the defaults:

```bash
php artisan vendor:publish --tag=tallkit-config
```

| Key               | Default       | What it controls                                                                                                                                    |
| ----------------- | ------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| `inject_assets`   | `'auto'`      | Where the script tag is injected: `'auto'` on pages that render a component, `true` on every HTML page, `false` never (use `@tallkitScripts`).      |
| `load_alpine`     | `true`        | Whether the script loads Alpine.js when the page has none.                                                                                          |
| `cache_classes`   | `true`        | Caches merged Tailwind class lists between requests.                                                                                                |
| `colors`          | `[]`          | Your own color names, each a `.tk-color-{name}` class.                                                                                              |
| `avatar.unavatar` | `true`        | Looks avatars up on unavatar.io, sending the email or username to that service. `false` shows only URLs as images.                                  |
| `address.lookup`  | `true`        | Zipcode lookup from the browser through viacep.com.br and brasilapi.com.br. A URL of your own with `{zipcode}` replaces them; `false` turns it off. |
| `masks`           | a `pt_BR` set | Input masks applied by field name, per app locale.                                                                                                  |
| `tooltip`         |               | Default delay, position, alignment, arrow, variant and size of tooltips.                                                                            |
| `icon`            |               | Iconify collections tried for an icon name given without one, and how long a missing icon is remembered.                                            |
| `upload`          |               | The upload route used by the rich text editors: disk, directory, allowed extensions, size limits and link lifetimes.                                |

`avatar.unavatar` and `address.lookup` send user data to third parties by default; turn them off if that does not fit your privacy policy.

## Customizing the views

A view placed in `resources/views/tallkit/`, mirroring the path it has in the package's `resources/views/`, replaces the package's version of that component.

## Commands

| Command                             | What it does                                                                                                                |
| ----------------------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| `php artisan tallkit:icons`         | Fetches the icons the views use ahead of time, so no page waits on Iconify for them. Also runs with `php artisan optimize`. |
| `php artisan tallkit:prune-uploads` | Deletes the files sent to the upload route that nothing uses anymore.                                                       |
