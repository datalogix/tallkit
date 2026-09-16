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

[$name, $fieldName, , $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: false, id: $id);
$iconOn ??= $icon;
$iconOff ??= $icon;
$hasStateLabel = $labelOn || $labelOff;
$content = $slot->isEmpty() ? $label : $slot;
$hasContent = $content || $hasStateLabel;
$isColored = $color && TALLKit::isColor($color) && ! in_array($color, ['slate', 'gray', 'zinc', 'stone'], true);

$wireModelDirective = $attributes->wire('model');
$isLiveModel = $wireModelDirective?->directive && $wireModelDirective->hasModifier('live');
$hasWireChange = $attributes->whereStartsWith('wire:change')->isNotEmpty();

$loading ??= $hasWireChange || $isLiveModel;
$loadingAction = $hasWireChange ? $attributes->whereStartsWith('wire:change')->first() : null;
$loadingModel = $isLiveModel ? $wireModelDirective->value() : null;

@endphp
<tk:field.wrapper
    inline
    :$align
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :label="false"
>
    <tk:tooltip.wrapper :$attributes :$tooltip>
        <label
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'control:')
                    ->dataKey('control')
                    ->merge([
                        'x-data' => $loading ? 'toggle('.Js::from(array_filter([
                            'action' => $loadingAction,
                            'model' => $loadingModel,
                            'delay' => $loadingDelay,
                            'minDuration' => $loadingMinDuration,
                        ], fn ($value) => $value !== null)).')' : null,
                        'disabled' => $attributes->get('disabled'),
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
                        TALLKit::textNeutral(variant: 'muted', prefix: '[:where(&)]:'),
                        $isColored
                            ? TALLKit::text(color: $color, prefix: 'has-[input:checked]:')
                            : TALLKit::textNeutral(variant: 'strong', prefix: 'has-[input:checked]:'),
                    ])
            }}
        >
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
                            'aria-label' => $hasContent ? null : ($tooltip ?: __('Toggle')),
                            'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
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
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-on:')
                        ->classes(
                            'hidden peer-checked:inline-flex [&_*]:fill-current',
                            $isColored ? TALLKit::text(color: $color) : TALLKit::textNeutral(variant: 'strong'),
                        )
                        ->when($loading, fn ($attrs) => $attrs->merge(['x-show' => '!blocking']))
                    "
                />
            @endif

            @if ($iconOff)
                <tk:icon
                    :icon="$iconOff"
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-off:')
                        ->classes('inline-flex peer-checked:hidden')
                        ->when($loading, fn ($attrs) => $attrs->merge(['x-show' => '!blocking']))
                    "
                />
            @endif

            @if ($loading)
                <tk:loading
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loading:')->dataKey('icon')"
                    x-show="blocking"
                    x-cloak
                    :announce="false"
                />
            @endif

            @if ($hasStateLabel)
                <tk:element
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'label-checked:')->classes('label-checked hidden peer-checked:inline')"
                    :label="$labelOn"
                />
                <tk:element
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'label-unchecked:')->classes('label-unchecked inline peer-checked:hidden')"
                    :label="$labelOff"
                />
            @elseif ($content)
                <tk:element
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'label:')"
                    :label="$content"
                />
            @endif
        </label>
    </tk:tooltip.wrapper>
</tk:field.wrapper>
