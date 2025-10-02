@aware(['paginator'])

@php
$wireKey = method_exists($paginator, 'getCursorName')
    ? 'cursor-' . $paginator->getCursorName() . '-' . optional($paginator->nextCursor())->encode()
    : false;

$wireClick = method_exists($paginator, 'getCursorName')
    ? "setPage('{$paginator->nextCursor()?->encode()}','{$paginator->getCursorName()}')"
    : "nextPage('{$paginator->getPageName()}')";

$disabled = !$paginator->hasMorePages();
@endphp

<tk:button
    :attributes="$attributes->merge(['wire:key' => $wireKey])"
    :aria-disabled="$disabled"
    :aria-hidden="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'next'"
    :href="in_livewire() ? false : $paginator->nextPageUrl()"
    :wire:click="in_livewire() ? $wireClick : false"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-right"
    tooltip="pagination.next"
/>
