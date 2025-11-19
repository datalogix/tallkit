@aware(['paginator'])

<tk:button
    :$attributes
    :aria-disabled="$paginator->onFirstPage()"
    :aria-hidden="$paginator->onFirstPage()"
    :disabled="$paginator->onFirstPage()"
    :rel="in_livewire() ? false : 'first'"
    :href="in_livewire() ? false : $paginator->url(1)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage(1, '{{ $paginator->getPageName() }}')"
    icon="chevron-double-left"
    tooltip="pagination.first"
/>
