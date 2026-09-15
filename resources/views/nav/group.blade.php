@aware(['size'])
@props([
    'size' => null,
    'expanded' => null,
    'expandable' => null,
    'heading' => null,
    'line' => null,
    'collapse' => null,
])
@if ($expandable && $heading)
    <div
        x-data="disclosure"
        {{ $attributes->whereDoesntStartWith(['heading:', 'container:', 'line:'])->classes('group/disclosure') }}
        @if ($expanded !== false) data-open @endif
    >
        <tk:button
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')->classes('w-full min-w-0 justify-start p-2.5')"
            :$size
            :label="$heading"
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
                TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')
                    ->classes('min-w-0 relative hidden group-data-[open]/disclosure:block space-y-[2px]')
                    ->when($line !== false, fn($attrs) => $attrs->classes(TALLKit::generateClassBySize(size: $size, name: 'ps', values: ['8', '9', '10', '11', '12', '13', '14'])))
                    ->when($collapse === true, fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse' => '']))
                    ->when(is_string($collapse), fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse.'.$collapse => '']))
            }}
        >
            @if ($line !== false)
                <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'line:')->classes(
                    'absolute inset-y-[3px] w-px start-0',
                    TALLKit::backgroundNeutral(variant: 'strong'),
                    TALLKit::generateClassBySize(size: $size, name: 'ms', values: ['4', '4.5', '5', '5.5', '6', '6.5', '7']),
                ) }}></div>
            @endif

            {{ $slot }}
        </div>
    </div>
@elseif ($heading)
    <div {{ $attributes->whereDoesntStartWith(['heading:', 'container:'])->classes('block space-y-[2px]') }}>
        <tk:heading
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'heading:')->classes('p-2.5 leading-none truncate', TALLKit::textNeutral(variant: 'subtle'))"
            :size="TALLKit::adjustSize(size: $size)"
            :label="$heading"
        />

        <div {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:') }}>
            {{ $slot }}
        </div>
    </div>
@else
    <div {{ $attributes->whereDoesntStartWith(['heading:'])->classes('block space-y-[2px]') }}>
        {{ $slot }}
    </div>
@endif
