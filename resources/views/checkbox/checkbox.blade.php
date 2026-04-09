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
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @checked($checked)
            value="{{ $value }}"
            type="checkbox"
            {{
                $attributes
                    ->dataKey('checkbox')
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:',
                        'icon-area:', 'icon-on:', 'icon-off:',
                    ])
                    ->classes(
                        '
                            transition-colors
                            transition-shadow
                            transition-transform
                            duration-200
                            ease-out
                            motion-reduce:transition-none

                            rounded
                            peer
                            shrink-0
                            size-full
                            appearance-none
                            [print-color-adjust:exact]

                            bg-white
                            dark:bg-white/10

                            border
                            border-zinc-300
                            dark:border-white/10

                            disabled:border-zinc-200
                            dark:disabled:border-white/5

                            [&[data-invalid]:not(:focus-visible)]:border-red-500
                            dark:[&[data-invalid]:not(:focus-visible)]:border-red-400

                            disabled:[&[data-invalid]:not(:focus-visible)]:border-red-500
                            dark:disabled:[&[data-invalid]:not(:focus-visible)]:border-red-400

                            shadow-xs
                            disabled:shadow-none
                            [&[data-invalid]]:disabled:shadow-none

                            disabled:opacity-75
                            dark:disabled:opacity-50

                            focus-visible:outline-2
                            focus-visible:outline-blue-700
                            dark:focus-visible:outline-blue-300
                            focus-visible:outline-offset-0

                            focus-visible:ring-2
                            focus-visible:ring-blue-700/20
                            dark:focus-visible:ring-blue-300/20

                            disabled:cursor-not-allowed
                            disabled:resize-none

                            checked:shadow-none
                            checked:not-[data-invalid]:border-none
                            checked:disabled:opacity-30

                            enabled:hover:border-zinc-300
                            dark:enabled:hover:border-white/20
                        ',
                        match ($variant) {
                            'accent' => 'checked:bg-[var(--color-accent)]',
                            default => 'checked:bg-zinc-800 dark:checked:bg-white',
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
                            peer-checked:[&_.checked]:block
                            peer-checked:[&_.unchecked]:hidden
                        ',
                    )
            }}
        >
            <tk:icon
                :name="$iconOn ?? 'check'"
                :attributes="TALLKit::attributesAfter($attributes, 'icon-on:')->classes(
                    'size-full m-px checked scale-90',
                    match ($variant) {
                        'accent' => 'text-[var(--color-accent-foreground)]',
                        default => 'text-white dark:text-zinc-700',
                        'red' => 'text-white',
                        'orange' => 'text-white',
                        'amber' => 'text-white',
                        'yellow' => 'text-white',
                        'lime' => 'text-white',
                        'green' => 'text-white',
                        'emerald' => 'text-white',
                        'teal' => 'text-white',
                        'cyan' => 'text-white',
                        'sky' => 'text-white',
                        'blue' => 'text-white',
                        'indigo' => 'text-white',
                        'violet' => 'text-white',
                        'purple' => 'text-white',
                        'fuchsia' => 'text-white',
                        'pink' => 'text-white',
                        'rose' => 'text-white',
                    },
                )"
            />

            @if ($iconOff)
                <tk:icon
                    :name="$iconOff"
                    :attributes="TALLKit::attributesAfter($attributes, 'icon-off:')->classes('size-full m-px unchecked')"
                />
            @endif
        </div>
    </div>
</tk:field.wrapper>
