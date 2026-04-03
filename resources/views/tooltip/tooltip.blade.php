@props([
    'position' => null,
    'align' => null,
    'content' => null,
    'kbd' => null,
    'mode' => null,
    'size' => null,
    'variant' => null,
    'arrow' => null,
])
<div
    wire:replace.self
    x-data="popover({
        mode: @js($mode ?? 'hover'),
        position: @js($position ?? 'top'),
        align: @js($align ?? 'center')
    })"
    {{ TALLKit::attributesAfter($attributes, 'container:')->classes('contents') }}
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
            @if (TALLKit::isSlot($content))
                {{ $content }}
            @else
                {!! nl2br(__($content)) !!}
            @endif
        </tk:tooltip.content>
    @endif
</div>
