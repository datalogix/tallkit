<tk:field.wrapper
    :$attributes
    :$name
    :$id
    :label="$slot->isEmpty() ? $label : $slot"
    variant="inline"
>
    <label
        {{ $buildDataAttribute('control') }}
        {{ $attributes->only('disabled') }}
        {{ $attributesAfter('control:')->classes('
                inline-flex
                relative
                rounded-full

                [print-color-adjust:exact]

                bg-zinc-300
                dark:bg-transparent
                dark:border
                dark:border-white/20

                has-[input:disabled]:opacity-50
                has-[input:checked:not([data-invalid])]:border-0

                has-[input[data-invalid]]:border-red-400 dark:has-[input[data-invalid]]:border-red-500
            ',
             match ($size) {
                'xs' => 'w-8 h-5 [&_span]:size-3 [&_[data-tallkit-icon]]:size-2.5',
                'sm' => 'w-10 h-6 [&_span]:size-4 [&_[data-tallkit-icon]]:size-3',
                default => 'w-12 h-7 [&_span]:size-5 [&_[data-tallkit-icon]]:size-4',
                'lg' => 'w-13 h-7.5 [&_span]:size-5.5 [&_[data-tallkit-icon]]:size-4.5',
                'xl' => 'w-15 h-8.5 [&_span]:size-6.5 [&_[data-tallkit-icon]]:size-5',
                '2xl' => 'w-16 h-9 [&_span]:size-7 [&_[data-tallkit-icon]]:size-5.5',
                '3xl' => 'w-18 h-10 [&_span]:size-8 [&_[data-tallkit-icon]]:size-6.5',
            },
            match ($variant) {
                'accent' => '
                    has-[input:checked]:bg-[var(--color-accent)]
                    has-[input:checked]:[&_span]:bg-[var(--color-accent-foreground)]
                    has-[input:checked]:[&_span]:text-[var(--color-accent-content)]
                ',
                default => 'has-[input:checked]:bg-zinc-600 dark:has-[input:checked]:bg-zinc-500',
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
        ) }}
    >
        <input {{
                $attributes->whereDoesntStartWith([
                    'field:', 'label:', 'info:', 'badge:', 'description:', 'help:', 'error:',
                    'group:', 'prefix:', 'suffix:',
                    'control:', 'icon:', 'icon-on:', 'icon-off:'
                ])->classes('sr-only peer')
            }}
            @isset ($name) name="{{ $name }}" @endisset
            @isset ($id) id="{{ $id }}" @endisset
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @checked($checked)
            value="{{ $value }}"
            type="checkbox"
            role="switch"
            aria-label="{{ __('Toggle') }}"
        />
        <span
            aria-hidden="true"
            {{
                $attributesAfter('icon:')->classes('
                    bg-white
                    absolute top-1 start-1
                    rounded-full transition-all

                    flex
                    items-center
                    justify-center

                    peer-checked:translate-x-full
                    rtl:peer-checked:-translate-x-full

                    [&_.checked]:hidden
                    [&_.unchecked]:inline
                    peer-checked:[&_.checked]:inline
                    peer-checked:[&_.unchecked]:hidden

                    [:where(&)]:text-zinc-800
                    ',
                )
            }}
        >
            @if ($iconOn)
                <tk:icon
                    :name="$iconOn"
                    :attributes="$attributesAfter('icon-on:')->classes('checked')"
                />
            @endif

            @if ($iconOff)
                <tk:icon
                    :name="$iconOff"
                    :attributes="$attributesAfter('icon-off:')->classes('unchecked')"
                />
            @endif
        </span>
    </label>
</tk:field.wrapper>
