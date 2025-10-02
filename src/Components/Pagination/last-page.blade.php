@aware(['paginator'])

@php
$wireClick = "setPage(".$paginator->lastPage().", '{$paginator->getPageName()}')";
$disabled = $paginator->onLastPage();
@endphp

<tk:button
    :$attributes
    :aria-disabled="$disabled"
    :aria-hidden="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'last'"
    :href="in_livewire() ? false : $paginator->url($paginator->lastPage())"
    :wire:click="in_livewire() ? $wireClick : false"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-double-right"
    tooltip="pagination.last"
/>
