<div x-data="{{ $toggleable ? 'dropdown' : 'tooltip' }}({ position: '{{ $position ?? 'top' }}', align: '{{ $align ?? 'center' }}' })"
    {{ $attributes->whereDoesntStartWith(['content:', 'kbd:'])->classes('inline-block') }} data-tallkit-tooltip>
    {{ $slot }}

    @if ($content)
        <tk:tooltip.content :attributes="$attributesAfter('content:')">
            {{ $content }}

            @if ($kbd)
                <x-slot:kbd :attributes="$attributesAfter('kbd:')">
                    {{ $kbd }}
                </x-slot:kbd>
            @endif
        </tk:tooltip.content>
    @endif
</div>
