@props([
    'size' => null,
    'name' => null,
    'logo' => null,
    'logoDark' => null,
    'alt' => null,
    'href' => null,
])
@php

$logo ??= find_image('logo');
$logoDark ??= find_image('logo-dark');
$name = $name === true ? config('app.name') : $name;
$alt ??= $name ? '' : config('app.name');
$href ??= route_detect('home');

@endphp
<tk:element
    name="brand"
    as="div"
    :$href
    :attributes="$attributes->whereDoesntStartWith(['logo:', 'image:', 'image-dark:', 'name:'])
        ->classes(
            'justify-center',
            TALLKit::paddingBlock(size: $size),
            TALLKit::gap(size: $size)
        )
    "
>
    @if (TALLKit::isSlot($logo))
        <div {{
            $logo->attributes->classes(
                '
                    flex items-center justify-center
                    overflow-hidden shrink-0
                    [:where(&)]:min-w-6
                ',
                TALLKit::roundedSize(size: $size, mode: 'small'),
            )
        }}>
            @if ($logoDark)
                <span class="block dark:hidden">{{ $logo }}</span>

                @if (TALLKit::isSlot($logoDark))
                    <span class="hidden dark:block">{{ $logoDark }}</span>
                @else
                    <img
                        src="{{ $logoDark }}"
                        {{
                            TALLKit::attributesAfter($attributes, 'image-dark:')
                                ->classes('hidden dark:block h-full')
                                ->merge($alt !== null ? ['alt' => $alt] : [])
                        }}
                    />
                @endif
            @else
                {{ $logo }}
            @endif
        </div>
    @elseif ($logo || $logoDark || $slot->isNotEmpty())
        <div {{
            TALLKit::attributesAfter($attributes, 'logo:')
                ->classes(
                    '
                        flex items-center justify-center
                        overflow-hidden shrink-0
                    ',
                    TALLKit::height(size: $size, mode: 'large'),
                    TALLKit::roundedSize(size: $size, mode: 'small'),
                )
        }}>
            @if (TALLKit::isSlot($logoDark))
                <span class="hidden dark:block">{{ $logoDark }}</span>
            @elseif ($logoDark)
                <img
                    src="{{ $logoDark }}"
                    {{
                        TALLKit::attributesAfter($attributes, 'image-dark:')
                            ->classes('hidden dark:block h-full')
                            ->merge($alt !== null ? ['alt' => $alt] : [])
                    }}
                />
            @endif

            @if ($logo)
                <img
                    src="{{ $logo }}"
                    {{
                        TALLKit::attributesAfter($attributes, 'image:')
                            ->classes(['block dark:hidden' => !!$logoDark, 'h-full'])
                            ->merge($alt !== null ? ['alt' => $alt] : [])
                    }}
                />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif

    <tk:heading
        :attributes="TALLKit::attributesAfter($attributes, 'name:')->classes('truncate')"
        :$size
        :label="$name"
        as="span"
    />
</tk:element>
