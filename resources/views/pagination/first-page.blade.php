@aware(['paginator'])
@props(['paginator'])
@php

$disabled = $paginator->onFirstPage();

@endphp
<tk:button
    :$attributes
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'first'"
    :href="(in_livewire() || $disabled) ? false : $paginator->url(1)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage(1, '{{ $paginator->getPageName() }}')"
    icon="chevron-double-left"
    tooltip="pagination.first"
>
    {{ $slot }}
</tk:button>
