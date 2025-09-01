@aware(['paginator'])

@php
$wireClick = "setPage({$page}, '{$paginator->getPageName()}')";
$current = $paginator->currentPage() === $page;
@endphp

<tk:button
    :attributes="$attributes->merge($current
        ? ['aria-current' => 'page']
        : [
            'tooltip' => __('Go to page :page', ['page' => $page]),
            'aria-label' => __('Go to page :page', ['page' => $page]),
        ]
    )"
    :disabled="$current"
    :href="in_livewire() ? false : $paginator->url($page)"
    :wire:click="in_livewire() ? $wireClick : false"
    :wire:loading.attr="in_livewire() ? 'disabled' : false"
    :label="$page"
/>
