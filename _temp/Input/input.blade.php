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
        :$name
        :$id
        :$label
    >
        <tk:field.control
            :$attributes
            :$size
        >
            <input
                type="{{ $type }}"
                {{ $buildDataAttribute('control') }}
                {{ $buildDataAttribute('group-target') }}
                @if ($name) name="{{ $name }}" @endif
                @if ($id) id="{{ $id }}" @endif
                @if ($invalid) aria-invalid="true" data-invalid @endif
                @if ($placeholder) placeholder="{{ __((string) $placeholder) }}" @endif
                @unless (in_livewire()) value="{{ $value }}" @endif
                @if ($mask) x-data x-mask="{{ $mask }}" @endif
                {{ $attributes->whereDoesntStartWith([
                        'field:', 'label:', 'info:', 'badge:', 'description:',
                        'group:', 'prefix:', 'suffix:',
                        'help:', 'error:',
                        'control:', 'prepend:', 'icon:', 'append:', 'loading:', 'icon-trailing:', 'kbd:',
                        'input:', 'clearable:', 'copyable:', 'viewable:',
                    ])
                    ->except('class')
                    ->classes(
                        '
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

                            disabled:opacity-75
                            dark:disabled:opacity-50
                        ',
                        match ($variant) {
                            'ghost' => 'focus:outline-none',
                            default => '
                                bg-white
                                dark:bg-white/10

                                border
                                border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                                disabled:border-b-zinc-200 dark:disabled:border-white/5
                                disabled:[&[data-invalid]]:border-red-500 disabled:dark:[&[data-invalid]]:border-red-400
                                [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                                shadow-xs
                                disabled:shadow-none
                                [&[data-invalid]]:disabled:shadow-none
                            '
                        },
                        match ($size) {
                            'xs' => 'h-8 text-xs rounded-md ps-2 pe-2',
                            'sm' => 'h-9 text-sm rounded-md ps-2.5 pe-2.5',
                            default => ' h-10 text-base rounded-lg ps-3 pe-3',
                            'lg' => 'h-12 text-lg rounded-lg ps-3.5 pe-3.5',
                            'xl' => 'h-14 text-xl rounded-lg ps-4 pe-4',
                            '2xl' => 'h-16 text-2xl rounded-xl ps-4.5 pe-4.5',
                            '3xl' => 'h-18 text-3xl rounded-xl ps-5 pe-5',
                        },
                        match ($type) {
                            'color' => $prepend || $icon ? '' : 'ps-1 pe-1',
                            default => '',
                        },
                        $attributes->pluck('input:class')
                    )
                }}
            />

            @if ($clearable || $copyable || $viewable)
                <x-slot:append>
                    {{ $append ?? '' }}

                    @if ($clearable)
                        <tk:input.clearable
                            :attributes="$attributesAfter('clearable:')"
                            :$size
                        />
                    @endif

                    @if ($copyable)
                        <tk:input.copyable
                            :attributes="$attributesAfter('copyable:')"
                            :$size
                        />
                    @endif

                    @if ($viewable)
                        <tk:input.viewable
                            :attributes="$attributesAfter('viewable:')"
                            :$size
                        />
                    @endif
                </x-slot:append>
            @endif
        </tk:field.control>
    </tk:field.wrapper>
@endif
