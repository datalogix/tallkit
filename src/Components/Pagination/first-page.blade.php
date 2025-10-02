@aware(['paginator'])

@php
$wireClick = "setPage(1, '{$paginator->getPageName()}')";
$disabled = $paginator->onFirstPage();
@endphp

<tk:button
    :$attributes
    :aria-disabled="$disabled"
    :aria-hidden="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'first'"
    :href="in_livewire() ? false : $paginator->url(1)"
    :wire:click="in_livewire() ? $wireClick : false"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-double-left"
    tooltip="pagination.first"
/>
