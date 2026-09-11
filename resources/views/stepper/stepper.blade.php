@props([
    'size' => null,
    'vertical' => null,
    'current' => null,
    'steps' => null,
    'iconCompleted' => null,
    'iconActive' => null,
    'color' => null,
])
@php

$vertical = (bool) $vertical;
$currentStep = (int) $current;
$steps = collect($steps)->filter()->values();
$totalSteps = $steps->count();

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['step:', 'line:'])
            ->classes([
                '
                    flex items-start justify-between w-full mx-auto
                    [&_[data-tallkit-stepper-line]:last-child]:hidden
                ',
                'inline-flex flex-col' => $vertical
            ])
    }}
    role="list"
>
    @foreach ($steps as $index => $step)
        <tk:stepper.step
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'step:')
                ->merge(is_array($step) ? $step : ['label' => $step], false)
                ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'stepper-step', name: (string) $index)] : [], false)
            "
            :index="$index + 1"
            :total="$totalSteps"
            :status="$currentStep === $index + 1 ? 'active' : ($currentStep > $index + 1 ? 'completed' : 'pending')"
            :$iconCompleted
            :$iconActive
            :$size
            :$vertical
            :$color
        />
    @endforeach

    {{ $slot }}
</div>
