@aware(['size', 'vertical'])
@props([
    'size' => null,
    'vertical' => null,
])
<div
    aria-hidden="true"
    {{
        $attributes
            ->dataKey('stepper-line')
            ->classes([
                'bg-zinc-200 dark:bg-zinc-600',
                'w-px py-1 my-1.5' => $vertical,
                'h-px flex-1 mx-1.5' => ! $vertical,
                TALLKit::generateClassBySize(size: $size, name: $vertical ? 'ms' : 'mt', values: ['3', '3.5', '4', '4.5', '5', '6', '8']),
            ])
    }}
></div>
