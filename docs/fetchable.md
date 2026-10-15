# Fetchable

Requests a URL from the browser and shows what it answers: as formatted JSON, as a chart, or through markup of your own. A spinner shows while the request runs and an error message if it fails. Use it for data that should not delay the page, such as a report or a preview loaded after the first render.

## Usage

```blade
<tk:fetchable url="/api/status" />
```

A spinner first; when the request answers, the JSON response is shown formatted and colored, as `tk:pretty-print-json` shows it. A response with an error status, or a network failure, shows a red message with the reason (`Not Found`, for instance) in its place.

### Your own markup

```blade
<tk:fetchable url="/api/orders/summary">
    <div>
        <p>Open orders: <strong x-text="data.open"></strong></p>
        <p>Shipped today: <strong x-text="data.shipped"></strong></p>
    </div>
</tk:fetchable>
```

The slot is shown once the response arrives, with `data` holding the parsed JSON for Alpine expressions inside it. It is not rendered before that, so `data` is always there when the markup reads it. Wrap the slot in one element: only its first root element is rendered.

### A chart from an endpoint

```blade
<tk:fetchable url="/reports/sales" chart="chartjs" chart:label="Sales per month" />
```

The response is the chart's options in the library's own format, drawn by [`tk:chart`](chart.md) with that `library`: `apexcharts`, `chartjs`, `echarts` or `frappe-charts`. Attributes prefixed with `chart:` go to the chart.

### Loading on demand

```blade
<tk:fetchable id="report" url="/reports/heavy" :auto="false" />

<tk:button x-data x-on:click="Alpine.$data(document.getElementById('report')).fetch()" label="Load report" />
```

With `:auto="false"` nothing is requested, and "Nothing to show" is displayed, until `fetch()` is called. Inside the tag, `fetch()`, `reload()` and `update()` can be called directly from Alpine expressions, as in the next example.

### Reloading

```blade
<tk:fetchable url="/api/status">
    <div>
        <pre x-text="JSON.stringify(data)"></pre>

        <tk:button x-on:click="update()" label="Refresh" />
        <tk:button x-on:click="fetch('/api/status?verbose=1')" label="Verbose" />
    </div>
</tk:fetchable>
```

`update()` requests the URL again and swaps the content when the answer arrives, keeping the current one on screen meanwhile. `reload()` does the same but shows the spinner while waiting. `fetch(url, options)` requests another URL, which becomes the one the next calls use.

## Props

### `tk:fetchable`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `url` | string | `null` | The URL to request. |
| `auto` | bool | `true` | Requests the URL as soon as the page loads. `false` waits for `fetch()`. |
| `options` | array | `[]` | Options for `fetch()`: `method`, `headers`, `body`... plus `responseType`, the method used to read the response (`json` by default, `text`, `blob`...; empty for the raw `Response`). |
| `data` | mixed | `null` | Data to show without a request, when there is no `url`. |
| `chart` | string | `null` | Draws the response as a chart with this library: `apexcharts`, `chartjs`, `echarts` or `frappe-charts`. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:fetchable` | default | Shown when the response arrives, in place of the JSON or the chart. `data` holds the response. Only its first root element is rendered. |

## Events

Events are dispatched on the `tk:fetchable` element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:fetchable` | `started` | | A request started, except with `update()`. The automatic first request starts before listeners on the tag are attached, so it is not heard there. |
| `tk:fetchable` | `completed` | | The response arrived and was read. |
| `tk:fetchable` | `failed` | | The request failed, or answered with an error status. |

With Alpine:

```blade
<tk:fetchable url="/api/status" x-on:completed="lastCheck = new Date()" x-on:failed="offline = true" />
```

Calling a Livewire action:

```blade
<tk:fetchable url="/api/status" x-on:failed="$wire.reportOutage()" />
```

## Customization

Classes and attributes on the tag go to its wrapper `<div>`. To reach the parts inside, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `loading:` | `tk:fetchable` | The spinner. |
| `error:` | `tk:fetchable` | The error message. |
| `json:` | `tk:fetchable` | The formatted JSON. |
| `chart:` | `tk:fetchable` | The chart, with `chart`. |

```blade
<tk:fetchable url="/api/status" class="rounded-lg border p-4" json:class="text-xs" loading:class="size-8" />
```

## Accessibility

The spinner and the error message are `role="status"` live regions, so the start and the failure of the request are announced. What the slot renders is your own markup.

## Behavior notes

- Requests other than `GET`, `HEAD` and `OPTIONS` to the same origin carry the CSRF token (`X-XSRF-TOKEN` from the cookie, or `X-CSRF-TOKEN` from the `csrf-token` meta tag), so a `POST` to a Laravel route works without extra setup.
- A new request cancels the one still running, so only the last answer is shown.
- `headers` given to `fetch()` are merged with the ones from `options`; the other options replace them for this and the next requests.
- The wrapper has `wire:ignore`: a Livewire update does not touch what was loaded.

## Related

- [Chart](chart.md): draws options already known on the server, without a request.
- `tk:pretty-print-json`: formats JSON that is already on the page.
- `tk:loadable`: the loading, error and empty states this tag is built on, for content loaded in other ways.
