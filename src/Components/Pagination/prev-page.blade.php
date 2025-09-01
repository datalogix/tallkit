@aware(['paginator'])

@php
$wireKey = method_exists($paginator, 'getCursorName')
    ? 'cursor-' . $paginator->getCursorName() . '-' . optional($paginator->previousCursor())->encode()
    : false;

$wireClick = method_exists($paginator, 'getCursorName')
    ? "setPage('{$paginator->previousCursor()?->encode()}','{$paginator->getCursorName()}')"
    : "previousPage('{$paginator->getPageName()}')";

$disabled = $paginator->onFirstPage();
@endphp

<tk:button
    :attributes="$attributes->merge(['wire:key' => $wireKey])"
    :aria-disabled="$disabled"
    :aria-hidden="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'prev'"
    :href="in_livewire() ? false : $paginator->previousPageUrl()"
    :wire:click="in_livewire() ? $wireClick : false"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    icon="chevron-left"
    aria-label="pagination.previous"
    tooltip="pagination.previous"
/>
