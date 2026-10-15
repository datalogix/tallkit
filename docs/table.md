# Table

A data table from an array, a collection, a query or a model: column headers, rows, sorting by the URL, pagination, and per-column cell templates. It can add row selection, expandable rows, and columns the user can hide, reorder, resize and pin, remembered in the browser. For full control, the table parts can also be written by hand.

```
tk:table
├── tk:table.columns
│   └── tk:table.column ...
├── tk:table.rows
│   └── tk:table.row
│       └── tk:table.cell ...
└── tk:table.footer
```

## Usage

```blade
<tk:table :rows="App\Models\User::query()" :cols="['name' => 'Name', 'email' => 'Email', 'created_at' => 'Joined']" sortable />
```

A table with Name, Email and Joined headers, 15 users per page and the pagination under it. Each header is a link that sorts by its column (`?sortBy=email&sortDirection=asc`), showing the direction and setting `aria-sort`; dates are shown in the app's locale, booleans as a check or a cross, and every value is escaped.

### Rows from data

```blade
<tk:table :rows="$orders" :cols="['number' => 'Order', 'customer.name' => 'Customer', 'total_formatted' => 'Total', 'actions' => '']" />
```

Rows can be arrays, models or a paginator. A column key reads the value with dot notation (`customer.name`); a `<key>_formatted` attribute on the row is used in place of `<key>` when it exists. A column named `actions` is narrow and centered. Without `cols`, the columns come from the first row, or from the model (its visible, or fillable plus timestamps, attributes), leaving out `id` and anything that looks secret (`password`, `token`, `api_key`...).

### Custom cells

```blade
@php
    $roleCell = fn ($row) => view('users.role-badge', ['user' => $row]);
    $actionsCell = fn ($row) => view('users.actions', ['user' => $row]);
@endphp

<tk:table :rows="$users" :cols="['name' => 'Name', 'role' => 'Role', 'actions' => '']" :row_role="$roleCell" :row_actions="$actionsCell" />
```

`row_<key>` replaces the content of that column's cells: a closure receives any of `row`, `value`, `key`, `col`, `cols`, `rows` and `index` by name; any other value is printed as is. `col_<key>` does the same for a header.

### Selection and expandable rows

```blade
<tk:table
    :rows="$users"
    :cols="['name' => 'Name', 'email' => 'Email']"
    row-selection
    x-effect="$wire.selected = selectedIds"
    :row-expanded="fn ($user) => view('users.details', ['user' => $user])"
/>
```

```php
public array $selected = [];
```

A checkbox column with a "select all" box in the header, and a chevron column that opens a full-width row of details under each row. `selectedIds` holds the `id` (or `row-key`) of the checked rows, across pages; the `x-effect` keeps `$selected` in sync with it, e.g. `['1', '3']`.

### Columns the user controls

```blade
<tk:table :rows="$users" toggleable draggable resizable pinnable persist="users" />
```

Each header gets a menu to hide the column, pin it to the left, a grip to drag it to another place and an edge to resize it. A "Show hidden columns" button appears while any is hidden. With `persist`, the choices are kept in the browser under that key.

## Props

### `tk:table`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `rows` | array \| Collection \| Paginator \| query \| model | `null` | The rows. A query or model is paginated (15 per page). |
| `resource` | string \| model | `null` | A model class or instance, used as `rows`. |
| `cols` | array | from the rows | The columns: `key => label`, a list of keys, or `key => [props]` (`label`, `sortable`, `align`, `sticky`, `hidden`, `translate`...). |
| `sortable` | bool | `false` | Makes every column but `actions` sortable through the URL (`sortBy`, `sortDirection`), and sorts a query by it. |
| `pagination` | bool | `true` | `false` hides the pagination under the table. |
| `rowSelection` | bool \| slot | `false` | Adds a checkbox column. A slot replaces the checkbox in each row. |
| `selectAll` | bool \| slot | `true` with selection | The header checkbox. `false` leaves the header cell empty; a slot replaces it. |
| `rowKey` | string | `id` | The row attribute that identifies a row (`data-id`, `wire:key`, selection). |
| `rowExpanded` | Closure \| slot | `null` | Content of a details row under each row, opened by a chevron. A closure receives the row, the columns and the rows. Also `expanded`. |
| `noRecords` | string \| slot | `No records found` | The message when there are no rows. |
| `footer` | slot | `null` | A `<tfoot>` row spanning every column. |
| `toggleable`, `draggable`, `resizable`, `pinnable` | bool | `false` | Lets the user hide, reorder, resize or pin columns. |
| `persist` | string | `null` | A key to keep the column choices in the browser. |
| `columnMinWidth`, `columnMaxMinWidth` | int | | Width limits for resizing, in pixels. |
| `idColumn` | bool | `false` | Keeps the `id` column when the columns come from the rows. |
| `mapRelationsColumn` | bool | `true` | Shows `user_id` as the `user` relation. |
| `border`, `dense`, `striped`, `hover`, `verticalLines`, `horizontalLines`, `sticky` | bool | | Looks: an outer border, less padding, striped rows, highlighted row on hover, lines between columns, lines between rows (on by default, `false` removes them), a header that sticks to the top. |

`row_<key>` and `col_<key>` attributes or slots render custom cells and headers.

### `tk:table.column` and `tk:table.cell`

For tables written by hand. `tk:table.column`: `name`, `label`, `sortable` (`true`, `asc`, `desc`), `sortableHref`, `sortableAction`, `align`, `sticky`, `draggable`, `resizable`, `pinnable`, `hidden`. `tk:table.cell`: `align`, `label`, `sticky`, `column`, `pinnable`, `hidden`. `tk:table.columns`, `tk:table.rows`, `tk:table.row` and `tk:table.footer` render `<thead>`, `<tbody>`, `<tr>` and `<tfoot>`.

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:table` | default | Table parts written by hand (`<thead>`, `<tbody>`, `<tfoot>`), in place of the generated ones. |
| `tk:table` | `row_<key>`, `col_<key>` | A cell or header of that column. |
| `tk:table` | `rowSelection`, `selectAll`, `rowExpanded`, `noRecords`, `footer` | As the props of the same name. |

## Events

Dispatched on the `<table>`; do not bubble.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:table` | `ready` | | The column features are set up. |
| `tk:table` | `column-toggled` | `{ name, visible }` | The user hid or showed a column. |

## Customization

Classes and attributes on the tag go to the `<table>`. To reach the parts, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:`, `area:` | `tk:table` | The outer box, and the horizontally scrollable area. |
| `columns:`, `column:`, `column-<key>:` | `tk:table` | The header row, every header, one column's header. |
| `rows:`, `row:` | `tk:table` | The `<tbody>`, every row. |
| `cell:`, `cell-<key>:` | `tk:table` | Every cell, one column's cells (`cell-total:class="text-end tabular-nums"`). |
| `select-all:`, `cell-selection:`, `cell-expanded:`, `row-expanded:` | `tk:table` | The selection and details parts. |
| `no-records:`, `footer:`, `pagination:` | `tk:table` | The empty row, the footer, the pagination. |
| `hidden-columns:`, `show-columns:` | `tk:table` | The bar and button for hidden columns. |

```blade
<tk:table :rows="$orders" :cols="$cols" striped container:class="shadow-sm" column-total:align="right" cell-total:class="tabular-nums" />
```

## Accessibility

A native `<table>` with `<th scope="col">` headers. Sortable headers are links and the sorted one has `aria-sort`. The selection checkboxes are named "Select row" and "Select all rows"; the details buttons have `aria-expanded` and `aria-controls`. Boolean cells have a visually hidden "Yes" or "No" next to the icon.

## Behavior notes

- A query is sorted only by a column listed in `cols` (or a default column) without a dot, so `?sortBy=` cannot order by an attribute the table does not show; relation columns (`customer.name`) are not sorted on the server.
- Selection is kept across pages in `selectedIds`, and cleared with `resetSelection()`.
- A cell whose value is a model shows its `name`, `title` or `text`; a collection is shown as a list.

## Related

- [Pagination](pagination.md): the links under the table.
- `tk:list`: a simple list without columns.
