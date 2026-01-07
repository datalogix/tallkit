<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :$label
>
    <div {{ $attributes->only('class')->classes('w-full relative block group/textarea')
        ->when(in_livewire() && $loading, fn($attrs) => $attrs->merge([
            'wire:loading.class' => 'field-loading',
            'wire:target' => $wireTarget
        ]))
    }}>
        <textarea
            x-data="textarea(@js($maxRows))"
            {{ $buildDataAttribute('control') }}
            {{ $buildDataAttribute('group-target') }}
            @if (is_numeric($rows) || $rows === null) rows="{{ $rows ?? 3 }}" @endif
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
            {{ $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:', 'append:', 'loading:',
                    'textarea:',
                ])
                ->except('class')
                ->classes(['field-sizing-content' => $rows === 'auto'])
                ->classes(
                    match ($size) {
                        'xs' => 'p-1.5 text-xs rounded-md [.field-loading_&]:pe-[32px]',
                        'sm' => 'p-1.5 text-sm rounded-md [.field-loading_&]:pe-[36px]',
                        default => 'p-2 text-base rounded-lg [.field-loading_&]:pe-[40px]',
                        'lg' => 'p-2 text-lg rounded-lg [.field-loading_&]:pe-[48px]',
                        'xl' => 'p-2.5 text-xl rounded-lg [.field-loading_&]:pe-[56px]',
                        '2xl' => 'p-2.5 text-2xl rounded-xl [.field-loading_&]:pe-[64px]',
                        '3xl' => 'p-3 text-3xl rounded-xl [.field-loading_&]:pe-[72px]',
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
                    match($variant) {
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

        @if ($loading)
            <div
                {{
                    $attributesAfter('append:')->classes(
                        'absolute text-zinc-400',
                        match ($size) {
                            'xs' => 'top-1.5 right-1.5',
                            'sm' => 'top-1.5 right-1.5',
                            default => 'top-2 right-2',
                            'lg' => 'top-2 right-2',
                            'xl' => 'top-2.5 right-2.5',
                            '2xl' => 'top-2.5 right-2.5',
                            '3xl' => 'top-3 right-3',
                        }
                    )
                }}
            >
                <tk:loading
                    :attributes="$attributesAfter('loading:')->classes('hidden [.field-loading_&]:block')"
                    :size="$adjustSize()"
                />
            </div>
        @endif
    </div>
</tk:field.wrapper>
