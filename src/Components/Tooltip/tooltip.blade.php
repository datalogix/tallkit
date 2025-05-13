<div
    x-data="tooltip({ position: '{{ $position }}', align: '{{ $align }}' })"
    x-bind="trigger({{ $toggleable }})"
    {{ $attributes->classes('inline-block') }}
>
    {{ $slot }}

    @if ($content)
        <tk:tooltip.content :$kbd>
            {{ $content }}
        </tk:tooltip.content>
    @endif
</div>
