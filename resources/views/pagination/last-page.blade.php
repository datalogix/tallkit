@aware(['paginator'])
@props([
    ...TALLKit::elementProps(),
    'paginator',
    'icon' => 'chevron-double-right',
    'tooltip' => 'Last page',
])
@php

$disabled = $paginator->onLastPage();

@endphp
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['tooltip' => null])"
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'last'"
    :href="(in_livewire() || $disabled) ? false : $paginator->url($paginator->lastPage())"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
>
    {{ $slot }}
</tk:button>
