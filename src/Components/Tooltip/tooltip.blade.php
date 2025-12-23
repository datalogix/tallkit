<div
    x-data="popover({
        mode: @js($mode ?? 'hover'),
        position: @js($position ?? 'top'),
        align: @js($align ?? 'center')
    })"
    {{ $attributesAfter('container:')->classes('contents') }}
>
    @if (strip_tags($slot) === (string) $slot)
        <span>{{ $slot }}</span>
    @else
        {{ $slot }}
    @endif

    @if ($content)
        <tk:tooltip.content
            :attributes="$attributes->whereDoesntStartWith(['container:'])"
            :$kbd
            :$variant
            :$size
            :$arrow
        >
            {!! nl2br(__($content)) !!}
        </tk:tooltip.content>
    @endif
</div>
