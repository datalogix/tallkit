@aware(['paginator'])

@php
$wireKey = method_exists($paginator, 'getCursorName') && in_livewire()
    ? 'cursor-' . $paginator->getCursorName() . '-' . optional($paginator->nextCursor())->encode()
    : false;

$action = method_exists($paginator, 'getCursorName')
    ? "setPage('{$paginator->nextCursor()?->encode()}','{$paginator->getCursorName()}')"
    : "nextPage('{$paginator->getPageName()}')";
@endphp

<tk:button
    :$attributes
    :aria-disabled="!$paginator->hasMorePages()"
    :aria-hidden="!$paginator->hasMorePages()"
    :disabled="!$paginator->hasMorePages()"
    :rel="in_livewire() ? false : 'next'"
    :href="in_livewire() ? false : $paginator->nextPageUrl()"
    :action="$action"
    :wire:key="$wireKey"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-right"
    tooltip="pagination.next"
/>
