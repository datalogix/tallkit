# ApexCharts

A chart drawn by [ApexCharts](https://apexcharts.com), configured with the library's own options. It loads the library on demand, follows the light and dark color scheme and redraws when Livewire sends new options.

## Usage

```blade
<tk:apexcharts :options="[
    'chart' => ['type' => 'bar', 'height' => 300],
    'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]],
    'xaxis' => ['categories' => ['Jan', 'Feb', 'Mar']],
]" />
```

A spinner shows while the library loads, then a bar chart with three bars takes its place. `options` is the ApexCharts configuration, as a PHP array.

### Updated by Livewire

```blade
<tk:apexcharts label="Sales per month" :options="$chart" />
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

After `addMonth()` the chart already on the page is updated with the new options and shows a fourth bar. Any option can change this way, including the series and the chart type.

### Options written in JavaScript

```blade
<tk:apexcharts
    :options="['chart' => ['type' => 'bar', 'height' => 300], 'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]]]"
    data-options="{ colors: ['#ef4444'], yaxis: { labels: { formatter: (value) => value + ' un' } } }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry, such as formatter functions. Here the bars are red and the axis reads `30 un`. It also works alone, with no `options` at all.

### Options from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="apexcharts" />
```

`tk:fetchable` requests the URL and draws the JSON it answers, which is the ApexCharts configuration. Calling its `update('/reports/sales?year=2025')` requests again and updates the same chart.

## Props

### `tk:apexcharts`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `null` | The ApexCharts configuration: `chart`, `series`, `xaxis`... |
| `label` | string | `Chart` | The accessible name of the chart, translated. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

`tk:apexcharts` is [`tk:chart`](chart.md) with `library="apexcharts"`: `label` and every other attribute are passed to it.

## Events

`rendered` is dispatched on the chart element, the one the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:apexcharts` | `rendered` | `{ chart }`, the ApexCharts instance | The chart was drawn: the first time, on each update of the options and when the color scheme changes. |
| `tk:apexcharts` | `completed` | | The library is loaded; the chart is drawn right after. |
| `tk:apexcharts` | `failed` | | The library could not be loaded, or ApexCharts threw while creating the chart. |

With Alpine:

```blade
<tk:apexcharts
    :options="['chart' => ['type' => 'bar'], 'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]]]"
    @rendered="chart = $event.detail.chart"
    loadable:x-on:completed="ready = true"
/>
```

Calling a Livewire action:

```blade
<tk:apexcharts
    :options="['chart' => ['type' => 'bar'], 'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]]]"
    @rendered="$wire.chartReady()"
/>
```

## Customization

Classes and attributes on the tag go to the chart element. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:apexcharts` | The wrapper around the chart, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:apexcharts` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:apexcharts` | The error message. |

```blade
<tk:apexcharts
    :options="['chart' => ['type' => 'bar'], 'series' => [['name' => 'Sales', 'data' => [30, 40, 45]]]]"
    class="rounded-lg border"
    loadable:class="p-4"
    loadable:loading:class="size-10"
/>
```

The size of the chart is set in the options (`chart.height`, `chart.width`).

## Accessibility

The chart element has `role="figure"` and an `aria-label`: the `label` prop, or "Chart" translated. Give each chart a `label` that says what it shows. The spinner and the error message are `role="status"` regions. Keyboard interaction inside the chart is the library's own.

## Behavior notes

- The library is loaded from `https://cdn.jsdelivr.net/npm/apexcharts@5` by the first chart on the page, once for all of them. When `window.ApexCharts` already exists, as when the app bundles the library, nothing is requested.
- Without `theme.mode` in the options the chart follows the `dark` class of `<html>`: it is drawn in the matching mode, with a transparent background, and drawn again when the class changes. Setting `theme.mode` fixes the mode and stops that.
- `data-options` is merged over `options` one level deep: a key present in both is taken whole from `data-options`. `data-options="{ chart: { type: 'area' } }"` discards the `chart.height` given in `options`.
- A Livewire update that changes `options` updates the same ApexCharts instance; a color scheme change destroys it and creates another. Take the instance from the latest `rendered` event, or call `getChart()`, which Alpine expressions written on the tag can use.
- `options` that start empty and are filled by a later Livewire update draw the chart at that moment.
- With neither `options` nor `data-options` nothing is drawn, and no error shows.
- When the library cannot be loaded, or ApexCharts throws while creating the chart (an `xaxis` that is not an array, for instance), the error message takes the place of the chart and `failed` fires. A later Livewire update with valid options does not draw it again: the page has to be reloaded.
- Options ApexCharts rejects only while drawing, such as a `series` that is not an array, leave the chart blank with the error in the browser console only; no message shows, `failed` does not fire and `rendered` still does.

## Related

- [Chart](chart.md): the same chart with the library chosen by the `library` prop, when it is decided at runtime.
- [Chart.js](chartjs.md), `tk:echarts`, `tk:frappe-charts`: the same tag for other chart libraries, each with its own options format.
- `tk:fetchable`: loads data from a URL and, with `chart="apexcharts"`, draws it.
