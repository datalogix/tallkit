@aware(['paginator'])

<tk:button
    :$attributes
    :aria-disabled="$paginator->onLastPage()"
    :aria-hidden="$paginator->onLastPage()"
    :disabled="$paginator->onLastPage()"
    :rel="in_livewire() ? false : 'last'"
    :href="in_livewire() ? false : $paginator->url($paginator->lastPage())"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    action="setPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
    icon="chevron-double-right"
    tooltip="pagination.last"
/>
