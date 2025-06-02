<div
    x-data="dropdown({ position: '{{ $position ?? 'bottom' }}', align: '{{ $align ?? 'start' }}' })"
    {{ $attributes->classes('inline-block') }}
    data-tallkit-dropdown
>
    {{ $slot }}
</div>
