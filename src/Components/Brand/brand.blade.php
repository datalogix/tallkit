<tk:element :attributes="$attributes->whereDoesntStartWith(['logo:', 'image:', 'name:'])
        ->classes('py-2 flex items-center', $name ? 'gap-2' : '')
        " :$href data-tallkit-brand>
    @if ($isSlot($logo))
        <div {{ $logo->attributes->classes('flex items-center justify-center [:where(&)]:min-w-6 [:where(&)]:rounded-sm overflow-hidden shrink-0') }}>
            {{ $logo }}
        </div>
    @else
        <div {{ $attributesAfter('logo:')
            ->classes('flex items-center justify-center rounded-sm overflow-hidden shrink-0')
            ->classes(match ($size) {
                'sm' => 'h-8',
                default => 'h-12',
                'lg' => 'h-18',
                'xl' => 'h-26',
                '2xl' => 'h-40',
            }) }}  data-tallkit-brand-logo>
            @if ($logo)
                <img src="{{ $logo }}" @if ($alt) alt="{{ $alt }}" @endif {{ $attributesAfter('image:')->classes('h-full') }}
                    data-tallkit-brand-image />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif

    @if ($name)
        <div {{ $attributesAfter('name:')
            ->classes('font-medium truncate [:where(&)]:text-zinc-800 dark:[:where(&)]:text-zinc-100')
            ->classes(match ($size) {
                'sm' => 'text-sm',
                default => 'text-base',
                'lg' => 'text-lg',
                'xl' => 'text-2xl',
                '2xl' => 'text-3xl',
            }) }} data-tallkit-brand-name>
            {{ $name }}
        </div>
    @endif
</tk:element>
