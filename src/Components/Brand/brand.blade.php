<tk:element
    as="div"
    :name="$baseComponentKey()"
    :$href
    :attributes="$attributes->whereDoesntStartWith(['logo:', 'image:', 'image-dark:', 'name:'])
        ->classes('py-2 justify-center gap-4')
    "
>
    @if ($isSlot($logo))
        <div {{
            $logo->attributes->classes(
                '
                    flex items-center justify-center
                    overflow-hidden shrink-0
                    [:where(&)]:min-w-6
                ',
                $roundedSize(size: $size, mode: 'small'),
            )
        }}>
            {{ $logo }}
        </div>
    @elseif ($logo || $logoDark || $slot->isNotEmpty())
        <div {{
            $attributesAfter('logo:')
                ->classes(
                    '
                        flex items-center justify-center
                        overflow-hidden shrink-0
                    ',
                    $height(size: $size, mode: 'large'),
                    $roundedSize(size: $size, mode: 'small'),
                )
        }}>
            @if ($isSlot($logoDark))
                {{ $logoDark }}
            @elseif ($logoDark)
                <img
                    src="{{ $logoDark }}"
                    {{
                        $attributesAfter('image-dark:')
                            ->classes('hidden dark:block h-full')
                            ->merge($alt ? ['alt' => $alt] : [])
                    }}
                />
            @endif

            @if ($logo)
                <img
                    src="{{ $logo }}"
                    {{
                        $attributesAfter('image:')
                            ->classes(['block dark:hidden' => !!$logoDark, 'h-full'])
                            ->merge($alt ? ['alt' => $alt] : [])
                    }}
                />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif

    <tk:heading
        :attributes="$attributesAfter('name:')->classes('truncate')"
        :label="$name"
        :$size
    />
</tk:element>
