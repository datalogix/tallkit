@aware(['paginator'])
@props([
    'paginator',
    'options',
    'name' => 'perPage',
    'size' => null,
])
@php

$selectOptions = in_livewire()
    ? collect($options)->all()
    : collect($options)->mapWithKeys(fn ($option) => [
        request()->fullUrlWithQuery([$name => $option, $paginator->getPageName() => 1]) => $option,
    ])->all();

$value = in_livewire() ? $paginator->perPage() : array_search($paginator->perPage(), $selectOptions);

@endphp
<tk:select
    :attributes="$attributes
        ->classes('w-auto shrink-0')
        ->when(
            in_livewire(),
            fn ($attrs) => $attrs->merge(['wire:model.live' => $name], false),
            fn ($attrs) => $attrs->merge(['x-on:change' => 'window.location.href = $el.value'], false),
        )
    "
    :options="$selectOptions"
    :$value
    :placeholder="false"
    :aria-label="__('Items per page')"
    :$size
/>
