@props([
    'current' => null,
    'steps' => null,
    'size' => null,
    'iconCompleted' => null,
    'iconActive' => null,
    'color' => null,
])
@php

$currentStep = (int) $current;
$totalSteps = collect($steps)->filter()->count();

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith(['step:', 'line:'])
            ->classes('flex items-start justify-between w-full mx-auto')
    }}
    role="list"
>
    @foreach (collect($steps) as $index => $step)
        @if ($step)
            <tk:stepper.step
                :attributes="TALLKit::attributesAfter($attributes, 'step:')
                    ->merge(is_array($step) ? $step : ['label' => $step], false)
                    ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('stepper-step', (string) $index)] : [], false)
                "
                :index="$index + 1"
                :total="$totalSteps"
                :status="$currentStep === $index + 1 ? 'active' : ($currentStep > $index + 1 ? 'completed' : 'pending')"
                :$iconCompleted
                :$iconActive
                :$size
                :$color
            />
        @endif

         @if (! $loop->last)
            <tk:stepper.line
                :attributes="TALLKit::attributesAfter($attributes, 'line:')"
                :$size
            />
        @endif
    @endforeach

    {{ $slot }}
</div>
