@props([
    ...TALLKit::elementProps(),
    'preview' => null,
    'swatches' => null,
    'clearable' => null,
    'dropper' => null,
    'size' => null,
    'keepOpen' => null,
])
@php

$swatches ??= ['#ffffff', '#000000', '#71717a', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#0ea5e9', '#6366f1', '#a855f7', '#ec4899', '#84cc16', '#78716c', '#18181b'];
$style = $preview === 'underline' ? "value ? 'box-shadow: inset 0 -2px 0 0 ' + value : ''" : "'background-color: ' + (value || 'transparent')";

@endphp
<tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
    <tk:dropdown.button
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:'])->merge(['icon' => 'palette'])"
        :$size
        ::style="{{ $style }}"
        :icon::class="$preview === 'underline' ? null : '{
            \'opacity-100\': !value,
            \'opacity-0\': value,
        }'"
        :arrow="false"
    />

    <tk:popover
        :attributes="$attributes->prefixed('popover:')->classes('p-2 space-y-2')"
        :$keepOpen
    >
        <div {{ $attributes->prefixed('swatch:')->classes('grid grid-cols-5 gap-1') }}>
            <template x-for="(swatch, index) in @js(array_values($swatches))" :key="index">
                <tk:button
                    :attributes="$attributes->prefixed('option:')
                        ->classes(
                            '
                                tk-control-transition

                                [&[data-active]]:ring-2
                                [&[data-active]]:ring-offset-2
                                [&[data-active]]:ring-black/40
                                [&[data-active]]:dark:ring-white/60
                                [&[data-active]]:ring-offset-white
                                [&[data-active]]:dark:ring-offset-zinc-700
                            ',
                            TALLKit::widthHeight(size: $size)
                        )
                    "
                    ::data-active="value === swatch"
                    @click="pick(swatch)"
                    ::title="swatch"
                    ::style="{ backgroundColor: swatch }"
                />
            </template>
        </div>

        <div
            {{
                $attributes->prefixed('footer:')
                    ->classes('flex items-center justify-between gap-2 border-t border-zinc-100 pt-2 dark:border-white/10')
            }}
        >
            <div class="flex items-center gap-2">
                <input
                    type="color"
                    data-keep-open
                    x-bind:value="value || '#000000'"
                    @input="pick($event.target.value)"
                    {{
                        $attributes->prefixed('custom:')
                            ->classes(
                                'cursor-pointer tk-control-surface',
                                TALLKit::roundedSize(size: $size),
                                TALLKit::widthHeight(size: $size)
                            )
                            ->merge(['title' => __('Custom color')])
                    }}
                >

                @if ($dropper)
                    <tk:button
                        :attributes="$attributes->prefixed('dropper:')->merge(['tooltip' => __('Pick color'), 'variant' => 'none'])"
                        :size="TALLKit::adjustSize(size: $size)"
                        @click="dropColor()"
                        x-show="hasEyeDropper()"
                        icon="eye-dropper"
                    />
                @endif
            </div>

            @if ($clearable !== false)
                <tk:clearable
                    :attributes="$attributes->prefixed('clearable:')"
                    :size="TALLKit::adjustSize(size: $size)"
                    :label="is_string($clearable) ? $clearable : 'Clear'"
                    :icon="false"
                />
            @endif
        </div>
    </tk:popover>
</tk:dropdown>
