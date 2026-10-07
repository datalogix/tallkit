@aware(['size', 'collapse', 'reversed', 'border', 'level'])
@props([
    'size' => null,
    'collapse' => null,
    'reversed' => null,
    'border' => null,
    'expanded' => null,
    'disabled' => null,
    'label' => null,
    'level' => 2,
])
<div
    wire:ignore.self
    x-data="disclosure"
    {{
        $attributes
            ->dataKey('disclosure-item')
            ->whereDoesntStartWith(['trigger:', 'content:'])
            ->classes('group/disclosure')
            ->merge(['data-open' => $expanded])
    }}
>
    @if ($label !== null)
        <tk:accordion.heading
            :attributes="$attributes->prefixed('trigger:')"
            :$size
            :$collapse
            :$reversed
            :$border
            :$disabled
            :$label
            :$level
        />

        <tk:accordion.content
            :attributes="$attributes->prefixed('content:')"
            :$size
            :$collapse
            :$border
        >
            {{ $slot }}
        </tk:accordion.content>
    @else
        {{ $slot }}
    @endif
</div>
