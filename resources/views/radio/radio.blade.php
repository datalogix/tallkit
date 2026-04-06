@props([
    // field
    'id' => null,
    'size' => null,
    'align' => null,
    'labelAppend' => null,
    'labelPrepend' => null,
    'description' => null,
    'help' => null,
    'badge' => null,
    'info' => null,
    'prefix' => null,
    'suffix' => null,
    'showError' => null,

    // radio
    'value' => null,
    'checked' => null,
    'variant' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::fieldAttributes($attributes);
$checked = in_array($value, Arr::wrap($checked));

@endphp
<tk:field.wrapper
    :$attributes
    variant="inline"
    :$align
    :$name
    :$id
    :label="$slot->isEmpty() ? $label : $slot"
    :$labelAppend
    :$labelPrepend
    :$description
    :$help
    :$badge
    :$info
    :$prefix
    :$suffix
    :$size
    :$showError
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
            type="radio"
            {{
                $attributes
                    ->merge(['wire:model' => $wireModel])
                    ->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:',
                        'icon-area:',
                    ])
                    ->classes(
                        '
                            size-full
                            rounded-full

                            peer
                            appearance-none
                            shrink-0
                            transition-all

                            bg-white dark:bg-white/10
                            [print-color-adjust:exact]

                            border
                            border-zinc-300 dark:border-white/10
                            disabled:border-zinc-200 dark:disabled:border-white/5
                            [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                            shadow-xs
                            disabled:opacity-75
                            disabled:checked:opacity-50
                            disabled:shadow-none
                            checked:shadow-none
                            checked:not-[data-invalid]:border-none
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
                            absolute transition pointer-events-none size-full
                            flex justify-center items-center
                            opacity-0 peer-checked:opacity-100
                        '
                    )
            }}
        >
            <div
                {{
                    TALLKit::attributesAfter($attributes, 'icon:')->classes(
                        'rounded-full size-1/2',
                        match ($variant) {
                            'accent' => 'bg-[var(--color-accent-foreground)]',
                            default => 'bg-white dark:bg-zinc-700',
                            'red' => 'bg-white',
                            'orange' => 'bg-white',
                            'amber' => 'bg-white',
                            'yellow' => 'bg-white',
                            'lime' => 'bg-white',
                            'green' => 'bg-white',
                            'emerald' => 'bg-white',
                            'teal' => 'bg-white',
                            'cyan' => 'bg-white',
                            'sky' => 'bg-white',
                            'blue' => 'bg-white',
                            'indigo' => 'bg-white',
                            'violet' => 'bg-white',
                            'purple' => 'bg-white',
                            'fuchsia' => 'bg-white',
                            'pink' => 'bg-white',
                            'rose' => 'bg-white',
                        },
                    )
                }}
            ></div>
        </div>
    </div>
</tk:field.wrapper>
