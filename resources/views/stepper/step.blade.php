@aware(['size', 'vertical'])
@props([
    'size' => null,
    'vertical' => null,
    'index' => null,
    'total' => null,
    'status' => null,
    'color' => null,
    'icon' => null,
    'iconCompleted' => null,
    'iconActive' => null,
    'label' => null,
])
@php

$vertical = (bool) $vertical;
$iconClass = TALLKit::classes(
    '
        flex items-center justify-center shrink-0
        rounded-full border-2
    ',
    TALLKit::widthHeight(size: $size, mode: 'large'),
    match ($status) {
        'completed' => match ($color) {
            'accent' => 'border-[var(--color-accent)] bg-[var(--color-accent)]',
            default =>
                (TALLKit::borderActive(color: $color) ?? 'border-zinc-900 dark:border-white') . ' ' .
                (TALLKit::solidBackground(color: $color) ?? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900')
        },
        'active' => match ($color) {
            'accent' => 'border-[var(--color-accent)]',
            default => TALLKit::border(color: $color) ?? 'border-zinc-900 dark:border-white',
        },
        default => match ($color) {
            'accent' => 'border-[var(--color-accent)]/30',
            default =>
                (TALLKit::borderActive(color: $color) ?? 'border-zinc-200 dark:border-zinc-600') . ' ' .
                (TALLKit::solidBackground(color: $color) ?? 'bg-transparent')
        }
    },
);

@endphp
<tk:element
    :attributes="$attributes->whereDoesntStartWith(['icon:', 'bullet:'])
        ->classes([
            '
                flex flex-col items-center gap-2
                text-center font-medium
                text-zinc-900 dark:text-white
            ',
            'flex-row' => $vertical,
            TALLKit::fontSize(size: $size),
        ])
        ->merge([
            'aria-label' => trim(
                ($index && $total ? __('Step :current of :total', ['current' => $index, 'total' => $total]) : '').
                (is_string($label) && $label !== '' ? ': '.$label : '')
            ) ?: null,
        ])
    "
    :content:class="match ($status) {
        'completed' => 'opacity-100',
        'active' => 'opacity-100',
        default => 'opacity-75',
    }"
    :current="$status === 'active' ? 'step' : false"
    :icon:class="$iconClass"
    :$label
    as="div"
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
</tk:element>

@if ($slot->hasActualContent())
    <tk:stepper.line
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'line:')
            ->classes(match ($status) {
                'completed' => match ($color) {
                    'accent' => 'bg-[var(--color-accent)]',
                    default => TALLKit::background(color: $color) ?? 'bg-zinc-900 dark:bg-white',
                },
                default => '',
            }, 'py-2')
        "
        :$size
        :$vertical
    />
@endif

{{ $slot }}

<tk:stepper.line
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'line:')
        ->classes([
            'py-2' => $slot->hasActualContent(),
            match ($status) {
                'completed' => match ($color) {
                    'accent' => 'bg-[var(--color-accent)]',
                    default => TALLKit::background(color: $color) ?? 'bg-zinc-900 dark:bg-white',
                },
                default => '',
            }
        ])
    "
    :$size
    :$vertical
/>
