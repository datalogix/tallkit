@props([
    'preview' => null,
    'icon' => null,
    'swatches' => null,
    'clearable' => null,
    'dropper' => null,
    'size' => null,
])
@php

$swatches ??= ['#ffffff', '#000000', '#71717a', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#0ea5e9', '#6366f1', '#a855f7', '#ec4899', '#84cc16', '#78716c', '#18181b'];
$style = $preview === 'underline' ? "value ? 'box-shadow: inset 0 -2px 0 0 ' + value : ''" : "'background-color: ' + (value || 'transparent')";

@endphp
<tk:dropdown :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dropdown:')">
    <tk:dropdown.button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:'])"
        :$size
        ::style="{{ $style }}"
        :icon="$icon ?? 'palette'"
        :icon::class="$preview === 'underline' ? null : '{
            \'opacity-100\': !value,
            \'opacity-0\': value,
        }'"
        :arrow="false"
    />

    <tk:popover :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'popover:')->classes('p-2 space-y-2')">
        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'swatch:')->classes('grid grid-cols-5 gap-1') }}>
            @foreach ($swatches as $swatch)
                <tk:button
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'option:')
                        ->classes(
                            'tk-control-transition',
                            TALLKit::widthHeight(size: $size)
                        )
                    "
                    @click="pick('{{ $swatch }}')"
                    title="{{ $swatch }}"
                    style="background-color: {{ $swatch }}"
                />
            @endforeach
        </div>

        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'footer:')
                    ->classes('flex items-center justify-between gap-2 border-t border-zinc-100 pt-2 dark:border-white/10')
            }}
        >
            <div class="flex items-center gap-2">
                <input
                    type="color"
                    data-keep-open
                    x-bind:value="value || '#000000'"
                    @input="pick($event.target.value)"
                    title="{{ __('Custom color') }}"
                    {{
                        TALLKit::attributesAfter(attributes: $attributes, prefix: 'custom:')
                            ->classes(
                                'cursor-pointer tk-control-surface',
                                TALLKit::roundedSize(size: $size),
                                TALLKit::widthHeight(size: $size)
                            )
                    }}
                >

                @if ($dropper)
                    <tk:button
                        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dropper:')"
                        :size="TALLKit::adjustSize(size: $size)"
                        @click="dropColor()"
                        tooltip="{{ __('Pick color') }}"
                        icon="eye-dropper"
                        variant="none"
                    />
                @endif
            </div>

            @if ($clearable !== false)
                <tk:clearable
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'clearable:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    :label="is_string($clearable) ? $clearable : 'Clear'"
                    :icon="false"
                />
            @endif
        </div>
    </tk:popover>
</tk:dropdown>
