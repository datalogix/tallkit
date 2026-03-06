@if ($prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class'))
    <div
        wire:ignore
        x-data="fieldControl"
        {{ $dataKey() }}
        {{
            $attributesAfter('control:')->classes(
                '
                    [:where(&)]:w-full relative block

                    [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-prepend]]:items-start
                    [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-append]]:items-start
                ',
                $attributes->get('class')
            )
        }}
    >
        @if ($prepend || $icon)
            <div
                wire:cloak
                {{
                    $attributesAfter('prepend:')->classes(
                        'absolute top-0 bottom-0 flex items-center justify-center gap-x-1.5 pe-3 ps-3 start-0 text-zinc-400',
                    )
                }}
            >
                {{ $prepend ?? '' }}

                @if (is_string($icon) && $icon !== '')
                    <tk:icon
                        :attributes="$attributesAfter('icon:')->classes('pointer-events-none')"
                        :$size
                        :$icon
                    />
                @elseif ($icon)
                    <tk:element
                        :attributes="$attributesAfter('icon:')"
                        :label="$icon"
                    />
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($append || $loading || $iconTrailing || $kbd)
            <div
                wire:cloak
                {{
                    $attributesAfter('append:')->classes('
                        absolute top-0 bottom-0 flex items-center justify-center gap-x-1.5 pe-3 ps-3 end-0 text-zinc-400
                    ')
                }}
            >
                @if ($loading)
                    <tk:loading
                        :attributes="$attributesAfter('loading:')->classes('opacity-0')->merge([
                            'wire:loading.class.remove' => 'opacity-0',
                            'wire:target' => $wireTarget
                        ])"
                        :$size
                    />
                @endif

                @if (is_string($iconTrailing) && $iconTrailing !== '')
                    <tk:icon
                        :attributes="$attributesAfter('icon-trailing:')->classes('pointer-events-none')"
                        :$size
                        :icon="$iconTrailing"
                    />
                @elseif ($iconTrailing)
                    <tk:element
                        :attributes="$attributesAfter('icon-trailing:')"
                        :label="$iconTrailing"
                    />
                @endif

                @if (isset($kbd) && $kbd !== '')
                    <tk:kbd
                        :attributes="$attributesAfter('kbd:')"
                        :$size
                        :label="$kbd"
                    />
                @endif

                {{ $append ?? '' }}
            </div>
        @endif
    </div>
@else
    {{ $slot }}
@endif
