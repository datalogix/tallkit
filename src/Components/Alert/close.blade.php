<tk:element as="button" @click="dismiss" tooltip="{{ __('Close') }}"
    :attributes="$attributes->whereDoesntStartWith(['icon:'])->classes(
        'inline-flex items-center justify-center',
        'h-8 w-8',
        'rounded-lg focus:ring-2 p-2',
        'dark:hover:bg-zinc-600 transition',
        match ($type) {
            'danger' => 'focus:ring-red-300 hover:bg-red-200',
            'success' => 'focus:ring-green-300 hover:bg-green-200',
            'warning' => 'focus:ring-yellow-300 hover:bg-yellow-200',
            'info' => 'focus:ring-blue-300 hover:bg-blue-200',
            default => 'focus:ring-zinc-300 hover:bg-zinc-200',
        }
    )" aria-label="{{ __('Close') }}" data-tallkit-alert-close>
    @if ($slot->isEmpty())
        <tk:icon :attributes="$attributesAfter('icon:')" :icon="$icon ?? 'close'" data-tallkit-alert-close-icon />
    @else
        {{ $slot }}
    @endif
</tk:element>
