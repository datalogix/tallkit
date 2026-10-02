@props([
    'size' => null,
    ...TALLKit::fieldControlProps(),
])
@php

$innerSize = TALLKit::adjustSize(size: $size);
$wireTarget = null;

if (is_string($loading) || $loading === true) {
    $liveModel = TALLKit::livewireLiveModel($attributes);

    if ($liveModel !== null) {
        $loading = true;
        $wireTarget = $liveModel;
    } else {
        $wireTarget = $loading;
        $loading = (bool) $loading;
    }
}

@endphp
@if ($prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class'))
    <div
        {{
            $attributes->prefixed('control:')
                ->dataKey('field-control')
                ->classes(
                    '
                        [:where(&)]:w-full flex
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-prepend]]:items-start
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-append]]:items-start
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-append]]:ps-3

                        [&:has(:is(textarea,select[multiple]))]:items-start!
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-prepend]]:py-2
                        [&:has(:is(textarea,select[multiple]))_[data-tallkit-field-control-append]]:py-2
                    ',
                    $attributes->get('class')
                )
        }}
    >
        @if ($prepend || $icon)
            <div
                {{
                    $attributes->prefixed('prepend:')
                        ->dataKey('field-control-prepend')
                        ->classes(
                            'flex items-center justify-center gap-x-1.5 ps-3',
                            TALLKit::textNeutral(variant: 'subtle'),
                        )
                }}
            >
                {{ $prepend ?? '' }}

                @if (is_string($icon) && $icon !== '')
                    <tk:icon
                        :attributes="$attributes->prefixed('icon:')->classes('pointer-events-none')"
                        :size="$innerSize"
                        :$icon
                    />
                @elseif ($icon)
                    <tk:element
                        :attributes="$attributes->prefixed('icon:')"
                        :label="$icon"
                    />
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($append || $loading || $iconTrailing || $kbd)
            <div
                {{
                    $attributes->prefixed('append:')
                        ->dataKey('field-control-append')
                        ->classes([
                            'flex items-center justify-center gap-x-1.5 pe-3',
                            TALLKit::textNeutral(variant: 'subtle'),
                            '[&:has([data-tallkit-loading].hidden)]:pe-0' => ! $append && $loading && ! $iconTrailing && ! $kbd,
                        ])
                }}
            >
                @if ($loading)
                    <tk:loading
                        :attributes="$attributes->prefixed('loading:')->classes('hidden')->merge([
                            'wire:loading.class.remove' => 'hidden',
                            'wire:target' => $wireTarget
                        ])"
                        :size="$innerSize"
                    />
                @endif

                @if (is_string($iconTrailing) && $iconTrailing !== '')
                    <tk:icon
                        :attributes="$attributes->prefixed('icon-trailing:')->classes('pointer-events-none')"
                        :size="$innerSize"
                        :icon="$iconTrailing"
                    />
                @elseif ($iconTrailing)
                    <tk:element
                        :attributes="$attributes->prefixed('icon-trailing:')"
                        :label="$iconTrailing"
                    />
                @endif

                @if (isset($kbd) && $kbd !== '')
                    <tk:kbd
                        :attributes="$attributes->prefixed('kbd:')"
                        :size="$innerSize"
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
