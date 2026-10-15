# Frappe Charts

A chart drawn by [Frappe Charts](https://frappe.io/charts), a small SVG chart library, configured with its own options. It loads the library on demand, redraws when its container is resized and when Livewire sends new options.

## Usage

```blade
<tk:frappe-charts :options="[
    'type' => 'bar',
    'data' => [
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [['name' => 'Sales', 'values' => [30, 40, 45]]],
    ],
]" />
```

A spinner shows while the library loads, then a bar chart with three bars takes its place, as wide as its container. `options` is the object Frappe Charts takes in `new frappe.Chart(element, options)`, as a PHP array: `type`, `data`, `height`, `colors`...

### Updated by Livewire

```blade
<tk:frappe-charts label="Sales per month" :options="$chart" />
```

```php
public array $chart = [
    'type' => 'line',
    'height' => 250,
    'data' => [
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [['name' => 'Sales', 'values' => [30, 40, 45]]],
    ],
];

public function addMonth(): void
{
    $this->chart['data']['labels'][] = 'Apr';
    $this->chart['data']['datasets'][0]['values'][] = 50;
}
```

After `addMonth()` the chart on the page is drawn again with the new options and shows a fourth point.

### Rendering from data

```blade
<div class="grid grid-cols-2 gap-4">
    @foreach ($regions as $region)
        <tk:frappe-charts
            :label="'Sales in '.$region->name"
            :options="[
                'type' => 'line',
                'data' => [
                    'labels' => $region->months,
                    'datasets' => [['name' => $region->name, 'values' => $region->totals]],
                ],
            ]"
            wire:key="sales-{{ $region->id }}"
        />
    @endforeach
</div>
```

One line chart per region, side by side, each with its own accessible name, each as wide as its grid cell.

### Options written in JavaScript

```blade
<tk:frappe-charts
    :options="['type' => 'bar', 'data' => ['labels' => ['Jan', 'Feb', 'Mar'], 'datasets' => [['name' => 'Sales', 'values' => [30, 40, 45]]]]]"
    data-options="{ tooltipOptions: { formatTooltipY: (value) => value + ' un' } }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry, such as callbacks. Here the tooltip reads `30 un`. It also works alone, with no `options` at all.

### Options from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="frappe-charts" />
```

`tk:fetchable` requests the URL and draws the JSON it answers, which is the Frappe Charts options. Attributes prefixed with `chart:` go to the chart.

## Props

### `tk:frappe-charts`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `null` | The Frappe Charts options: `type`, `data`, `height`, `colors`, `axisOptions`... |
| `label` | string | `Chart` | The accessible name of the chart, translated. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

`tk:frappe-charts` is [`tk:chart`](chart.md) with `library="frappe-charts"`: `label` and every other attribute are passed to it.

## Events

`rendered` is dispatched on the chart element, the `<div>` the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:frappe-charts` | `rendered` | `{ chart }`, the Frappe Charts instance | The chart was drawn: the first time and on each update of the options. |
| `tk:frappe-charts` | `completed` | | The library is loaded; the chart is drawn right after. |
| `tk:frappe-charts` | `failed` | | The library could not be loaded, or Frappe Charts threw while drawing. |

With Alpine:

```blade
<tk:frappe-charts
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['values' => [7, 3]]]]]"
    @rendered="chart = $event.detail.chart"
    loadable:x-on:completed="ready = true"
/>
```

Calling a Livewire action:

```blade
<tk:frappe-charts
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['values' => [7, 3]]]]]"
    @rendered="$wire.chartReady()"
/>
```

## Customization

Classes and attributes on the tag go to the chart `<div>`. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:frappe-charts` | The wrapper around the chart, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:frappe-charts` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:frappe-charts` | The error message. |

```blade
<tk:frappe-charts
    :options="['type' => 'line', 'height' => 200, 'data' => ['labels' => ['Jan', 'Feb'], 'datasets' => [['values' => [3, 5]]]]]"
    class="rounded-lg border"
    loadable:class="p-4"
/>
```

The chart takes the width of its `<div>`; its height comes from `options.height`, not from CSS.

## Accessibility

The chart `<div>` has `role="figure"` and an `aria-label`: the `label` prop, or "Chart" translated. Give each chart a `label` that says what it shows. The spinner and the error message are `role="status"` regions.

## Behavior notes

- The library is loaded from `https://cdn.jsdelivr.net/npm/frappe-charts@1` by the first chart on the page, once for all of them. When `window.frappe.Chart` already exists, as when the app bundles the library, nothing is requested.
- `data-options` is merged over `options` at the top level: a key present in both is taken whole from `data-options`.
- A Livewire update that changes `options` destroys the chart and draws a new one with them, so a new instance comes with each `rendered` event; `getChart()`, callable from Alpine expressions on the tag, returns the current one.
- The chart redraws at the new width when its `<div>` is resized, by the window or by its container.
- Frappe Charts has no dark theme: the colors stay the same in dark mode. Set `colors` and style the axis text with CSS when the page has a dark mode.
- `options` that start empty and are filled by a later Livewire update draw the chart at that moment.
- With neither `options` nor `data-options` nothing is drawn, and no error shows.
- When the library cannot be loaded, or Frappe Charts throws while drawing, the error message takes the place of the chart and `failed` fires.

## Related

- [Chart](chart.md): the same chart with the library chosen by the `library` prop, when it is decided at runtime.
- [Chart.js](chartjs.md), [ApexCharts](apexcharts.md), [ECharts](echarts.md): the same tag drawn by other libraries, each with its own options format.
- [Fetchable](fetchable.md): loads data from a URL and, with `chart="frappe-charts"`, draws it.
