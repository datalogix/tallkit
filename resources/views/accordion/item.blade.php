@aware(['size', 'collapse', 'reversed', 'border', 'disabled', 'level', 'region'])
@props([
    'size' => null,
    'collapse' => null,
    'reversed' => null,
    'border' => null,
    'expanded' => null,
    'disabled' => null,
    'label' => null,
    'level' => 2,
    'region' => null,
    'name' => null,
])
<div
    wire:ignore.self
    x-data="disclosure"
    {{
        $attributes
            ->dataKey('disclosure-item')
            ->whereDoesntStartWith(['trigger:', 'content:'])
            ->classes('group/disclosure')
            ->merge([
                'data-open' => $expanded,
                'data-name' => $name
            ])
    }}
>
    @if (has_content($label))
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
            :$region
        >
            {{ $slot }}
        </tk:accordion.content>
    @else
        {{ $slot }}
    @endif
</div>
