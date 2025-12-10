@aware(['collapse', 'variant'])
@props(['collapse', 'variant'])

<div
    x-data="disclosure"
    {{ $buildDataAttribute('disclosure-item') }}
    {{ $attributes->whereDoesntStartWith(['heading:', 'content:'])->classes('py-4 group/disclosure') }}
    @if ($expanded) data-open @endif
>
    <tk:button
        :attributes="$attributesAfter('heading:')->classes('w-full dark:text-white!')"
        :$disabled
        :label="$heading"
        variant="none"
        content:class="flex-1"
        :icon="$variant === 'reverse' ? 'chevron-right' : false"
        icon::class="{ 'transition': {{ $collapse === true || is_string($collapse) ? 'true' : 'false' }}, 'rotate-90': opened }"
        :icon-trailing="$variant === 'reverse' ? false : 'chevron-down'"
        icon-trailing::class="{ 'transition': {{ $collapse === true || is_string($collapse) ? 'true' : 'false' }}, 'rotate-180': opened }"
    />

    <div {{
        $attributesAfter('content:')
            ->classes('pt-2 text-sm text-zinc-500 dark:text-zinc-300')
            ->merge(['x-show' => 'opened'])
            ->merge($collapse === true ? ['x-collapse' => ''] : [])
            ->merge(is_string($collapse) ? ['x-collapse.'.$collapse => ''] : [])
    }}>
        {{ $slot }}
    </div>
</div>
