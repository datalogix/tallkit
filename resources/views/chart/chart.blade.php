@props([
    'library' => null,
    'options' => null,
    'label' => null,
])
@php

$tag = $library === 'chartjs' ? 'canvas' : 'div';

@endphp
<tk:loadable
    :x-data="Str::camel($library)"
    :attributes="$attributes->prefixed('loadable:')"
>
    <{{ $tag }}
        {{
            $attributes->whereDoesntStartWith(['loadable:'])
                ->when($library === 'echarts', fn ($attrs) => $attrs->classes('[:where(&)]:w-full [:where(&)]:h-100'))
                ->merge(['role' => 'figure', 'aria-label' => __($label ?? 'Chart')])
        }}
        x-ref="target"
        @if ($options) x-init="render(serverOptions() ?? @js($options))" @endif
    ></{{ $tag }}>
</tk:loadable>
{{-- Outside wire:ignore, so each Livewire update brings them. --}}
<script type="application/json" {{ TALLKit::dataKey('options') }}>{!! Illuminate\Support\Js::encode($options) !!}</script>
