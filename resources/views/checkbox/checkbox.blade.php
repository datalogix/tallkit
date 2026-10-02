@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'indeterminate' => null,
    'iconOn' => null,
    'iconOff' => null,
    'iconIndeterminate' => null,
    'group' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::fieldContext(attributes: $attributes, label: $label, id: $id, scope: get_defined_vars());
$checked = is_array($checked) ? in_array($value, $checked) : (bool) $checked;
$checked = TALLKit::fieldOldChecked($fieldName, $checked, $value);

@endphp
<tk:field.wrapper
    inline
    :$align
    :$name
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    :label="$slot->isEmpty() ? $label : $slot"
>
    <div
        {{ $attributes->only('disabled')->dataKey('control') }}
        {{
            $attributes->prefixed('control:')
                ->classes(
                    'flex outline-offset-2 relative',
                    TALLKit::widthHeight(size: $size),
                )
        }}
    >
        <input
            @checked($checked)
            type="checkbox"
            @if ($indeterminate) x-init="$el.indeterminate = true" @endif
            {{
                $attributes
                    ->dataKey('checkbox')
                    ->dataKey('checkbox-group', $group)
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        'aria-describedby' => TALLKit::fieldDescribedBy(id: $id, description: $description, help: $help, invalid: $invalid, showError: $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                        'aria-label' => $label ? null : __('Checkbox'),
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes(extra: [
                        'icon-area:', 'icon-on:', 'icon-off:', 'icon-indeterminate:',
                    ]))
                    ->classes(
                        '
                            tk-control-transition
                            tk-control-surface
                            tk-control-invalid-border
                            tk-control-disabled
                            tk-control-focus-ring

                            rounded
                            peer
                            shrink-0
                            size-full
                            appearance-none
                            [print-color-adjust:exact]

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
                $attributes->prefixed('icon-area:')
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

                            [&_.checked]:hidden
                            [&_.unchecked]:block
                            [&_.indeterminate]:hidden

                            peer-checked:[&_.checked]:block
                            peer-checked:[&_.unchecked]:hidden
                            peer-checked:[&_.indeterminate]:hidden

                            peer-indeterminate:[&_.checked]:hidden
                            peer-indeterminate:[&_.unchecked]:hidden
                            peer-indeterminate:[&_.indeterminate]:block
                        ',
                    )
            }}
        >
            <tk:icon
                :icon="$iconOn ?? 'check'"
                :attributes="$attributes->prefixed('icon-on:')->classes(
                    'size-full m-px checked scale-90',
                    match (true) {
                        $color === 'accent' => 'text-[var(--color-accent-foreground)]',
                        TALLKit::isColor(color: $color) => TALLKit::checkedForeground(color: $color),
                        default => 'text-white dark:text-zinc-700',
                    },
                )"
            />

            @if ($iconOff)
                <tk:icon
                    :icon="$iconOff"
                    :attributes="$attributes->prefixed('icon-off:')->classes('size-full m-px unchecked')"
                />
            @endif

            @if ($indeterminate)
                <tk:icon
                    :icon="$iconIndeterminate ?? 'minus'"
                    :attributes="$attributes->prefixed('icon-indeterminate:')->classes('size-full m-px indeterminate')"
                />
            @endif
        </div>
    </div>
</tk:field.wrapper>
