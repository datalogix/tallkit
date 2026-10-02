@props([
    'size' => null,
    'vertical' => null,
    'current' => null,
    'items' => null,
    'iconCompleted' => null,
    'iconActive' => null,
    'color' => null,
])
@php

$vertical = (bool) $vertical;
$currentStep = (int) $current;
$items = collect($items)->filter()->values();
$totalSteps = $items->count();

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
    @foreach ($items as $index => $step)
        <tk:stepper.step
            :attributes="$attributes->prefixed('step:')
                ->merge(TALLKit::attributesFromItem($step), false)
                ->wireKey(TALLKit::generateId(prefix: 'stepper-step', name: (string) $index))
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
@php(TALLKit::stepperReset())
