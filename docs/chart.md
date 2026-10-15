# Chart

A chart drawn by one of four JavaScript libraries, chosen with `library` and configured with that library's own options. It loads the library on demand and redraws when Livewire sends new options. `tk:apexcharts`, `tk:chartjs`, `tk:echarts` and `tk:frappe-charts` are this same tag with `library` already set; use `tk:chart` when the library is decided at runtime.

## Usage

```blade
<tk:chart library="chartjs" :options="[
    'type' => 'bar',
    'data' => [
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [['label' => 'Sales', 'data' => [30, 40, 45]]],
    ],
]" />
```

A spinner shows while Chart.js loads, then a bar chart with three bars takes its place. `options` is the configuration of the chosen library, as a PHP array: here the one Chart.js takes in `new Chart(canvas, config)`.

### Choosing the library

```blade
<tk:chart :library="$library" :options="$options" label="Sales per month" />
```

```php
public string $library = 'echarts';

public array $options = [
    'xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar']],
    'yAxis' => ['type' => 'value'],
    'series' => [['type' => 'bar', 'data' => [30, 40, 45]]],
];
```

`library` takes `apexcharts`, `chartjs`, `echarts` or `frappe-charts`, and `options` must be in that library's format: the same array does not draw the same chart in another library. `<tk:echarts :options="$options" />` renders exactly the same as this example.

### Updated by Livewire

```blade
<tk:chart library="apexcharts" label="Sales per month" :options="$chart" />
```

```php
public array $chart = [
    'chart' => ['type' => 'bar', 'height' => 300],
    'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]],
    'xaxis' => ['categories' => ['Jan', 'Feb', 'Mar']],
];

public function addMonth(): void
{
    $this->chart['series'][0]['data'][] = 50;
    $this->chart['xaxis']['categories'][] = 'Apr';
}
```

After `addMonth()` the chart already on the page is updated with the new options and shows a fourth bar. The same holds for every library.

### Options written in JavaScript

```blade
<tk:chart
    library="chartjs"
    :options="['type' => 'bar', 'data' => ['labels' => ['Jan', 'Feb', 'Mar'], 'datasets' => [['label' => 'Sales', 'data' => [30, 40, 45]]]]]"
    data-options="{ options: { scales: { y: { ticks: { callback: (value) => value + ' un' } } } } }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry, such as formatter functions. Here the vertical axis reads `30 un`. It also works alone, with no `options` at all.

### Options from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="chartjs" />
```

`tk:fetchable` requests the URL and draws the JSON it answers with `tk:chart`, so the JSON is the configuration of the library named in `chart`. Attributes prefixed with `chart:` go to the chart.

## Props

### `tk:chart`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `library` | string | `null` | `apexcharts`, `chartjs`, `echarts` or `frappe-charts`. Required: without it, or with another value, nothing is drawn. |
| `options` | array | `null` | The configuration of the chosen library. |
| `label` | string | `Chart` | The accessible name of the chart, translated. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

`tk:apexcharts`, `tk:chartjs`, `tk:echarts` and `tk:frappe-charts` take `options` and pass everything else, `label` and the prefixed attributes included, to `tk:chart`.

## Events

`rendered` is dispatched on the chart element, the one the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:chart` | `rendered` | `{ chart }`, the library's chart instance | The chart was drawn: the first time and on each update of the options. ApexCharts, Chart.js and ECharts also fire it when the color scheme changes. |
| `tk:chart` | `completed` | | The library is loaded; the chart is drawn right after. |
| `tk:chart` | `failed` | | The library could not be loaded, or the library threw while drawing. |

With Alpine:

```blade
<tk:chart
    library="chartjs"
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['data' => [7, 3]]]]]"
    @rendered="chart = $event.detail.chart"
    loadable:x-on:completed="ready = true"
/>
```

Calling a Livewire action:

```blade
<tk:chart
    library="chartjs"
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['data' => [7, 3]]]]]"
    @rendered="$wire.chartReady()"
/>
```

## Customization

Classes and attributes on the tag go to the chart element: a `<canvas>` for Chart.js, a `<div>` for the others. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:chart` | The wrapper around the chart, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:chart` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:chart` | The error message. |

```blade
<tk:chart
    library="echarts"
    :options="['xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb']], 'yAxis' => ['type' => 'value'], 'series' => [['type' => 'line', 'data' => [3, 5]]]]"
    class="h-64 rounded-lg border"
    loadable:class="p-4"
    loadable:loading:class="size-10"
/>
```

The size comes from each library: ApexCharts and Frappe Charts take it from the options (`chart.height`, `height`), Chart.js sizes the canvas to its container, and the ECharts element defaults to full width and `h-100`, which a height class on the tag replaces, as in this example.

## Accessibility

The chart element has `role="figure"` and an `aria-label`: the `label` prop, or "Chart" translated. Give each chart a `label` that says what it shows. The spinner and the error message are `role="status"` regions. Keyboard interaction inside the chart is the library's own.

## Behavior notes

- Each library is loaded from jsDelivr by the first chart that uses it, once for all of them: `apexcharts@5`, `chart.js@4`, `echarts@6` and `frappe-charts@1`. When the library is already on `window` (`ApexCharts`, `Chart`, `echarts`, `frappe.Chart`), as when the app bundles it, nothing is requested.
- `data-options` is merged over `options` at the top level: a key present in both is taken whole from `data-options`.
- Updates from Livewire are applied differently by each library. ApexCharts merges the new options into the same instance. Chart.js replaces `type`, `data` and `options` of the same instance, and creates a new one when the new options carry `plugins`. ECharts merges them with its own `setOption`, replacing the whole `series` when `series` is present. Frappe Charts draws a new chart from the new options alone.
- The color scheme follows the `dark` class of `<html>` and is applied again when the class changes. ApexCharts draws in the matching mode with a transparent background, unless the options set `theme.mode`. ECharts uses its dark theme with a transparent background. Chart.js switches the global text and grid colors, only while they still hold its own defaults, so a color the app set on `Chart.defaults` is kept. Frappe Charts does not follow the color scheme.
- ECharts and Frappe Charts redraw when the chart element changes size.
- A color scheme change and some updates create a new chart instance. Take it from the latest `rendered` event, or call `getChart()`, which Alpine expressions written on the tag can use.
- `options` that start empty and are filled by a later Livewire update draw the chart at that moment.
- With neither `options` nor `data-options` nothing is drawn, and no error shows.
- If the library cannot be loaded, the error message takes the place of the chart. Options a library accepts without throwing but cannot draw leave the chart blank, with no message.

## Related

- [ApexCharts](apexcharts.md): `tk:chart` with `library="apexcharts"`, with ApexCharts examples.
- [Chart.js](chartjs.md), `tk:echarts`, `tk:frappe-charts`: `tk:chart` with the library fixed, when it is known in advance.
- `tk:fetchable`: loads data from a URL and, with `chart`, draws it.
