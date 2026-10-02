@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'name' => null,
    'logo' => null,
    'logoDark' => null,
    'alt' => null,
    'href' => null,
])
@php

$logo ??= TALLKit::findImage('logo', exts: ['svg', 'webp', 'png', 'jpg', 'jpeg']);
$logoDark ??= TALLKit::findImage('logo-dark', exts: ['svg', 'webp', 'png', 'jpg', 'jpeg']);
$name = $name === true ? config('app.name') : $name;
$alt ??= $name ? '' : config('app.name');
$href ??= route_detect('home');

@endphp
<tk:element
    kind="brand"
    as="div"
    :$href
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['logo:', 'image:', 'image-dark:', 'name:'])
        ->classes(
            'justify-center',
            TALLKit::paddingBlock(size: $size),
            TALLKit::gap(size: $size)
        )
    "
>
    @if (TALLKit::isSlot(slot: $logo))
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

                @if (TALLKit::isSlot(slot: $logoDark))
                    <span class="hidden dark:block">{{ $logoDark }}</span>
                @else
                    <img
                        src="{{ $logoDark }}"
                        {{
                            $attributes->prefixed('image-dark:')
                                ->classes('hidden dark:block h-full')
                                ->mergeDefined(['alt' => $alt])
                        }}
                    />
                @endif
            @else
                {{ $logo }}
            @endif
        </div>
    @elseif ($logo || $logoDark || $slot->isNotEmpty())
        <div {{
            $attributes->prefixed('logo:')
                ->classes(
                    '
                        flex items-center justify-center
                        overflow-hidden shrink-0
                    ',
                    TALLKit::height(size: $size, mode: 'large'),
                    TALLKit::roundedSize(size: $size, mode: 'small'),
                )
        }}>
            @if (TALLKit::isSlot(slot: $logoDark))
                <span class="hidden dark:block">{{ $logoDark }}</span>
            @elseif ($logoDark)
                <img
                    src="{{ $logoDark }}"
                    {{
                        $attributes->prefixed('image-dark:')
                            ->classes('hidden dark:block h-full')
                            ->mergeDefined(['alt' => $alt])
                    }}
                />
            @endif

            @if ($logo)
                <img
                    src="{{ $logo }}"
                    {{
                        $attributes->prefixed('image:')
                            ->classes(['block dark:hidden' => !!$logoDark, 'h-full'])
                            ->mergeDefined(['alt' => $alt])
                    }}
                />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif

    <tk:heading
        :attributes="$attributes->prefixed('name:')->classes('truncate')"
        :$size
        :label="$name"
        as="span"
    />
</tk:element>
