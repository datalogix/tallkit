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
    @if ($level)<div role="heading" aria-level="{{ $level }}">@endif
    <tk:button
        :attributes="$attributes->prefixed('trigger:')->classes(
            TALLKit::paddingInline(size: $border ? $size : 'none', mode: 'largest'),
            TALLKit::paddingBlock(size: $size, mode: 'largest'),
            'w-full [&_[data-tallkit-icon]]:ml-auto',
        )"
        :$size
        :$disabled
        :$label
        variant="none"
        content:class="flex-1 justify-start"
        :icon="$reversed ? 'chevron-right' : false"
        icon:class="rtl:-scale-x-100"
        icon::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-90 rtl:-rotate-90': opened }"
        :iconTrailing="$reversed ? false : 'chevron-down'"
        icon-trailing::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-180': opened }"
    />
    @if ($level)</div>@endif

    <div
        x-cloak
        {{
            $attributes->prefixed('content:')
                ->classes(
                    TALLKit::fontSize(size: $size),
                    TALLKit::paddingInline(size: $border ? $size : 'none', mode: 'largest'),
                    TALLKit::paddingBlock(size: $size, mode: 'largest'),
                    'pt-0!',
                )
                ->merge(['x-show' => 'opened'])
                ->merge(
                    match (true) {
                        $collapse === false => [],
                        is_string($collapse) => ['x-collapse.'.$collapse => ''],
                        default => ['x-collapse' => ''],
                    }
                )
        }}
    >
        {{ $slot }}
    </div>
</div>
