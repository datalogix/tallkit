@aware(['paginator'])

<tk:button
    :$attributes
    :aria-current="$paginator->currentPage() === $page ? 'page' : false"
    :tooltip="$paginator->currentPage() === $page ? false : __('Go to page :page', ['page' => $page])"
    :disabled="$paginator->currentPage() === $page"
    :href="in_livewire() ? false : $paginator->url($page)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    :label="$page"
    action="setPage({{ $page }}, '{{ $paginator->getPageName() }}')"
/>
