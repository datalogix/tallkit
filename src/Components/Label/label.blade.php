@if ($slot->isNotEmpty() || $text)
    <label {{ $attributes->whereDoesntStartWith(['badge:'])
        ->classes(
            'inline-flex items-center',
            'text-sm font-medium',
            'text-zinc-800 dark:text-white'
        )
    }} data-tallkit-label>
        {{ $slot->isEmpty() ? __($text) : $slot }}

        @if ($badge)
            <span {{ $attributesAfter('badge:')
                ->classes('ms-1.5')
                ->when(
                    is_string($badge),
                    fn ($attrs) => $attrs->classes('text-zinc-800/70 text-xs bg-zinc-800/5 px-1.5 py-1 rounded-[4px] dark:bg-white/10 dark:text-zinc-300')
                )
            }} aria-hidden="true" data-tallkit-label-badge>
                {{ $badge }}
            </span>
        @endif
    </label>
@endif
