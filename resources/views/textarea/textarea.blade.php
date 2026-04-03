@props([
    'variant' => null,
    'resize' => null,
    'rows' => null,
    'max-rows' => null,
])
@php

$maxRows = ${'max-rows'} ?? $attributes->pluck('maxRows');

@endphp
<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <tk:field.control
        :$attributes
        :$size
    >
        <textarea
            x-data="textarea(@js($maxRows))"
            data-tallkit-control
            data-tallkit-group-target
            @if (is_numeric($rows) || $rows === null) rows="{{ $rows ?? 3 }}" @endif
            @if ($name) name="{{ $name }}" @endif
            @if ($id) id="{{ $id }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
            {{ $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:',
                    'group:', 'prefix:', 'suffix:',
                    'help:', 'error:',
                    'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                    'textarea:',
                ])
                ->except('class')
                ->classes(['field-sizing-content' => $rows === 'auto'])
                ->classes(
                    match ($size) {
                        'xs' => 'p-1.5 text-xs rounded-md',
                        'sm' => 'p-1.5 text-sm rounded-md',
                        default => 'p-2 text-base rounded-lg',
                        'lg' => 'p-2 text-lg rounded-lg',
                        'xl' => 'p-2.5 text-xl rounded-lg',
                        '2xl' => 'p-2.5 text-2xl rounded-xl',
                        '3xl' => 'p-3 text-3xl rounded-xl',
                    },
                    match ($maxRows ? 'none' : $resize) {
                        'none' => 'resize-none',
                        'both' => 'resize',
                        'horizontal' => 'resize-x',
                        default => 'resize-y',
                    },
                    '
                        peer
                        block
                        w-full
                        appearance-none

                        text-zinc-700
                        disabled:text-zinc-500

                        placeholder-zinc-400
                        disabled:placeholder-zinc-400/70

                        dark:text-zinc-300
                        dark:disabled:text-zinc-400
                        dark:placeholder-zinc-400
                        dark:disabled:placeholder-zinc-500

                        disabled:opacity-75
                        dark:disabled:opacity-50
                    ',
                    match ($variant) {
                        'ghost' => 'focus:outline-none',
                        default => '
                            bg-white
                            dark:bg-white/10

                            border
                            border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                            disabled:border-b-zinc-200 dark:disabled:border-white/5
                            disabled:[&[data-invalid]]:border-red-500 disabled:dark:[&[data-invalid]]:border-red-400
                            [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                            shadow-xs
                            disabled:shadow-none
                            [&[data-invalid]]:disabled:shadow-none
                        '
                    },
                    $attributes->pluck('textarea:class')
                )
            }}
        >{{ $value }}</textarea>
    </tk:field.control>
</tk:field.wrapper>
