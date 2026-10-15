# Chart.js

A chart drawn by [Chart.js](https://www.chartjs.org) on a `<canvas>`, configured with the library's own options. It loads the library on demand, follows the light and dark color scheme and redraws when Livewire sends new options.

## Usage

```blade
<tk:chartjs :options="[
    'type' => 'bar',
    'data' => [
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [['label' => 'Sales', 'data' => [30, 40, 45]]],
    ],
]" />
```

A spinner shows while the library loads, then a bar chart with three bars takes its place. `options` is the configuration Chart.js takes in `new Chart(canvas, config)`, as a PHP array: `type`, `data` and `options`.

### Updated by Livewire

```blade
<tk:chartjs label="Sales per month" :options="$chart" />
```

```php
public array $chart = [
    'type' => 'bar',
    'data' => [
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [['label' => 'Sales', 'data' => [30, 40, 45]]],
    ],
];

public function addMonth(): void
{
    $this->chart['data']['labels'][] = 'Apr';
    $this->chart['data']['datasets'][0]['data'][] = 50;
}
```

After `addMonth()` the chart already on the page is updated with the new options and shows a fourth bar.

### Rendering from data

```blade
<div class="grid grid-cols-2 gap-4">
    @foreach ($regions as $region)
        <tk:chartjs
            :label="'Sales in '.$region->name"
            :options="[
                'type' => 'line',
                'data' => [
                    'labels' => $region->months,
                    'datasets' => [['label' => $region->name, 'data' => $region->totals]],
                ],
            ]"
            wire:key="sales-{{ $region->id }}"
        />
    @endforeach
</div>
```

One line chart per region, side by side, each with its own accessible name. Chart.js sizes each canvas to the width of its grid cell.

### Options written in JavaScript

```blade
<tk:chartjs
    :options="['type' => 'bar', 'data' => ['labels' => ['Jan', 'Feb', 'Mar'], 'datasets' => [['label' => 'Sales', 'data' => [30, 40, 45]]]]]"
    data-options="{ options: { scales: { y: { ticks: { callback: (value) => value + ' un' } } } } }"
/>
```

`data-options` is a JavaScript object merged over `options`, for what a PHP array cannot carry, such as callbacks. Here the labels of the vertical axis read `0 un`, `5 un`... It also works alone, with no `options` at all.

### Options from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="chartjs" />
```

`tk:fetchable` requests the URL and draws the JSON it answers, which is the Chart.js configuration. Attributes prefixed with `chart:` go to the chart.

## Props

### `tk:chartjs`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `null` | The Chart.js configuration: `type`, `data`, `options`, `plugins`. |
| `label` | string | `Chart` | The accessible name of the chart, translated. |

`data-options` is an attribute, not a prop: a JavaScript expression that returns an object of options.

`tk:chartjs` is [`tk:chart`](chart.md) with `library="chartjs"`: `label` and every other attribute are passed to it.

## Events

`rendered` is dispatched on the `<canvas>`, the element the tag's attributes go to, and does not bubble: listen on the tag itself. The others are dispatched on the wrapper around it and do not bubble either, so they are listened to with the `loadable:` prefix.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:chartjs` | `rendered` | `{ chart }`, the Chart.js instance | The chart was drawn: the first time, on each update of the options and when the color scheme changes. |
| `tk:chartjs` | `completed` | | The library is loaded; the chart is drawn right after. |
| `tk:chartjs` | `failed` | | The library could not be loaded, or Chart.js threw while drawing. |

With Alpine:

```blade
<tk:chartjs
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['data' => [7, 3]]]]]"
    @rendered="chart = $event.detail.chart"
    loadable:x-on:completed="ready = true"
/>
```

Calling a Livewire action:

```blade
<tk:chartjs
    :options="['type' => 'pie', 'data' => ['labels' => ['Yes', 'No'], 'datasets' => [['data' => [7, 3]]]]]"
    @rendered="$wire.chartReady()"
/>
```

## Customization

Classes and attributes on the tag go to the `<canvas>`. To reach the elements around it, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loadable:` | `tk:chartjs` | The wrapper around the chart, the spinner and the error. `loadable:silent` hides the spinner. |
| `loadable:loading:` | `tk:chartjs` | The spinner shown while the library loads. |
| `loadable:error:` | `tk:chartjs` | The error message. |

```blade
<tk:chartjs
    :options="['type' => 'line', 'data' => ['labels' => ['Jan', 'Feb'], 'datasets' => [['label' => 'Sales', 'data' => [3, 5]]]]]"
    class="rounded-lg border"
    loadable:class="p-4"
    loadable:loading:class="size-10"
/>
```

Chart.js sizes the canvas to the width of its container and takes the height from the chart's aspect ratio (`options.aspectRatio`), so the width of the container is what sets the size.

## Accessibility

The `<canvas>` has `role="figure"` and an `aria-label`: the `label` prop, or "Chart" translated. Give each chart a `label` that says what it shows. The spinner and the error message are `role="status"` regions. Keyboard interaction inside the chart is the library's own.

## Behavior notes

- The library is loaded from `https://cdn.jsdelivr.net/npm/chart.js@4` by the first chart on the page, once for all of them. When `window.Chart` already exists, as when the app bundles the library, nothing is requested.
- `data-options` is merged over `options` at the top level: a key present in both is taken whole from `data-options`. `data-options="{ options: { animation: false } }"` discards every key the PHP `options.options` had.
- A Livewire update that changes `options` replaces `type`, `data` and `options` of the same Chart.js instance and calls `update()`. When the new options carry `plugins`, the instance is destroyed and a new one is created instead.
- The color scheme follows the `dark` class of `<html>`. When the class changes, the global text and grid colors (`Chart.defaults.color`, `Chart.defaults.scale.grid.color`) switch to light or dark, and the chart is destroyed and created again with them. They switch only while they still hold Chart.js's own defaults, so a color the app set on `Chart.defaults` is kept.
- A color scheme change and an update with `plugins` create a new instance. Take it from the latest `rendered` event, or call `getChart()`, which Alpine expressions written on the tag can use.
- `options` that start empty and are filled by a later Livewire update draw the chart at that moment.
- With neither `options` nor `data-options` nothing is drawn, and no error shows.
- When the library cannot be loaded, or Chart.js throws while drawing (a `datasets` that is not a list, for instance), the error message takes the place of the chart and `failed` fires. A later Livewire update with valid options does not draw it again: the page has to be reloaded.
- Options Chart.js accepts without throwing but cannot draw, such as an unknown `type`, leave the chart blank, with no message and no `failed`.

## Related

- [Chart](chart.md): the same chart with the library chosen by the `library` prop, when it is decided at runtime.
- [ApexCharts](apexcharts.md): the same tag drawn by ApexCharts, with its own options format.
- `tk:echarts`, `tk:frappe-charts`: the same tag for ECharts and Frappe Charts.
- `tk:fetchable`: loads data from a URL and, with `chart="chartjs"`, draws it.
