@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext(attributes: $attributes, label: $label, id: $id);
$checked = is_array($checked) ? in_array($value, $checked) : (bool) $checked;

@endphp
<tk:field.wrapper
    inline
    :$align
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :label="$slot->isEmpty() ? $label : $slot"
>
    <div
        {{ $attributes->only('disabled')->dataKey('control') }}
        {{
            TALLKit::attributesAfter(attributes: $attributes, prefix: 'control:')
                ->classes(
                    'flex outline-offset-2 relative',
                    TALLKit::widthHeight(size: $size),
                )
        }}
    >
        <input
            @checked($checked)
            type="radio"
            {{
                $attributes
                    ->dataKey('radio')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        'aria-describedby' => TALLKit::ariaDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                        'aria-label' => $label ? null : __('Radio'),
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: ['icon-area:']))
                    ->classes(
                        '
                            tk-control-transition
                            tk-control-surface
                            tk-control-invalid-border
                            tk-control-disabled
                            tk-control-focus-ring

                            rounded-full
                            peer
                            shrink-0
                            size-full
                            appearance-none
                            [print-color-adjust:exact]

                            disabled:cursor-not-allowed

                            checked:shadow-none
                            checked:not-[data-invalid]:border-none
                            checked:disabled:opacity-30
                            dark:checked:disabled:opacity-20

                            enabled:hover:border-zinc-300
                            dark:enabled:hover:border-white/20
                        ',
                        match ($color) {
                            'accent' => 'checked:bg-[var(--color-accent)]',
                            default => TALLKit::checkedBackground(color: $color) ?? 'checked:bg-zinc-800 dark:checked:bg-white',
                        },
                    )
            }}
        />

        <div
            {{
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon-area:')
                    ->classes(
                        '
                            absolute
                            pointer-events-none
                            size-full

                            transition-opacity
                            duration-200
                            ease-out
                            motion-reduce:transition-none

                            flex
                            justify-center
                            items-center

                            opacity-0
                            peer-checked:opacity-100
                        '
                    )
            }}
        >
            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')->classes(
                        'rounded-full',
                        TALLKit::widthHeight(size: $size, mode: 'smallest'),
                        match (true) {
                            $color === 'accent' => 'bg-[var(--color-accent-foreground)]',
                            TALLKit::isColor(color: $color) => 'bg-white',
                            default => 'bg-white dark:bg-zinc-700',
                        },
                    )
                }}
            ></div>
        </div>
    </div>
</tk:field.wrapper>
