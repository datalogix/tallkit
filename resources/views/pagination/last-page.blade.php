@aware(['paginator'])
@props(['paginator'])
@php

$disabled = $paginator->onLastPage();

@endphp
<tk:button
    :$attributes
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'last'"
    :href="(in_livewire() || $disabled) ? false : $paginator->url($paginator->lastPage())"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
    icon="chevron-double-right"
    tooltip="pagination.last"
>
    {{ $slot }}
</tk:button>
