@aware(['paginator'])
@props(['paginator', 'page' => 1])
@php

$disabled = $paginator->currentPage() === $page;

@endphp
<tk:button
    :$attributes
    :aria-current="$disabled ? 'page' : false"
    :tooltip="$disabled ? false : __('Go to page :page', ['page' => $page])"
    :disabled="$disabled"
    :href="(in_livewire() || $disabled) ? false : $paginator->url($page)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    :label="$page"
    action="setPage({{ $page }}, '{{ $paginator->getPageName() }}')"
>
    {{ $slot }}
</tk:button>
