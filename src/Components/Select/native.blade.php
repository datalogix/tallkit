<tk:field
    :$label
    :$name
    :$id
    :$description
    :$help
    {{ $attributes }}
>
    <select
        data-tallkit-select-native
        @isset ($name) name="{{ $name }}" @endisset
        @isset ($id) id="{{ $id }}" @endisset
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if (is_numeric($size)) size="{{ $size }}" @endif
        {{ $attributes
            ->classes(match($size) {
                default => 'h-10 py-2 text-base sm:text-sm leading-none rounded-lg',
                'xl' => 'h-14 py-2 text-xl sm:text-sm leading-none rounded-xl',
                'lg' => 'h-12 py-2 text-lg sm:text-sm leading-none rounded-lg',
                'base' => 'h-10 py-2 text-base sm:text-sm leading-none rounded-lg',
                'sm' => 'h-8 py-1.5 text-sm leading-none rounded-md',
                'xs' => 'h-6 text-xs leading-none rounded-md',
            })
            ->classes('
                appearance-none

                peer
                border border-zinc-300

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
                dark:disabled:bg-white/[9%]

                ps-3 pe-10
                block
                block
                w-full
                rounded-lg

                shadow-xs disabled:shadow-none

                focus:outline-hidden
                focus:ring-2
                focus:ring-accent
                focus:ring-offset-2
                focus:ring-offset-accent-foreground

                has-[option.placeholder:checked]:text-zinc-400 dark:has-[option.placeholder:checked]:text-zinc-400
                dark:[&>option]:bg-zinc-700 dark:[&>option]:text-white

                bg-size-[1.5em_1.5em]
                bg-no-repeat
                [print-color-adjust:exact]

                bg-position-[right_.5rem_center]
                rtl:bg-position-[left_.5rem_center]
            ')
            ->classes($invalid ? 'border-red-500' : 'border-zinc-200 border-b-zinc-300/80 dark:border-white/10')
        }}
    >
        @if($placeholder)
            <option value="" disabled selected class="placeholder">{{ __($placeholder) }}</option>
        @endif

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            @foreach($options as $key => $value)
                <option value="{{ data_get($value, 'id', $key) }}">
                    {{ data_get($value, 'name', $key) }}
                </option>
            @endforeach
        @endif
    </select>
</tk:field>
