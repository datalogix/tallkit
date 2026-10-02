@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'icon' => null,
    'iconOn' => null,
    'iconOff' => null,
    'labelOn' => null,
    'labelOff' => null,
    'variant' => null,
    'tooltip' => null,
    'loading' => null,
    'loadingDelay' => null,
    'loadingMinDuration' => null,
])
@php

[$name, $fieldName, , $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: false, id: $id, scope: get_defined_vars());
$checked = TALLKit::fieldOldChecked($fieldName, (bool) $checked, $value ?? null);
$iconOn ??= $icon;
$iconOff ??= $icon;
$hasStateLabel = $labelOn || $labelOff;
$content = $slot->isEmpty() ? $label : $slot;
$hasContent = $content || $hasStateLabel;
$isColored = $color && TALLKit::isColor($color) && ! in_array($color, ['slate', 'gray', 'zinc', 'stone'], true);

$liveModel = TALLKit::livewireLiveModel($attributes);
$isLiveModel = $liveModel !== null;
$hasWireChange = $attributes->whereStartsWith('wire:change')->isNotEmpty();

$loading ??= $hasWireChange || $isLiveModel;
$loadingAction = $hasWireChange ? $attributes->whereStartsWith('wire:change')->first() : null;
$loadingModel = $liveModel;

$tip = TALLKit::tooltip($tooltip, $attributes, name: $hasContent ? strip_tags((string) $content) : null, isName: ! $hasContent);
$tipText = $tip['attributes'][TALLKit::dataKey('tooltip')] ?? null;

@endphp
<tk:field.wrapper
    inline
    :$align
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :label="false"
>
    <label
        {{
            $attributes->prefixed('control:')
                ->dataKey('control')
                ->merge($tip['attributes'])
                ->merge([
                    'x-data' => $loading ? 'toggle('.Js::from(array_filter([
                        'action' => $loadingAction,
                        'model' => $loadingModel,
                        'delay' => $loadingDelay,
                        'minDuration' => $loadingMinDuration,
                    ], fn ($value) => $value !== null)).')' : null,
                    'disabled' => TALLKit::isAttributeEnabled($attributes->get('disabled')) ?: null,
                ])
                ->classes([
                    '
                        tk-control-transition
                        tk-control-focus-ring-self
                        tk-control-invalid-ring-self

                        inline-flex
                        items-center
                        justify-center
                        cursor-pointer
                        select-none
                        [print-color-adjust:exact]

                        has-[input:disabled]:opacity-disabled
                        has-[input:disabled]:cursor-not-allowed
                    ',
                    TALLKit::height(size: $size),
                    TALLKit::paddingInline(size: $size) => $hasContent,
                    TALLKit::width(size: $size) => !$hasContent,
                    TALLKit::gap(size: $size, mode: 'small'),
                    TALLKit::roundedSize(size: $size),
                    TALLKit::fontSize(size: $size, weight: true),
                    TALLKit::iconSize(size: $size),
                    match ($variant) {
                        'filled' => '
                            bg-zinc-800/5
                            dark:bg-white/10

                            has-[input:not(:disabled)]:hover:bg-zinc-800/15
                            dark:has-[input:not(:disabled)]:hover:bg-white/20
                        ',
                        'ghost' => '
                            bg-transparent

                            has-[input:not(:disabled)]:hover:bg-zinc-800/10
                            dark:has-[input:not(:disabled)]:hover:bg-white/10
                        ',
                        'subtle' => '
                            bg-transparent

                            has-[input:not(:disabled)]:hover:bg-zinc-800/5
                            dark:has-[input:not(:disabled)]:hover:bg-white/5
                        ',
                        default => '
                            border
                            border-zinc-200
                            dark:border-white/10

                            bg-white
                            dark:bg-zinc-700

                            has-[input:not(:disabled)]:hover:bg-zinc-800/5
                            dark:has-[input:not(:disabled)]:hover:bg-zinc-600/85
                        ',
                    },
                    '[:where(&)]:text-zinc-600 dark:[:where(&)]:text-zinc-300',
                    $isColored
                        ? 'tk-color-'.$color.' has-[input:checked]:bg-(--tk-soft) has-[input:checked]:text-(--tk-on-soft)'
                        : TALLKit::classes(
                            TALLKit::textNeutral(variant: 'strong', prefix: 'has-[input:checked]:'),
                            'has-[input:checked]:bg-zinc-800/10 dark:has-[input:checked]:bg-white/15',
                        ),
                ])
        }}
    >
        @if ($tip['html'])
            <template {{ TALLKit::dataKey('tooltip-content') }}>{!! $tip['html'] !!}</template>
        @endif
        <input
            @checked((bool) $checked)
            type="checkbox"
            {{
                $attributes
                    ->dataKey('toggle')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        'aria-label' => $hasContent ? null : ($tipText ?: __('Toggle')),
                        'aria-describedby' => collect([
                            TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                            $tip['describedBy'],
                        ])->filter()->implode(' ') ?: null,
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                        'icon-on:', 'icon-off:', 'label-checked:', 'label-unchecked:',
                    ]))
                    ->classes('sr-only peer')
            }}
        />

        @if ($iconOn)
            <tk:icon
                :icon="$iconOn"
                :attributes="$attributes->prefixed('icon-on:')
                    ->classes(
                        'hidden peer-checked:inline-flex [&_*]:fill-current',
                        $isColored ? TALLKit::mutedText(color: $color) : TALLKit::textNeutral(variant: 'strong'),
                    )
                    ->when($loading, fn ($attrs) => $attrs->merge(['x-show' => '!blocking']))
                "
            />
        @endif

        @if ($iconOff)
            <tk:icon
                :icon="$iconOff"
                :attributes="$attributes->prefixed('icon-off:')
                    ->classes('inline-flex peer-checked:hidden')
                    ->when($loading, fn ($attrs) => $attrs->merge(['x-show' => '!blocking']))
                "
            />
        @endif

        @if ($loading)
            <tk:loading
                :attributes="$attributes->prefixed('loading:')->dataKey('icon')"
                x-show="blocking"
                x-cloak
                :announce="false"
            />
        @endif

        @if ($hasStateLabel)
            <tk:element
                :attributes="$attributes->prefixed('label-checked:')->classes('label-checked hidden peer-checked:inline')"
                :label="$labelOn"
            />
            <tk:element
                :attributes="$attributes->prefixed('label-unchecked:')->classes('label-unchecked inline peer-checked:hidden')"
                :label="$labelOff"
            />
        @elseif ($content)
            <tk:element
                :attributes="$attributes->prefixed('label:')"
                :label="$content"
            />
        @endif
    </label>
</tk:field.wrapper>
