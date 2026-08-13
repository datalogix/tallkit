@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'variant' => null,
    'indeterminate' => null,
    'iconOn' => null,
    'iconOff' => null,
    'iconIndeterminate' => null,
    'group' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = TALLKit::resolveFieldContext($attributes, $label, $id);
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
            TALLKit::attributesAfter($attributes, 'control:')
                ->classes(
                    'flex outline-offset-2 relative',
                    TALLKit::widthHeight(size: $size),
                )
        }}
    >
        <input
            @checked($checked)
            type="checkbox"
            x-init="$el.indeterminate = @js((bool) $indeterminate)"
            {{
                $attributes
                    ->dataKey('checkbox')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        TALLKit::dataKey('checkbox-group') => $group,
                        'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                        'aria-label' => $label ? null : __('Checkbox'),
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
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

                            disabled:cursor-not-allowed

                            checked:shadow-none
                            checked:not-[data-invalid]:border-none
                            checked:disabled:opacity-30

                            enabled:hover:border-zinc-300
                            dark:enabled:hover:border-white/20
                        ',
                        match ($variant) {
                            'accent' => 'checked:bg-[var(--color-accent)]',
                            'red' => 'checked:bg-red-600 dark:checked:bg-red-500',
                            'orange' => 'checked:bg-orange-600 dark:checked:bg-orange-500',
                            'amber' => 'checked:bg-amber-600 dark:checked:bg-amber-500',
                            'yellow' => 'checked:bg-yellow-600 dark:checked:bg-yellow-500',
                            'lime' => 'checked:bg-lime-600 dark:checked:bg-lime-500',
                            'green' => 'checked:bg-green-600 dark:checked:bg-green-500',
                            'emerald' => 'checked:bg-emerald-600 dark:checked:bg-emerald-500',
                            'teal' => 'checked:bg-teal-600 dark:checked:bg-teal-500',
                            'cyan' => 'checked:bg-cyan-600 dark:checked:bg-cyan-500',
                            'sky' => 'checked:bg-sky-600 dark:checked:bg-sky-500',
                            'blue' => 'checked:bg-blue-600 dark:checked:bg-blue-500',
                            'indigo' => 'checked:bg-indigo-600 dark:checked:bg-indigo-500',
                            'violet' => 'checked:bg-violet-600 dark:checked:bg-violet-500',
                            'purple' => 'checked:bg-purple-600 dark:checked:bg-purple-500',
                            'fuchsia' => 'checked:bg-fuchsia-600 dark:checked:bg-fuchsia-500',
                            'pink' => 'checked:bg-pink-600 dark:checked:bg-pink-500',
                            'rose' => 'checked:bg-rose-600 dark:checked:bg-rose-500',
                            default => 'checked:bg-zinc-800 dark:checked:bg-white',
                        },
                    )
            }}
        />

        <div
            {{
                TALLKit::attributesAfter($attributes, 'icon-area:')
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
                :attributes="TALLKit::attributesAfter($attributes, 'icon-on:')->classes(
                    'size-full m-px checked scale-90',
                    match ($variant) {
                        'accent' => 'text-[var(--color-accent-foreground)]',
                        'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose' => 'text-white',
                        default => 'text-white dark:text-zinc-700',
                    },
                )"
            />

            @if ($iconOff)
                <tk:icon
                    :icon="$iconOff"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-off:')->classes('size-full m-px unchecked')"
                />
            @endif

            @if ($indeterminate)
                <tk:icon
                    :icon="$iconIndeterminate ?? 'minus'"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-indeterminate:')->classes('size-full m-px indeterminate')"
                />
            @endif
        </div>
    </div>
</tk:field.wrapper>
