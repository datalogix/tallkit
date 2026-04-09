@props([
    ...TALLKit::fieldProps(),
    'align' => null,
    'checked' => null,
    'variant' => null,
    'iconOn' => null,
    'iconOff' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$checked ??= in_array($value, Arr::wrap($checked));

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
                        transition-colors
                        transition-shadow
                        transition-transform
                        duration-200
                        ease-out
                        motion-reduce:transition-none

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
                    match ($size) {
                        'xs' => 'w-8 h-5',
                        'sm' => 'w-10 h-6',
                        default => 'w-12 h-7',
                        'lg' => 'w-13 h-7.5',
                        'xl' => 'w-15 h-8.5',
                        '2xl' => 'w-16 h-9',
                        '3xl' => 'w-18 h-10',
                    },
                    TALLKit::iconSize(size: $size),
                    match ($variant) {
                        'accent' => '
                            has-[input:checked]:bg-[var(--color-accent)]
                            has-[input:checked]:[&_span]:bg-[var(--color-accent-foreground)]
                            has-[input:checked]:[&_span]:text-[var(--color-accent-content)]
                        ',
                        default => 'has-[input:checked]:bg-zinc-800 dark:has-[input:checked]:bg-white dark:has-[input:checked]:[&_span]:bg-zinc-800',
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
                    },
                )
        }}
    >
        <input
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @checked($checked)
            value="{{ $value }}"
            type="checkbox"
            role="switch"
            aria-label="{{ __('Toggle') }}"
            {{
                $attributes
                    ->dataKey('toggle')
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:',
                        'icon:', 'icon-on:', 'icon-off:',
                    ])
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

                            transition-colors
                            transition-shadow
                            transition-transform
                            duration-200
                            ease-out
                            motion-reduce:transition-none

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
                        match ($size) {
                            'xs' => 'size-4',
                            'sm' => 'size-4.5',
                            default => 'size-5',
                            'lg' => 'size-5.5',
                            'xl' => 'size-6',
                            '2xl' => 'size-6.5',
                            '3xl' => 'size-7',
                        },
                    )
            }}
        >
            @if ($iconOn)
                <tk:icon
                    :name="$iconOn"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-on:')->classes('checked')"
                />
            @endif

            @if ($iconOff)
                <tk:icon
                    :name="$iconOff"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-off:')->classes('unchecked')"
                />
            @endif
        </span>
    </label>
</tk:field.wrapper>
