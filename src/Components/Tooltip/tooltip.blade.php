<div
    x-data="tooltip({
        mode: @js($mode),
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
            :attributes="$attributes->whereDoesntStartWith(['container:', 'kbd:'])"
            :$variant
            :$size
            :$arrow
        >
            {!! __($content) !!}

            @if ($kbd)
                <x-slot name="kbd" :attributes="$attributesAfter('kbd:')">
                    {{ $kbd }}
                </x-slot>
            @endif
        </tk:tooltip.content>
    @endif
</div>
