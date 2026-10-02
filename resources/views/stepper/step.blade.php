@aware(['size', 'vertical', 'current'])
@props([
    ...TALLKit::elementProps(),
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

if ($index === null && $current !== null) {
    $position = TALLKit::stepperNextPosition();
    $status ??= match (true) {
        $position < (int) $current => 'completed',
        $position === (int) $current => 'active',
        default => 'pending',
    };
}

$stepName = collect([
    $index && $total ? __('Step :current of :total', ['current' => $index, 'total' => $total]) : null,
    is_string($label) && $label !== '' ? __($label) : null,
])->filter()->implode(': ');
$stepName = $stepName !== '' ? $stepName.' ('.__(match ($status) {
    'completed' => 'completed',
    'active' => 'current',
    default => 'pending',
}).')' : null;

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
            default => TALLKit::isColor($color)
                ? TALLKit::background(color: $color).' '.TALLKit::border(color: $color)
                : 'border-zinc-900 dark:border-white bg-zinc-900 dark:bg-white text-white dark:text-zinc-900',
        },
        'active' => match ($color) {
            'accent' => 'border-[var(--color-accent)]',
            default => TALLKit::border(color: $color) ?? 'border-zinc-900 dark:border-white',
        },
        default => match ($color) {
            'accent' => 'border-[var(--color-accent)]/30',
            default => 'border-zinc-200 dark:border-zinc-600 bg-transparent',
        }
    },
);

@endphp
<tk:element
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['icon:', 'bullet:', 'line:'])
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
            'aria-label' => $stepName,
            TALLKit::dataKey('stepper-step') => true,
            'data-status' => $status ?? 'pending',
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
                :attributes="$attributes->prefixed('icon:')"
                size="sm"
                :icon="match ($status) {
                    'completed' => $iconCompleted,
                    'active' => $iconActive,
                    default => $icon,
                } ?? $icon"
            />
        @elseif ($index)
            {{ $index }}
        @elseif ($status !== 'pending')
            <span {{ $attributes->prefixed('bullet:')->classes(
                'bg-current rounded-full',
                TALLKit::widthHeight(size: $size, mode: 'smallest')
            ) }}></span>
        @endif
    </x-slot:icon>
</tk:element>

@if ($slot->hasActualContent())
    <tk:stepper.line
        :attributes="$attributes->prefixed('line:')
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
    :attributes="$attributes->prefixed('line:')
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
