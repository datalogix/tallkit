@aware(['size', 'collapse', 'variant'])
@props(['size', 'collapse', 'variant'])

<div
    x-data="disclosure"
    {{ $buildDataAttribute('disclosure-item') }}
    {{ $attributes->whereDoesntStartWith(['heading:', 'content:'])->classes('py-4 group/disclosure') }}
    @if ($expanded) data-open @endif
>
    <tk:button
        :attributes="$attributesAfter('heading:')->classes('w-full dark:text-white!')"
        :$size
        :$disabled
        :label="$heading"
        variant="none"
        content:class="flex-1"
        :icon="$variant === 'reverse' ? 'chevron-right' : false"
        icon::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-90': opened }"
        :icon-trailing="$variant === 'reverse' ? false : 'chevron-down'"
        icon-trailing::class="{ 'transition': {{ $collapse !== false ? 'true' : 'false' }}, 'rotate-180': opened }"
    />

    <div {{
        $attributesAfter('content:')
            ->classes(
                'pt-2 text-zinc-500 dark:text-zinc-400',
                $fontSize(size: $size)
            )
            ->merge(['x-show' => 'opened'])
            ->merge($collapse === false ? [] : ['x-collapse' => ''])
            ->merge(is_string($collapse) ? ['x-collapse.'.$collapse => ''] : [])
    }}>
        {{ $slot }}
    </div>
</div>
