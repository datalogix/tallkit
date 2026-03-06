<div {{
    $attributes
        ->whereDoesntStartWith(['step:', 'line:'])
        ->classes('flex items-start justify-between w-full mx-auto')
}}>
    @foreach (collect($steps) as $index => $step)
        @if ($step)
            <tk:stepper.step
                :attributes="$attributesAfter('step:')->merge(is_array($step) ? $step : ['label' => $step], false)"
                :index="$index + 1"
                :status="$current === $index + 1 ? 'active' : ($current > $index + 1 ? 'completed' : 'pending')"
                :$iconCompleted
                :$iconActive
            />
        @endif

         @if (! $loop->last)
            <tk:stepper.line :attributes="$attributesAfter('line:')" />
        @endif
    @endforeach

    {{ $slot }}
</div>
