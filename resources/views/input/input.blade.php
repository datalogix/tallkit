@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'type' => null,
    'mask' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::resolveFieldContext($attributes, $label);
$type ??= TALLKit::detectInputType($name);
$mask = TALLKit::detectInputMask($name, $mask, $type);
$viewable ??= $type === 'password';
$hasControl = $clearable || $copyable || $viewable || $prepend || $icon || $append || $loading || $iconTrailing || $kbd || $attributes->has('class');

@endphp
@if ($type === 'file')
    <tk:upload :$attributes>{{ $slot }}</tk:upload>
@elseif ($type === 'checkbox')
    <tk:checkbox :$attributes>{{ $slot }}</tk:checkbox>
@elseif ($type === 'radio')
    <tk:radio :$attributes>{{ $slot }}</tk:radio>
@elseif ($type === 'reset' || $type === 'button')
    <tk:button :$attributes :$type>{{ $slot }}</tk:button>
@elseif ($type === 'submit')
    <tk:submit :$attributes>{{ $slot }}</tk:submit>
@elseif ($type === 'range')
    <tk:slider :$attributes>{{ $slot }}</tk:slider>
@else
    <tk:field.wrapper
        :$name
        :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldProps())"
    >
        <tk:field.control
            :$size
            :attributes="TALLKit::mergeDefinedProps($attributes, get_defined_vars(), TALLKit::fieldControlProps())
                ->when(
                    $hasControl,
                    fn ($attrs) => $attrs->classes(
                        '
                            flex
                            items-center

                            bg-white
                            dark:bg-white/10

                            border
                            border-zinc-300
                            dark:border-white/10

                            has-[[data-tallkit-control]:disabled]:border-zinc-200
                            dark:has-[[data-tallkit-control]:disabled]:border-white/5

                            has-[[data-tallkit-control][data-invalid]:not(:focus-visible)]:border-red-500
                            dark:has-[[data-tallkit-control][data-invalid]:not(:focus-visible)]:border-red-400

                            has-[[data-tallkit-control][data-invalid]:disabled:not(:focus-visible)]:border-red-500
                            dark:has-[[data-tallkit-control][data-invalid]:disabled:not(:focus-visible)]:border-red-400

                            shadow-xs
                            has-[[data-tallkit-control]:disabled]:shadow-none
                            has-[[data-tallkit-control][data-invalid]:disabled]:shadow-none

                            has-[[data-tallkit-control]:disabled]:opacity-75
                            dark:has-[[data-tallkit-control]:disabled]:opacity-50
                            has-[[data-tallkit-control]:disabled]:cursor-not-allowed

                            has-[[data-tallkit-control]:focus-visible]:outline-2
                            has-[[data-tallkit-control]:focus-visible]:outline-blue-700
                            dark:has-[[data-tallkit-control]:focus-visible]:outline-blue-300
                            has-[[data-tallkit-control]:focus-visible]:outline-offset-0

                            has-[[data-tallkit-control]:focus-visible]:ring-2
                            has-[[data-tallkit-control]:focus-visible]:ring-blue-700/20
                            dark:has-[[data-tallkit-control]:focus-visible]:ring-blue-300/20

                            [&_[data-tallkit-control]]:outline-none
                        ',
                        match ($size) {
                            'xs' => 'rounded-md',
                            'sm' => 'rounded-md',
                            default => 'rounded-lg',
                            'lg' => 'rounded-lg',
                            'xl' => 'rounded-lg',
                            '2xl' => 'rounded-xl',
                            '3xl' => 'rounded-xl',
                        },
                    ),
                )
            "
        >
            <input
                type="{{ $type }}"
                @if ($name) name="{{ $name }}" @endif
                @if ($id) id="{{ $id }}" @endif
                @if ($invalid) aria-invalid="true" data-invalid @endif
                @if ($placeholder) placeholder="{{ __((string) $placeholder) }}" @endif
                @unless (in_livewire()) value="{{ $value ?? $slot }}" @endif
                @if ($mask) x-data x-mask="{{ $mask }}" @endif
                {{
                    $attributes
                        ->dataKey('input')
                        ->dataKey('control')
                        ->dataKey('group-target')
                        ->merge(['wire:model' => $wireModel])
                        ->whereDoesntStartWith([
                            'field:', 'label:', 'info:', 'badge:', 'description:',
                            'group:', 'prefix:', 'suffix:',
                            'help:', 'error:',
                            'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                            'input:', 'clearable:', 'copyable:', 'viewable:',
                        ])
                        ->except('class')
                        ->classes(
                            '
                                bg-transparent
                                flex-1
                                peer
                                block
                                w-full
                                appearance-none
                                [print-color-adjust:exact]

                                text-zinc-700
                                disabled:text-zinc-500
                                dark:text-zinc-300
                                dark:disabled:text-zinc-400

                                placeholder-zinc-400
                                disabled:placeholder-zinc-400/70
                                dark:placeholder-zinc-400
                                dark:disabled:placeholder-zinc-500

                                disabled:cursor-not-allowed
                                disabled:resize-none
                            ',
                            match ($size) {
                                'xs' => 'h-8 text-xs ps-2 pe-2',
                                'sm' => 'h-9 text-sm ps-2.5 pe-2.5',
                                default => 'h-10 text-base ps-3 pe-3',
                                'lg' => 'h-12 text-lg ps-3.5 pe-3.5',
                                'xl' => 'h-14 text-xl ps-4 pe-4',
                                '2xl' => 'h-16 text-2xl ps-4.5 pe-4.5',
                                '3xl' => 'h-18 text-3xl ps-5 pe-5',
                            },
                            match ($type) {
                                'color' => $prepend || $icon ? '' : 'ps-1 pe-1',
                                default => '',
                            },
                            $attributes->pluck('input:class'),
                         )
                         ->when(
                            !$hasControl,
                            fn ($attrs) => $attrs->classes(
                                '
                                    bg-white
                                    dark:bg-white/10

                                    border
                                    border-zinc-300
                                    dark:border-white/10

                                    disabled:border-zinc-200
                                    dark:disabled:border-white/5

                                    [&[data-invalid]:not(:focus-visible)]:border-red-500
                                    dark:[&[data-invalid]:not(:focus-visible)]:border-red-400

                                    disabled:[&[data-invalid]:not(:focus-visible)]:border-red-500
                                    dark:disabled:[&[data-invalid]:not(:focus-visible)]:border-red-400

                                    shadow-xs
                                    disabled:shadow-none
                                    [&[data-invalid]]:disabled:shadow-none

                                    disabled:opacity-75
                                    dark:disabled:opacity-50

                                    focus-visible:outline-2
                                    focus-visible:outline-blue-700
                                    dark:focus-visible:outline-blue-300
                                    focus-visible:outline-offset-0

                                    focus-visible:ring-2
                                    focus-visible:ring-blue-700/20
                                    dark:focus-visible:ring-blue-300/20
                                ',
                                match ($size) {
                                    'xs' => 'rounded-md',
                                    'sm' => 'rounded-md',
                                    default => 'rounded-lg',
                                    'lg' => 'rounded-lg',
                                    'xl' => 'rounded-lg',
                                    '2xl' => 'rounded-xl',
                                    '3xl' => 'rounded-xl',
                                },
                            ),
                        )
                }}
            />

            @if ($clearable || $copyable || $viewable)
                <x-slot:append>
                    {{ $append ?? '' }}

                    @if ($clearable)
                        <tk:input.clearable
                            :attributes="TALLKit::attributesAfter($attributes, 'clearable:')"
                            :$size
                        />
                    @endif

                    @if ($copyable)
                        <tk:input.copyable
                            :attributes="TALLKit::attributesAfter($attributes, 'copyable:')"
                            :$size
                        />
                    @endif

                    @if ($viewable)
                        <tk:input.viewable
                            :attributes="TALLKit::attributesAfter($attributes, 'viewable:')"
                            :$size
                        />
                    @endif
                </x-slot:append>
            @endif
        </tk:field.control>
    </tk:field.wrapper>
@endif
