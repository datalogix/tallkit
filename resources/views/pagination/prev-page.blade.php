@aware(['paginator'])
@props([
    ...TALLKit::elementProps(),
    'paginator',
    'icon' => 'chevron-left',
    'tooltip' => 'Previous page',
])
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
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['tooltip' => null])"
    :aria-disabled="$disabled"
    :disabled="$disabled"
    :rel="in_livewire() ? false : 'prev'"
    :href="(in_livewire() || $disabled) ? false : $paginator->previousPageUrl()"
    :action="$action"
    :wire:key="$wireKey"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
>
    {{ $slot }}
</tk:button>
