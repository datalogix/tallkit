@props([
    ...TALLKit::elementProps(),
    'preview' => null,
    'swatches' => null,
    'clearable' => null,
    'dropper' => null,
    'size' => null,
    'keepOpen' => null,
    'standalone' => null,
    'value' => null,
    'format' => null,
])
@php

$swatches ??= ['#ffffff', '#000000', '#71717a', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#0ea5e9', '#6366f1', '#a855f7', '#ec4899', '#84cc16', '#78716c', '#18181b'];
$style = $preview === 'underline'
    ? "{ boxShadow: shownValue() ? 'inset 0 -4px 0 0 ' + shownValue() : '' }"
    : 'swatchStyle(shownValue())';
$fieldAttributes = $standalone !== false
    ? $attributes->filter(fn ($value, $key) => in_array($key, ['name', 'disabled', 'readonly']) || str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model'))
    : null;
$ariaLabelBinding = has_content($ariaLabel) ?Js::from(__($ariaLabel))." + (shownValue() ? ', ' + shownValue() : '')" : null;

@endphp
@if ($standalone !== false)
<div
    wire:ignore.self
    x-data="colorPicker({{ Js::from([
        'value' => $value,
        'format' => $format,
    ]) }})"
    class="inline-flex"
>
    <input
        type="hidden"
        {{
            $fieldAttributes
                ->dataKey('color-picker')
                ->merge(['value' => unless_livewire($value)])
        }}
    />
@endif

<tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
    <tk:dropdown.button
        :attributes="$attributes->withElementProps(get_defined_vars())->whereDoesntStartWith(['dropdown:', 'popover:', 'swatch:', 'option:', 'footer:', 'custom:', 'dropper:', 'clearable:', 'wire:model', 'x-model'])->except('name')->merge(['icon' => 'palette'])->merge($ariaLabelBinding ? ['x-bind:aria-label' => $ariaLabelBinding] : [])"
        :$size
        ::style="{{ $style }}"
        :icon::class="$preview === 'underline' ? null : '{
            \'opacity-100\': !shownValue(),
            \'opacity-0\': shownValue(),
        }'"
        :arrow="false"
    />

    <tk:popover
        :attributes="$attributes->prefixed('popover:')->classes('p-2 space-y-2')"
        :$keepOpen
    >
        <div {{ $attributes->prefixed('swatch:')->classes('grid grid-cols-5 gap-1') }}>
            <template x-for="(swatch, index) in {{ Js::from(array_values($swatches)) }}" :key="index">
                <tk:button
                    :attributes="$attributes->prefixed('option:')
                        ->classes(
                            '
                                tk-control-transition
                                inset-ring inset-ring-black/10 dark:inset-ring-white/20

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
                    ::data-active="isActive(swatch)"
                    ::aria-pressed="String(isActive(swatch))"
                    @click="pick(swatch)"
                    ::title="swatch"
                    ::aria-label="swatch"
                    ::style="swatchStyle(swatch)"
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
                    x-bind:value="hexValue()"
                    @input="previewNative($event.target.value)"
                    @change="pickNative($event.target.value)"
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
                        :attributes="$attributes->prefixed('dropper:')->merge(['tooltip' => 'Pick color from screen', 'variant' => 'none'])"
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
                    :label="string_value($clearable, 'Clear')"
                    :icon="false"
                    action="clear()"
                    x-bind:class="{ 'hidden': !value }"
                />
            @endif
        </div>
    </tk:popover>
</tk:dropdown>

@if ($standalone !== false)
</div>
@endif
