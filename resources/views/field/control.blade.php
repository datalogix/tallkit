@props([
    'size' => null,
    'prepend' => null,
    'append' => null,
    'icon' => null,
    'iconTrailing' => null,
    'kbd' => null,
    'loading' => null,
])
@php

$wireTarget = null;

if (is_string($loading) || $loading === true) {
    $wireModel = $attributes->wire('model');

    if ($wireModel?->directive && $wireModel->hasModifier('live')) {
        $loading = true;
        $wireTarget = $wireModel->value();
    } else {
        $wireTarget = $loading;
        $loading = (bool) $loading;
    }
}

@endphp
@if ($prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class'))
    <div
        {{
            TALLKit::attributesAfter($attributes, 'control:')
                ->dataKey('field-control')
                ->classes(
                    '
                        [:where(&)]:w-full flex
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-prepend]]:items-start
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-append]]:items-start
                    ',
                    $attributes->get('class')
                )
        }}
    >
        @if ($prepend || $icon)
            <div
                {{
                    TALLKit::attributesAfter($attributes, 'prepend:')
                        ->dataKey('field-control-prepend')
                        ->classes('flex items-center justify-center gap-x-1.5 ps-3 text-zinc-400')
                }}
            >
                {{ $prepend ?? '' }}

                @if (is_string($icon) && $icon !== '')
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')->classes('pointer-events-none')"
                        :$size
                        :$icon
                    />
                @elseif ($icon)
                    <tk:element
                        :attributes="TALLKit::attributesAfter($attributes, 'icon:')"
                        :label="$icon"
                    />
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($append || $loading || $iconTrailing || $kbd)
            <div
                {{
                    TALLKit::attributesAfter($attributes, 'append:')
                        ->dataKey('field-control-append')
                        ->classes('
                            flex items-center justify-center gap-x-1.5 pe-3 text-zinc-400
                            [&:has([data-tallkit-loading].hidden)]:pe-0
                        ')
                        ->classes(['[&:has([data-tallkit-loading].hidden)]:pe-0' => !$append && $loading && !$iconTrailing && !$kbd])
                }}
            >
                @if ($loading)
                    <tk:loading
                        :attributes="TALLKit::attributesAfter($attributes, 'loading:')->classes('hidden')->merge([
                            'wire:loading.class.remove' => 'hidden',
                            'wire:target' => $wireTarget
                        ])"
                        :$size
                    />
                @endif

                @if (is_string($iconTrailing) && $iconTrailing !== '')
                    <tk:icon
                        :attributes="TALLKit::attributesAfter($attributes, 'icon-trailing:')->classes('pointer-events-none')"
                        :$size
                        :icon="$iconTrailing"
                    />
                @elseif ($iconTrailing)
                    <tk:element
                        :attributes="TALLKit::attributesAfter($attributes, 'icon-trailing:')"
                        :label="$iconTrailing"
                    />
                @endif

                @if (isset($kbd) && $kbd !== '')
                    <tk:kbd
                        :attributes="TALLKit::attributesAfter($attributes, 'kbd:')"
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
