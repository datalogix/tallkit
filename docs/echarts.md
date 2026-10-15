# ECharts

A chart drawn by [Apache ECharts](https://echarts.apache.org), configured with the library's own options. It loads the library on demand, follows the light and dark color scheme, resizes with its container and redraws when Livewire sends new options.

## Usage

```blade
<tk:echarts :options="[
    'xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar']],
    'yAxis' => ['type' => 'value'],
    'series' => [['type' => 'bar', 'data' => [30, 40, 45]]],
]" />
```

A spinner shows while the library loads, then a bar chart with three bars takes its place, as wide as its container and 25rem tall. `options` is the object ECharts takes in `chart.setOption(option)`, as a PHP array.

### Updated by Livewire

```blade
<tk:echarts label="Sales per month" :options="$chart" />
```

```php
public array $chart = [
    'xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar']],
    'yAxis' => ['type' => 'value'],
    'series' => [['type' => 'bar', 'data' => [30, 40, 45]]],
];

public function addMonth(): void
{
    $this->chart['xAxis']['data'][] = 'Apr';
    $this->chart['series'][0]['data'][] = 50;
}
```

After `addMonth()` the chart already on the page animates to the new options and shows a fourth bar.

### Rendering from data

```blade
<div class="grid grid-cols-2 gap-4">
    @foreach ($regions as $region)
        <tk:echarts
            :label="'Sales in '.$region->name"
            :options="[
                'xAxis' => ['type' => 'category', 'data' => $region->months],
                'yAxis' => ['type' => 'value'],
                'series' => [['type' => 'line', 'data' => $region->totals]],
            ]"
            class="h-64"
            wire:key="sales-{{ $region->id }}"
        />
    @endforeach
</div>
```

One line chart per region, side by side, each 16rem tall and with its own accessible name. Each chart fills the width of its grid cell and redraws when the cell changes size.

### Options written in JavaScript

```blade
<tk:echarts
    :options="['xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar']], 'yAxis' => ['type' => 'value'], 'series' => [['type' => 'bar', 'data' => [30, 40, 45]]]]"
    data-options="{ yAxis: { type: 'value', axisLabel: { formatter: (value) => value + ' un' } } }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry, such as callbacks. Here the labels of the vertical axis read `0 un`, `10 un`... It also works alone, with no `options` at all.

### Options from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="echarts" />
```

`tk:fetchable` requests the URL and draws the JSON it answers, which is the ECharts option object. Attributes prefixed with `chart:` go to the chart.

## Props

### `tk:echarts`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `null` | The ECharts option object: `xAxis`, `yAxis`, `series`, `tooltip`, `legend`... |
| `label` | string | `Chart` | The accessible name of the chart, translated. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

`tk:echarts` is [`tk:chart`](chart.md) with `library="echarts"`: `label` and every other attribute are passed to it.

## Events

`rendered` is dispatched on the chart element, the `<div>` the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:echarts` | `rendered` | `{ chart }`, the ECharts instance | The chart was drawn: the first time, on each update of the options and when the color scheme changes. |
| `tk:echarts` | `completed` | | The library is loaded; the chart is drawn right after. |
| `tk:echarts` | `failed` | | The library could not be loaded, or ECharts threw while drawing. |

With Alpine:

```blade
<tk:echarts
    :options="['series' => [['type' => 'pie', 'data' => [['name' => 'Yes', 'value' => 7], ['name' => 'No', 'value' => 3]]]]]"
    @rendered="chart = $event.detail.chart"
    loadable:x-on:completed="ready = true"
/>
```

Calling a Livewire action:

```blade
<tk:echarts
    :options="['series' => [['type' => 'pie', 'data' => [['name' => 'Yes', 'value' => 7], ['name' => 'No', 'value' => 3]]]]]"
    @rendered="$wire.chartReady()"
/>
```

## Customization

Classes and attributes on the tag go to the chart `<div>`. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:echarts` | The wrapper around the chart, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:echarts` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:echarts` | The error message. |

```blade
<tk:echarts
    :options="['xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb']], 'yAxis' => ['type' => 'value'], 'series' => [['type' => 'line', 'data' => [3, 5]]]]"
    class="h-80 rounded-lg border"
    loadable:class="p-4"
    loadable:loading:class="size-10"
/>
```

ECharts draws into the size of its `<div>`, which is `w-full h-100` by default. A width or height class on the tag replaces the default one.

## Accessibility

The chart `<div>` has `role="figure"` and an `aria-label`: the `label` prop, or "Chart" translated. Give each chart a `label` that says what it shows. The spinner and the error message are `role="status"` regions. Keyboard interaction inside the chart is the library's own.

## Behavior notes

- The library is loaded from `https://cdn.jsdelivr.net/npm/echarts@6` by the first chart on the page, once for all of them. When `window.echarts` already exists, as when the app bundles the library, nothing is requested.
- `data-options` is merged over `options` at the top level: a key present in both is taken whole from `data-options`.
- A Livewire update that changes `options` is passed to `setOption()` of the same instance, which merges it into the current chart. When the new options have `series`, the series are replaced instead of merged, so a series removed on the server disappears from the chart.
- The color scheme follows the `dark` class of `<html>`. In dark mode the chart uses ECharts's `dark` theme with a transparent background, so the page shows through. When the class changes, the instance is disposed and created again with the other theme.
- A color scheme change creates a new instance. Take it from the latest `rendered` event, or call `getChart()`, which Alpine expressions written on the tag can use.
- The chart redraws at the new size when its `<div>` is resized, by the window or by its container.
- `options` that start empty and are filled by a later Livewire update draw the chart at that moment.
- With neither `options` nor `data-options` nothing is drawn, and no error shows.
- When the library cannot be loaded, or ECharts throws while drawing, the error message takes the place of the chart and `failed` fires.
- Options ECharts accepts without throwing but cannot draw, such as an unknown series `type`, leave the chart blank, with no message and no `failed`.

## Related

- [Chart](chart.md): the same chart with the library chosen by the `library` prop, when it is decided at runtime.
- [Chart.js](chartjs.md), [ApexCharts](apexcharts.md): the same tag drawn by other libraries, each with its own options format.
- `tk:frappe-charts`: the same tag for Frappe Charts.
- `tk:fetchable`: loads data from a URL and, with `chart="echarts"`, draws it.
