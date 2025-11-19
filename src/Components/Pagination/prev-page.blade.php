@aware(['paginator'])

@php
$disabled = $paginator->onFirstPage();

$wireKey = method_exists($paginator, 'getCursorName') && in_livewire()
    ? 'cursor-' . $paginator->getCursorName() . '-' . optional($paginator->previousCursor())->encode()
    : false;

$action = method_exists($paginator, 'getCursorName')
    ? "setPage('{$paginator->previousCursor()?->encode()}','{$paginator->getCursorName()}')"
    : "previousPage('{$paginator->getPageName()}')";
@endphp

<tk:button
    :$attributes
    :aria-disabled="$disabled"
    :aria-hidden="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'prev'"
    :href="in_livewire() ? false : $paginator->previousPageUrl()"
    :action="$action"
    :wire:key="$wireKey"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-left"
    tooltip="pagination.previous"
/>
