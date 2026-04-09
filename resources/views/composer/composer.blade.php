@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'rows' => null,
    'maxRows' => 10,
    'submit' => null,
    'inline' => null,
    'header' => null,
    'footer' => null,
    'actionsLeading' => null,
    'actionsTrailing' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$value = in_livewire() ? null : ($value ?? $slot);

@endphp
<tk:field.wrapper
    :$name
    :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
>
    <div
        wire:ignore
        x-data="composer(@js($submit), @js($placeholder ? __($placeholder) : null))"
        role="group"
        @if ($invalid) aria-invalid="true" data-invalid @endif
        @if ($inline) data-inline @endif
        {{
            $attributes->whereDoesntStartWith([
                'field:', 'label:', 'info:', 'badge:', 'description:',
                'group:', 'prefix:', 'suffix:',
                'help:', 'error:',
                'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                'header:', 'input:', 'textarea:', 'footer:', 'actions-leading:', 'actions-trailing:',
            ])
            ->classes(
                '
                    grid
                    grid-cols-[auto_1fr_1fr_auto]

                    peer
                    w-full
                    appearance-none
                    [print-color-adjust:exact]

                    bg-white
                    dark:bg-white/10

                    border
                    border-zinc-300
                    dark:border-white/10

                    [&[disabled]]:border-zinc-200
                    dark:[&[disabled]]:border-white/5

                    [&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-500
                    dark:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-400

                    disabled:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-500
                    dark:disabled:[&[data-invalid]:not(:has([data-tallkit-control]:focus-visible))]:border-red-400

                    shadow-xs
                    [&[disabled]]:shadow-none
                    [&[disabled]]:[&[data-invalid]]:shadow-none

                    [&[disabled]]:opacity-75
                    dark:[&[disabled]]:opacity-50

                    has-[[data-tallkit-control]:focus-visible]:outline-2
                    has-[[data-tallkit-control]:focus-visible]:outline-blue-700
                    dark:has-[[data-tallkit-control]:focus-visible]:outline-blue-300
                    has-[[data-tallkit-control]:focus-visible]:outline-offset-0

                    has-[[data-tallkit-control]:focus-visible]:ring-2
                    has-[[data-tallkit-control]:focus-visible]:ring-blue-700/20
                    dark:has-[[data-tallkit-control]:focus-visible]:ring-blue-300/20

                    [&[disabled]]:cursor-not-allowed
                    [&[disabled]]:pointer-events-none
                ',
                match ($size) {
                    'xs' => 'text-xs rounded-md px-1.5 py-1',
                    'sm' => 'text-sm rounded-md px-2 py-1.5',
                    default => 'text-base rounded-lg px-3 py-2',
                    'lg' => 'text-lg rounded-lg px-3.5 py-2.5',
                    'xl' => 'text-xl rounded-lg px-4 py-3',
                    '2xl' => 'text-2xl rounded-xl px-4.5 py-3.5',
                    '3xl' => 'text-3xl rounded-xl px-5 py-4',
                },
            )
        }}
    >
        @if ($header && !$inline)
            <div {{ TALLKit::attributesAfter($attributes, 'header:')->classes(
                '
                    flex items-center gap-1
                    col-span-3 mb-2
                ',
            ) }}>
                {{ $header }}
            </div>
        @endif

        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->classes(
                    '
                        col-span-4
                        [[data-inline]_&]:col-span-2
                        [[data-inline]_&]:col-start-2

                        [&_[data-tallkit-control]]:px-2
                        [&_[data-tallkit-control]]:py-1.5
                        [&_[data-tallkit-control]]:h-auto
                        [&_[data-tallkit-control]]:bg-transparent
                        [&_[data-tallkit-control]]:border-none
                        [&_[data-tallkit-control]]:outline-none
                        [&_[data-tallkit-control]]:ring-0
                        [&_[data-tallkit-control]]:resize-none
                        [&_[data-tallkit-control]]:shadow-none
                        [&_[data-tallkit-control]]:rounded-none
                    '
                )"
        >
            @isset ($input)
                {{ $input }}
            @else
                <tk:textarea
                    :attributes="TALLKit::attributesAfter($attributes, 'textarea:')"
                    :$id
                    :$size
                    :$placeholder
                    :$maxRows
                    :label="false"
                    :rows="$inline ? 1 : ($rows ?? 2)"
                >{{ $value }}</tk:textarea>
            @endisset
        </tk:field.control>

        @if ($footer && !$inline)
            <div {{ TALLKit::attributesAfter($attributes, 'footer:')->classes(
                '
                    flex items-center gap-1
                    col-span-3mt-2
                ',
            ) }}>
                {{ $footer }}
            </div>
        @endif

        @isset ($actionsLeading)
            <div {{ TALLKit::attributesAfter($attributes, 'actions-leading:')->classes(
                '
                    flex items-start gap-1
                    col-span-2
                    [[data-inline]_&]:col-span-1
                    [[data-inline]_&]:col-start-1
                    [[data-inline]_&]:row-start-1
                ',
            ) }}>
                {{ $actionsLeading ?? '' }}
            </div>
        @endisset

        @isset ($actionsTrailing)
            <div {{ TALLKit::attributesAfter($attributes, 'actions-trailing:')->classes(
                '
                    flex items-start justify-end gap-1
                    col-span-2
                    [[data-inline]_&]:col-span-1
                ',
            ) }}>
                {{ $actionsTrailing ?? '' }}
            </div>
        @endisset
    </div>
</tk:field.wrapper>
