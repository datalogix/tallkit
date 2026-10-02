@aware(['paginator'])
@props([
    ...TALLKit::elementProps(),
    'paginator',
    'icon' => 'chevron-double-left',
    'tooltip' => 'First page',
])
@php

$disabled = $paginator->onFirstPage();

@endphp
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['tooltip' => null])"
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'first'"
    :href="(in_livewire() || $disabled) ? false : $paginator->url(1)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage(1, '{{ $paginator->getPageName() }}')"
>
    {{ $slot }}
</tk:button>
