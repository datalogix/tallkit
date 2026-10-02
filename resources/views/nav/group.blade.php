@aware(['size'])
@props([
    'size' => null,
    'expanded' => null,
    'expandable' => null,
    'label' => null,
    'line' => null,
    'collapse' => null,
])
@php
$parts = ['trigger:', 'heading:', 'container:', 'line:'];
@endphp
@if ($expandable && $label)
    <div
        wire:ignore.self
        x-data="disclosure"
        {{ $attributes->whereDoesntStartWith($parts)->classes('group/disclosure') }}
        @if ($expanded !== false) data-open @endif
    >
        <tk:button
            :attributes="$attributes->prefixed('trigger:')->classes('w-full min-w-0 justify-start p-2.5')"
            :$size
            :$label
            content:class="truncate"
            variant="subtle"
            icon="chevron-right"
            icon:class="
                me-1.5 group-data-[open]/disclosure:rotate-90
                {{ $collapse === true || is_string($collapse) ? 'transition': '' }}
            "
        />

        <div
            {{
                $attributes->prefixed('container:')
                    ->classes('min-w-0 relative hidden group-data-[open]/disclosure:block space-y-[2px]')
                    ->when($line !== false, fn($attrs) => $attrs->classes(TALLKit::generateClassBySize(size: $size, name: 'ps', values: ['8', '9', '10', '11', '12', '13', '14'])))
                    ->when($collapse === true, fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse' => '']))
                    ->when(is_string($collapse), fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse.'.$collapse => '']))
            }}
        >
            @if ($line !== false)
                <div {{ $attributes->prefixed('line:')->classes(
                    'absolute inset-y-[3px] w-px start-0',
                    TALLKit::backgroundNeutral(variant: 'strong'),
                    TALLKit::generateClassBySize(size: $size, name: 'ms', values: ['4', '4.5', '5', '5.5', '6', '6.5', '7']),
                ) }}></div>
            @endif

            {{ $slot }}
        </div>
    </div>
@elseif ($label)
    <div {{ $attributes->whereDoesntStartWith($parts)->classes('block space-y-[2px]') }}>
        <tk:heading
            :attributes="$attributes->prefixed('heading:')->classes('p-2.5 leading-none truncate', TALLKit::textNeutral(variant: 'subtle'))"
            :size="TALLKit::adjustSize(size: $size)"
            :$label
        />

        <div {{ $attributes->prefixed('container:') }}>
            {{ $slot }}
        </div>
    </div>
@else
    <div {{ $attributes->whereDoesntStartWith($parts)->classes('block space-y-[2px]') }}>
        {{ $slot }}
    </div>
@endif
