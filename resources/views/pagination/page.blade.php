@aware(['paginator'])
@props(['paginator', 'page' => 1])
@php

$isCurrent = $paginator->currentPage() === $page;

@endphp
<tk:button
    :$attributes
    :aria-current="$isCurrent ? 'page' : false"
    :data-current="$isCurrent ? true : false"
    :tooltip="$isCurrent ? false : __('Go to page :page', ['page' => $page])"
    :disabled="$isCurrent"
    :href="(in_livewire() || $isCurrent) ? false : $paginator->url($page)"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    :label="$page"
    action="setPage({{ $page }}, '{{ $paginator->getPageName() }}')"
>
    {{ $slot }}
</tk:button>
