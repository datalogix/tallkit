<tk:field
    :$label
    :$name
    :$id
    :$description
    :$help
    {{ $attributes }}
>
    <textarea
        rows="{{ $rows }}"
        @isset ($name) name="{{ $name }}" @endisset
        @isset ($id) id="{{ $id }}" @endisset
        @if ($invalid) aria-invalid="true" data-invalid @endif
        {{ $attributes
            ->classes(match($size) {
                'xs' => 'p-1 text-xs',
                'sm' => 'p-2 text-sm',
                default => 'p-3 text-base',
                'large' => 'p-4 text-lg',
                'xl' => 'p-4 text-xl',
            })
            ->classes(match($resize) {
                'none' => 'resize-none',
                'both' => 'resize',
                'horizontal' => 'resize-x',
                'vertical' => 'resize-y',
            })
            ->classes($rows === 'auto' ? 'field-sizing-content' : '')
            ->classes('
                peer
                border

                text-zinc-700
                placeholder-zinc-400

                disabled:text-zinc-500
                disabled:placeholder-zinc-400/70

                dark:text-zinc-300
                dark:disabled:text-zinc-400
                dark:placeholder-zinc-400
                dark:disabled:placeholder-zinc-500

                bg-white
                dark:bg-white/10
                dark:disabled:bg-white/[7%]

                block
                w-full

                shadow-xs disabled:shadow-none rounded-lg

                focus:ring-blue-500
                focus:border-blue-500
            ')
            ->classes($invalid ? 'border-red-500' : 'border-zinc-200 border-b-zinc-300/80 dark:border-white/10')
        }}
    >{{ $slot }}</textarea>
</tk:field>
