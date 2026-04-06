@props([
    // field
    'id' => null,
    'size' => null,
    'variant' => null,
    'align' => null,
    'labelAppend' => null,
    'labelPrepend' => null,
    'description' => null,
    'help' => null,
    'badge' => null,
    'info' => null,
    'prefix' => null,
    'suffix' => null,
    'showError' => null,

    // control
    'prepend' => null,
    'append' => null,
    'icon' => null,
    'iconTrailing' => null,
    'kbd' => null,
    'loading' => null,

    // input
    'value' => null,
    'type' => null,
    'mask' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
])
@php

[$name, $fieldName, $label, $placeholder, $invalid, $wireModel] = TALLKit::fieldAttributes($attributes);
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
        :$attributes
        :$variant
        :$align
        :$name
        :$id
        :$label
        :$labelAppend
        :$labelPrepend
        :$description
        :$help
        :$badge
        :$info
        :$prefix
        :$suffix
        :$size
        :$showError
    >
        <tk:field.control
            :attributes="$attributes->classes([
                '
                    flex items-center

                    bg-white
                    dark:bg-white/10

                    border
                    border-zinc-200 border-b-zinc-300/80 dark:border-white/10

                    has-[input:disabled]:border-b-zinc-200
                    dark:has-[input:disabled]:border-white/5

                    has-[input[data-invalid]:disabled]:border-red-500
                    dark:has-[input[data-invalid]:disabled]:border-red-400
                    has-[input[data-invalid]]:border-red-500
                    dark:has-[input[data-invalid]]:border-red-400

                    shadow-xs
                    has-[input:disabled]:shadow-none
                    has-[input[data-invalid]:disabled]:shadow-none

                    has-[input:disabled]:opacity-75
                    dark:has-[input:disabled]:opacity-50

                    focus-within:ring-2
                    focus-within:ring-blue-300

                ' => $hasControl,
                match ($size) {
                    'xs' => 'rounded-md',
                    'sm' => 'rounded-md',
                    default => 'rounded-lg',
                    'lg' => 'rounded-lg',
                    'xl' => 'rounded-lg',
                    '2xl' => 'rounded-xl',
                    '3xl' => 'rounded-xl',
                } => $hasControl,
            ])"
            :$size
            :$prepend
            :$append
            :$icon
            :$iconTrailing
            :$kbd
            :$loading
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
                        ->classes([
                            '
                                outline-none
                                bg-transparent
                                flex-1
                                peer
                                block
                                w-full
                                appearance-none

                                text-zinc-700
                                placeholder-zinc-400

                                disabled:text-zinc-500
                                disabled:placeholder-zinc-400/70

                                dark:text-zinc-300
                                dark:disabled:text-zinc-400
                                dark:placeholder-zinc-400
                                dark:disabled:placeholder-zinc-500

                                focus:outline-none
                            ',
                            '
                                bg-white
                                dark:bg-white/10

                                border
                                border-zinc-200 border-b-zinc-300/80 dark:border-white/10

                                disabled:border-b-zinc-200
                                dark:disabled:border-white/5

                                disabled:[&[data-invalid]]:border-red-500
                                disabled:dark:[&[data-invalid]]:border-red-400

                                [&[data-invalid]]:border-red-500
                                dark:[&[data-invalid]]:border-red-400

                                shadow-xs
                                disabled:shadow-none
                                [&[data-invalid]]:disabled:shadow-none

                                disabled:opacity-75
                                dark:disabled:opacity-50

                                focus:ring-2
                                focus:ring-blue-300
                            ' => !$hasControl,
                            match ($size) {
                                'xs' => 'rounded-md',
                                'sm' => 'rounded-md',
                                default => 'rounded-lg',
                                'lg' => 'rounded-lg',
                                'xl' => 'rounded-lg',
                                '2xl' => 'rounded-xl',
                                '3xl' => 'rounded-xl',
                            } => !$hasControl,
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
                            $attributes->pluck('input:class')
                        ])
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
