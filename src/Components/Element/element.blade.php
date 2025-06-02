<tk:tooltip.wrapper :$tooltip :$attributes>
    <{{ $as }} {{ $attributes
    ->whereDoesntStartWith(['tooltip:'])
    ->when($as === 'a', fn($attrs) => $attrs->merge(['href' => $href]))
    ->when($as === 'button', fn($attrs) => $attrs->merge(['type' => $type ?? 'button']))
    }}>
        {{ $slot }}
    </{{ $as }}>
</tk:tooltip.wrapper>
