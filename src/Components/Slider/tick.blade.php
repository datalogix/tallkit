<tk:text
    as="span"
    :attributes="$attributes->classes('min-w-5 flex items-center justify-center pointer-events-none')"
    :$label
    :$size
>
    @if ($slot->hasActualContent() || $label)
        {{ $slot }}
    @else
        <span class="h-1.5 w-px bg-black/25 dark:bg-white/25"></span>
    @endif
</tk:text>
