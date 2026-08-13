@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'variant' => null,
    'iconOn' => null,
    'iconOff' => null,
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
    <label
        {{ $attributes->only('disabled')->dataKey('control') }}
        {{
            TALLKit::attributesAfter($attributes, 'control:')
                ->classes(
                    '
                        tk-control-transition

                        rounded-full
                        inline-flex
                        relative
                        overflow-hidden
                        [print-color-adjust:exact]

                        bg-zinc-300
                        dark:bg-transparent

                        ring
                        ring-zinc-300
                        dark:ring-white/20

                        has-[input:not(:disabled)]:hover:opacity-80
                        dark:has-[input:not(:disabled)]:hover:opacity-80

                        has-[input:disabled]:opacity-20
                        has-[input:disabled:checked]:opacity-10
                        dark:has-[input:disabled]:opacity-30
                        dark:has-[input:disabled:checked]:opacity-20

                        has-[input[data-invalid]:disabled]:ring-red-500
                        dark:has-[input[data-invalid]:disabled]:ring-red-400
                        has-[input[data-invalid]]:ring-red-500
                        dark:has-[input[data-invalid]]:ring-red-400

                        has-[input:disabled]:cursor-not-allowed

                        has-[input:focus-visible]:outline-2
                        has-[input:focus-visible]:outline-blue-700
                        dark:has-[input:focus-visible]:outline-blue-300
                        has-[input:focus-visible]:outline-offset-0

                        has-[input:focus-visible]:ring-2
                        has-[input:focus-visible]:ring-blue-700/20
                        dark:has-[input:focus-visible]:ring-blue-300/20
                    ',
                    TALLKit::generateClassBySize(size: $size, name: 'w', values: ['8', '10', '12', '14', '16', '18', '22']),
                    TALLKit::generateClassBySize(size: $size, name: 'h', values: ['5', '6', '7', '8', '9', '10', '12']),
                    TALLKit::iconSize(size: $size),
                    match ($variant) {
                        'accent' => '
                            has-[input:checked]:bg-[var(--color-accent)]
                            has-[input:checked]:[&_span]:bg-[var(--color-accent-foreground)]
                            has-[input:checked]:[&_span]:text-[var(--color-accent-content)]
                        ',
                        'red' => 'has-[input:checked]:bg-red-600 dark:has-[input:checked]:bg-red-500',
                        'orange' => 'has-[input:checked]:bg-orange-600 dark:has-[input:checked]:bg-orange-500',
                        'amber' => 'has-[input:checked]:bg-amber-600 dark:has-[input:checked]:bg-amber-500',
                        'yellow' => 'has-[input:checked]:bg-yellow-600 dark:has-[input:checked]:bg-yellow-500',
                        'lime' => 'has-[input:checked]:bg-lime-600 dark:has-[input:checked]:bg-lime-500',
                        'green' => 'has-[input:checked]:bg-green-600 dark:has-[input:checked]:bg-green-500',
                        'emerald' => 'has-[input:checked]:bg-emerald-600 dark:has-[input:checked]:bg-emerald-500',
                        'teal' => 'has-[input:checked]:bg-teal-600 dark:has-[input:checked]:bg-teal-500',
                        'cyan' => 'has-[input:checked]:bg-cyan-600 dark:has-[input:checked]:bg-cyan-500',
                        'sky' => 'has-[input:checked]:bg-sky-600 dark:has-[input:checked]:bg-sky-500',
                        'blue' => 'has-[input:checked]:bg-blue-600 dark:has-[input:checked]:bg-blue-500',
                        'indigo' => 'has-[input:checked]:bg-indigo-600 dark:has-[input:checked]:bg-indigo-500',
                        'violet' => 'has-[input:checked]:bg-violet-600 dark:has-[input:checked]:bg-violet-500',
                        'purple' => 'has-[input:checked]:bg-purple-600 dark:has-[input:checked]:bg-purple-500',
                        'fuchsia' => 'has-[input:checked]:bg-fuchsia-600 dark:has-[input:checked]:bg-fuchsia-500',
                        'pink' => 'has-[input:checked]:bg-pink-600 dark:has-[input:checked]:bg-pink-500',
                        'rose' => 'has-[input:checked]:bg-rose-600 dark:has-[input:checked]:bg-rose-500',
                        default => 'has-[input:checked]:bg-zinc-800 dark:has-[input:checked]:bg-white dark:has-[input:checked]:[&_span]:bg-zinc-800',
                    },
                )
        }}
    >
        <input
            @checked($checked)
            type="checkbox"
            role="switch"
            {{
                $attributes
                    ->dataKey('toggle')
                    ->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'wire:model' => $wireModel,
                        'aria-label' => $label ? null : __('Toggle'),
                        'aria-describedby' => TALLKit::ariaDescribedBy($id, $description, $help, $invalid, $showError),
                        'aria-invalid' => $invalid ? 'true' : null,
                        'data-invalid' => $invalid ? true : null,
                    ])
                    ->whereDoesntStartWith(TALLKit::fieldExcludedPrefixes([
                        'icon:', 'icon-on:', 'icon-off:',
                    ]))
                    ->classes('sr-only peer')
            }}
        />
        <span
            aria-hidden="true"
            {{
                TALLKit::attributesAfter($attributes, 'icon:')
                    ->classes(
                        '
                            bg-white
                            absolute
                            top-1
                            start-1
                            rounded-full
                            shadow-sm
                            pointer-events-none

                            tk-control-transition

                            flex
                            items-center
                            justify-center

                            peer-checked:translate-x-full
                            rtl:peer-checked:-translate-x-full
                            peer-active:scale-95
                            peer-checked:shadow-md

                            [&_.checked]:hidden
                            [&_.unchecked]:inline
                            peer-checked:[&_.checked]:inline
                            peer-checked:[&_.unchecked]:hidden

                            [:where(&)]:text-zinc-800
                        ',
                        TALLKit::generateClassBySize(size: $size, name: 'size', values: ['3', '4', '5', '6', '7', '8', '10']),
                    )
            }}
        >
            @if ($iconOn)
                <tk:icon
                    :icon="$iconOn"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-on:')->classes('checked')"
                />
            @endif

            @if ($iconOff)
                <tk:icon
                    :icon="$iconOff"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-off:')->classes('unchecked')"
                />
            @endif
        </span>
    </label>
</tk:field.wrapper>
