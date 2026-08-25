@props([
    'animate' => null,
    'size' => null,
])
<div
    role="status"
    {{ $attributes->classes('flex flex-col', TALLKit::gap(size: $size)) }}
>
    <span class="sr-only">{{ __('Loading...') }}</span>

    {{ $slot }}
</div>
