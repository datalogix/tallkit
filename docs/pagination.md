# Pagination

The page links under a list or a table: a "Showing 1 to 10 of 97 results" summary, previous, next, first and last buttons, and the page numbers with gaps. It takes a Laravel paginator, and inside a Livewire component with `WithPagination` it changes page without a reload.

```
tk:pagination
├── tk:pagination.first-page
├── tk:pagination.prev-page
├── tk:pagination.page ...
├── tk:pagination.next-page
├── tk:pagination.last-page
└── tk:pagination.per-page
```

`tk:pagination` renders the inner tags itself; they are written by hand only inside its `links` slot.

## Usage

```blade
@foreach ($users as $user)
    ...
@endforeach

<tk:pagination :paginator="$users" />
```

```php
use Livewire\WithPagination;

public function render()
{
    return view('users', ['users' => User::query()->paginate(10)]);
}
```

A line on top, then "Showing 1 to 10 of 97 results" at the start and the links at the end: first, previous, the page numbers around the current one with a gap before the last two, next and last. The current page is highlighted and disabled. Inside Livewire, a click changes the page and scrolls the page to the top; outside it, the links are plain URLs.

### Choosing the page size

```blade
<tk:pagination :paginator="$users" :per-page="[10, 25, 50]" />
```

```php
public int $perPage = 10;

public function render()
{
    return view('users', ['users' => User::query()->paginate($this->perPage)]);
}
```

A select with 10, 25 and 50 next to the summary. Inside Livewire it is bound to `$perPage` (or to `per-page-name`); outside it, it reloads the page with `?perPage=25` and back on page 1.

### Simple and cursor paginators

```blade
<tk:pagination :paginator="$posts" />
```

With `simplePaginate()` or `cursorPaginate()` there is no total, so only the previous and next buttons are shown. When everything fits in one page, a "Total: 3 results" line is shown instead.

### A plain collection

```blade
<tk:pagination :paginator="$orders" :separator="false" dense />
```

A collection or array that is not paginated shows only its total, here without the line on top and with less padding: useful under a table that lists everything.

## Props

### `tk:pagination`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `paginator` | Paginator \| Collection \| array | required | The paginator (`paginate()`, `simplePaginate()`, `cursorPaginate()`), or a collection to show the total of. |
| `perPage` | array | `null` | Page sizes to choose from in a select. |
| `perPageName` | string | `perPage` | The Livewire property, or the query string key, the page size is bound to. |
| `total` | bool | `true` | `false` hides the results summary. |
| `firstPage` | bool | `true` | `false` removes the first page button. |
| `lastPage` | bool | `true` | `false` removes the last page button. |
| `eachSide` | int | `3` | How many page numbers are shown on each side of the current one. |
| `scrollTo` | string \| false | `body` | A selector of the element scrolled into view on a page change inside Livewire. `false` keeps the scroll. |
| `separator` | bool | `true` | Draws a line above the pagination. |
| `dense` | bool | `false` | Less padding, as in a dense table. |
| `size` | string | `md` | `xs`, `sm`, `md`, `lg`, `xl`, `2xl` or `3xl`. |

### `tk:pagination.first-page`, `.prev-page`, `.next-page` and `.last-page`

[`tk:button`](button.md)s with an icon and a tooltip ("Previous page"...), disabled at the ends. `icon` and `tooltip` change the arrow and its text; they also take the other [element props](shared-props.md#element-props).

### `tk:pagination.page` and `tk:pagination.per-page`

A page number button (`page`), and the page size select (`options`, `name`).

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:pagination` | `results` | Replaces the whole pagination: a function receiving the paginator, for a layout of your own. |
| `tk:pagination` | `links` | Replaces the page numbers: a function receiving the paginator and the elements (page arrays and `...` gaps). |

## Customization

Classes and attributes on the tag go to the outer element. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:pagination` | The element with the padding. |
| `nav:` | `tk:pagination` | The `<nav>`. |
| `summary:`, `results:`, `total:` | `tk:pagination` | The summary area, the "Showing..." text, the short total shown on small screens. |
| `per-page:` | `tk:pagination` | The page size select. |
| `pages:` | `tk:pagination` | The group of buttons. |
| `page:`, `dots:` | `tk:pagination` | Each page number, each gap. |
| `first-page:`, `prev-page:`, `next-page:`, `last-page:` | `tk:pagination` | The arrow buttons (`prev-page:icon="arrow-left"`). |
| `separator:` | `tk:pagination` | The line on top. |

```blade
<tk:pagination :paginator="$users" size="sm" :each-side="1" page:variant="ghost" prev-page:label="Previous" />
```

## Accessibility

A `<nav>` named "Pagination Navigation". The current page has `aria-current="page"`; each button has a name ("Go to page 3", "Next page"); the disabled ends have `aria-disabled`. The page size select is named "Items per page".

## Behavior notes

- On small screens the page numbers and the first and last buttons are hidden, leaving the previous and next buttons and a short "Total: 97 results".
- Inside Livewire the buttons are disabled while the page changes, so a second click does not skip a page.
- With a single page, only a "Total: 3 results" line is shown.

## Related

- `tk:table`: the table this usually goes under, which can render it for you.
- [Button](button.md): the buttons the links are made of.
