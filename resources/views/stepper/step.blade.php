@aware(['size'])
@props([
    'index' => null,
    'total' => null,
    'size' => null,
    'status' => null,
    'color' => null,
    'icon' => null,
    'iconCompleted' => null,
    'iconActive' => null,
])
@php

$label = $attributes->get('label');
$ariaLabel = trim(
    ($index && $total ? __('Step :current of :total', ['current' => $index, 'total' => $total]) : '')
    .($label ? ': '.$label : '')
);

@endphp
<tk:element
    :attributes="$attributes->whereDoesntStartWith(['icon:', 'bullet:'])
        ->classes(
            'flex flex-col items-center flex-1 text-center gap-2',
            TALLKit::fontSize(size: $size),
        )
        ->merge([
            'aria-label' => $ariaLabel ?: null,
        ])
    "
    :current="$status === 'active' ?  'step' : false"
    :icon:class="TALLKit::classes(
        'rounded-full text-white flex items-center justify-center font-semibold shrink-0',
        TALLKit::widthHeight(size: $size, mode: 'large'),
        match ($status) {
            'completed' => match ($color) {
                'accent' => 'bg-[var(--color-accent)]',
                default => TALLKit::background(color: $color) ?? 'bg-green-600 dark:bg-green-700',
            },
            'active' => match ($color) {
                'accent' => 'bg-[var(--color-accent)]',
                default => TALLKit::backgroundActive(color: $color) ?? 'bg-blue-500 dark:bg-blue-600',
            },
            default => 'bg-zinc-400 dark:bg-zinc-600',
        },
    )"
    role="listitem"
>
    <x-slot:icon>
        @if ($icon)
            <tk:icon
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'icon:')"
                size="sm"
                :icon="match ($status) {
                    'completed' => $iconCompleted,
                    'active' => $iconActive,
                    default => $icon,
                } ?? $icon"
            />
        @elseif ($index)
            {{ $index }}
        @else
            <span {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'bullet:')->classes(
                'bg-white rounded-full',
                TALLKit::widthHeight(size: $size, mode: 'smallest')
            ) }}></span>
        @endif
    </x-slot:icon>

    {{ $slot }}
</tk:element>
