@if ($slot->hasActualContent() || $label)
    <tk:element
        :attributes="$attributes->classes(
            'pointer-events-none ms-auto font-medium text-xs rounded-sm',
            match ($variant) {
                'text' => 'text-zinc-400',
                default => 'bg-zinc-800/5 dark:bg-white/10 px-1 py-0.5 text-zinc-500 dark:text-zinc-300',
            }
        )"
        :name="$baseComponentKey()"
        :$label
    >
        {{ $slot }}
    </tk:element>
@endif
