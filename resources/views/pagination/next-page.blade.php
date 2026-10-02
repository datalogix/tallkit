@aware(['paginator'])
@props([
    ...TALLKit::elementProps(),
    'paginator',
    'icon' => 'chevron-right',
    'tooltip' => 'Next page',
])
@php

$disabled = ! $paginator->hasMorePages();

$wireKey = method_exists($paginator, 'getCursorName') && in_livewire()
    ? 'cursor-' . $paginator->getCursorName() . '-' . optional($paginator->nextCursor())->encode()
    : false;

$action = method_exists($paginator, 'getCursorName')
    ? "setPage('{$paginator->nextCursor()?->encode()}','{$paginator->getCursorName()}')"
    : "nextPage('{$paginator->getPageName()}')";

@endphp
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['tooltip' => null])"
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'next'"
    :href="(in_livewire() || $disabled) ? false : $paginator->nextPageUrl()"
    :action="$action"
    :wire:key="$wireKey"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
>
    {{ $slot }}
</tk:button>
