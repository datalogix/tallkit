<tk:element
    as="div"
    :name="$baseComponentKey()"
    :$href
    :attributes="$attributes->whereDoesntStartWith(['logo:', 'image:', 'name:'])->classes('py-2 inline-flex justify-center gap-2')"
>
    @if ($isSlot($logo))
        <div {{ $logo->attributes->classes('flex items-center justify-center [:where(&)]:min-w-6 [:where(&)]:rounded-sm overflow-hidden shrink-0') }}>
            {{ $logo }}
        </div>
    @else
        <div {{
            $attributesAfter('logo:')
                ->classes('flex items-center justify-center rounded-sm overflow-hidden shrink-0')
                ->classes(match ($size) {
                    'xs' => 'h-6',
                    'sm' => 'h-8',
                    default => 'h-12',
                    'lg' => 'h-18',
                    'xl' => 'h-26',
                    '2xl' => 'h-36',
                    '3xl' => 'h-48',
                })
        }}>
            @if ($logo)
                <img
                    src="{{ $logo }}"
                    {{ $attributesAfter('image:')->classes('h-full')->merge($alt ? ['alt' => $alt] : []) }}
                />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif

    @if ($name)
        <div
            {{
                $attributesAfter('name:')
                    ->classes('font-medium truncate [:where(&)]:text-zinc-800 dark:[:where(&)]:text-zinc-100')
                    ->classes(match ($size) {
                        'xs' => 'text-sm',
                        'sm' => 'text-base',
                        default => 'text-lg',
                        'lg' => 'text-2xl',
                        'xl' => 'text-3xl',
                        '2xl' => 'text-4xl',
                        '3xl' => 'text-5xl',
                    })
            }}
        >
            {{ $name }}
        </div>
    @endif
</tk:element>
