<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :label="$slot->isEmpty() ? $label : $slot"
    variant="inline"
>
    <div
        {{ $buildDataAttribute('control') }}
        {{ $attributes->only('disabled') }}
        {{ $attributesAfter('control:')->classes(
            'flex mt-px outline-offset-2 relative',
            match($size) {
                'xs' => 'size-3.5',
                'sm' => 'size-4',
                default => 'size-5',
                'lg' => 'size-6',
                'xl' => 'size-8',
                '2xl' => 'size-10',
                '3xl' => 'size-12',
            })
        }}
    >
        <input {{
            $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'control:', 'icon-area:', 'icon-on:', 'icon-off:',
                ])
                ->classes(
                    '
                    size-full
                    rounded

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
                    match($variant) {
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
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @checked($checked)
            value="{{ $value }}"
            type="checkbox"
        />

        <div {{ $attributesAfter('icon-area:')
            ->classes('
                absolute transition pointer-events-none size-full flex justify-center items-center
                [&_.checked]:hidden
                [&_.unchecked]:block
                peer-checked:[&_.checked]:block
                peer-checked:[&_.unchecked]:hidden
            ')
        }}>
            <tk:icon
                :name="$iconOn ?? 'check'"
                :attributes="$attributesAfter('icon-on:')->classes(
                    'size-full m-px checked',
                    match($variant) {
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
                    :attributes="$attributesAfter('icon-off:')->classes('size-full m-px unchecked')"
                />
            @endif
        </div>
    </div>
</tk:field.wrapper>
