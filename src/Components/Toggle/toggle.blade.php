<tk:field.wrapper :$attributes variant="inline">
    <label
        {{ $buildDataAttribute('control') }}
        {{ $attributes->only('disabled') }}
        {{ $attributesAfter('control:')->classes('flex') }}
    >
        <input {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'control:',
                ])->classes('sr-only peer')
            }}
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @checked($checked)
            value="{{ $value }}"
            type="checkbox"
            role="switch"
            aria-label="{{ __('Toggle') }}"
        />
        <span
            aria-hidden="true"
            {{
                $attributesAfter('icon:')->classes(
                    '
                    relative
                    rounded-full

                    bg-zinc-800/15 dark:bg-zinc-700
                    [print-color-adjust:exact]

                    peer-disabled:opacity-50
                    peer-checked:after:translate-x-full
                    rtl:peer-checked:after:-translate-x-full
                    peer-checked:border-0


                    after:absolute after:bg-white
                    after:rounded-full after:transition-all
                    after:top-1 after:start-1

                    dark:bg-transparent
                    dark:border
                    dark:border-white/20
                    ',
                    match ($size) {
                        'xs' => 'w-8 h-5 after:size-3',
                        'sm' => 'w-10 h-6 after:size-4',
                        default => 'w-12 h-7 after:size-5',
                        'lg' => 'w-13 h-7.5 after:size-5.5',
                        'xl' => 'w-15 h-8.5 after:size-6.5',
                        '2xl' => 'w-16 h-9 after:size-7',
                        '3xl' => 'w-18 h-10 after:size-8',
                    },
                    match ($variant) {
                        'accent' => ' peer-checked:bg-[var(--color-accent)] peer-checked:after:bg-[var(--color-accent-foreground)]',
                        default => 'peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500',
                        'red' => 'peer-checked:bg-red-600 dark:peer-checked:bg-red-500',
                        'orange' => 'peer-checked:bg-orange-600 dark:peer-checked:bg-orange-500',
                        'amber' => 'peer-checked:bg-amber-600 dark:peer-checked:bg-amber-500',
                        'yellow' => 'peer-checked:bg-yellow-600 dark:peer-checked:bg-yellow-500',
                        'lime' => 'peer-checked:bg-lime-600 dark:peer-checked:bg-lime-500',
                        'green' => 'peer-checked:bg-green-600 dark:peer-checked:bg-green-500',
                        'emerald' => 'peer-checked:bg-emerald-600 dark:peer-checked:bg-emerald-500',
                        'teal' => 'peer-checked:bg-teal-600 dark:peer-checked:bg-teal-500',
                        'cyan' => 'peer-checked:bg-cyan-600 dark:peer-checked:bg-cyan-500',
                        'sky' => 'peer-checked:bg-sky-600 dark:peer-checked:bg-sky-500',
                        'blue' => 'peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500',
                        'indigo' => 'peer-checked:bg-indigo-600 dark:peer-checked:bg-indigo-500',
                        'violet' => 'peer-checked:bg-violet-600 dark:peer-checked:bg-violet-500',
                        'purple' => 'peer-checked:bg-purple-600 dark:peer-checked:bg-purple-500',
                        'fuchsia' => 'peer-checked:bg-fuchsia-600 dark:peer-checked:bg-fuchsia-500',
                        'pink' => 'peer-checked:bg-pink-600 dark:peer-checked:bg-pink-500',
                        'rose' => 'peer-checked:bg-rose-600 dark:peer-checked:bg-rose-500',
                    },
                )
            }}></span>
    </label>

    @if ($slot->isNotEmpty())
        <x-slot:label>
            {{ $slot }}
        </x-slot:label>
    @endif
</tk:field.wrapper>
