@php $invalid ??= $name && $errors->has($name); @endphp

<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <textarea
        {{ $buildDataAttribute('control') }}
        {{ $buildDataAttribute('group-target') }}
        @if (is_numeric($rows) || $rows === null) rows="{{ $rows ?? 3 }}" @endif
        @isset ($name) name="{{ $name }}" @endisset
        @isset ($id) id="{{ $id }}" @endisset
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
        {{ $attributes->whereDoesntStartWith([
                'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                'group:', 'prefix:', 'suffix:',
            ])
            ->classes(match ($size) {
                'xs' => 'p-2 text-xs rounded-md',
                'sm' => 'p-2 text-sm rounded-md',
                default => 'p-3 text-base rounded-lg',
                'lg' => 'p-3.5 text-lg rounded-lg',
                'xl' => 'p-4 text-xl rounded-lg',
                '2xl' => 'p-4.5 text-2xl rounded-xl',
                '3xl' => 'p-5 text-3xl rounded-xl',
            })
            ->classes(match ($resize) {
                'none' => 'resize-none',
                'both' => 'resize',
                'horizontal' => 'resize-x',
                default => 'resize-y',
            })
            ->classes(['field-sizing-content' => $rows === 'auto'])
            ->classes('
                peer
                block
                w-full
                appearance-none

                shadow-xs
                disabled:shadow-none

                border
                border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                disabled:border-b-zinc-200 dark:disabled:border-white/5
                [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                text-zinc-700
                disabled:text-zinc-500

                placeholder-zinc-400
                disabled:placeholder-zinc-400/70

                dark:text-zinc-300
                dark:disabled:text-zinc-400
                dark:placeholder-zinc-400
                dark:disabled:placeholder-zinc-500

                bg-white
                dark:bg-white/10
                disabled:opacity-75
                dark:disabled:opacity-50

                focus:ring-blue-500
                focus:border-blue-500
            ')
        }}
    >{{ $value }}</textarea>
</tk:field.wrapper>
