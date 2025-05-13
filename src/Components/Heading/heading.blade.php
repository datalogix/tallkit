    <{{ $level > 0 && $level < 5 ? 'h'.$level : 'div' }} {{ $attributes
        ->classes('font-medium [:where(&)]:text-gray-800 [:where(&)]:dark:text-white')
        ->classes(match ($size) {
            'xl' => 'text-2xl',
            'lg' => 'text-lg',
            default => 'text-base',
            'sm' => 'text-sm',
        })
    }}>
        {{ $slot }}
    </{{ $level > 0 && $level < 5 ? 'h'.$level : 'div' }}>
