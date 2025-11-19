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
@else
    @php $invalid ??= $name && $errors->has($name); @endphp

    <tk:field.wrapper
        :$attributes
        :$name
        :$id
        :$label
    >
        <div
            {{ $buildDataAttribute('input') }}
            {{ $attributes->only('class')->classes('w-full relative block group/input') }}
        >
            @if (is_string($icon) && $icon !== '')
                <div class="pointer-events-none absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0">
                    <tk:icon
                        :attributes="$attributesAfter('icon:')"
                        :size="$adjustSize()"
                        :$icon
                    />
                </div>
            @elseif ($icon)
                <tk:element
                    :attributes="$attributesAfter('icon:')->classes('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0')"
                    :label="$icon"
                />
            @endif

            <input
                {{ $buildDataAttribute('control') }}
                {{ $buildDataAttribute('group-target') }}
                type="{{ $type }}"
                @isset ($name) name="{{ $name }}" @endisset
                @isset ($id) id="{{ $id }}" @endisset
                @if ($size && $type === 'range') data-size="{{ $size }}" @endif
                @if ($invalid) aria-invalid="true" data-invalid @endif
                @if ($placeholder) placeholder="{{ __($placeholder) }}" @endif
                @unless (in_livewire()) value="{{ $value }}" @endif
                @if ($loading && in_livewire()) wire:loading.style="$paddingEnd(true)" @endif
                @if ($loading && $wireTarget && in_livewire()) wire:target="{{ $wireTarget }}" @endif
                @if ($mask) x-data x-mask="{{ $mask }}" @endif
                {{ $attributes->whereDoesntStartWith([
                        'input:', 'icon:', 'loading:', 'clearable:', 'kbd:', 'copyable:', 'viewable:', 'icon-trailing:',
                        'field:', 'label:', 'information:', 'badge:', 'description:', 'help:', 'error:',
                        'group:', 'prefix:', 'suffix:',
                    ])
                    ->except('class')
                    ->classes(
                        '
                        peer
                        block
                        w-full
                        appearance-none

                        shadow-xs
                        disabled:shadow-none

                        border
                        border-zinc-200 border-b-zinc-300/80 dark:border-white/10
                        disabled:border-b-zinc-200 dark:disabled:border-white/5
                        [&[data-invalid]]:border-red-500 dark:[&[data-invalid]]:border-red-400

                        text-zinc-700
                        placeholder-zinc-400

                        disabled:text-zinc-500
                        disabled:placeholder-zinc-400/70

                        dark:text-zinc-300
                        dark:disabled:text-zinc-400
                        dark:placeholder-zinc-400
                        dark:disabled:placeholder-zinc-500

                        bg-white
                        dark:bg-white/10
                        disabled:opacity-75
                        dark:disabled:opacity-50
                        ',
                        match ($size) {
                            'xs' => 'h-8 py-1.5 text-xs rounded-md ' . ($icon ? 'ps-8' : 'ps-2') . ' pe-2',
                            'sm' => 'h-9 py-1.5 text-sm rounded-md ' . ($icon ? 'ps-9' : 'ps-2.5') . ' pe-2.5',
                            default => ' h-10 py-2 text-base rounded-lg ' . ($icon ? 'ps-10' : 'ps-3') . ' pe-3',
                            'lg' => 'h-12 py-2 text-lg rounded-lg ' . ($icon ? 'ps-11' : 'ps-3.5') . ' pe-3.5',
                            'xl' => 'h-14 py-2.5 text-xl rounded-lg ' . ($icon ? 'ps-12' : 'ps-4') . ' pe-4',
                            '2xl' => 'h-16 py-2.5 text-2xl rounded-xl ' . ($icon ? 'ps-13' : 'ps-4.5') . ' pe-4.5',
                            '3xl' => 'h-18 py-3 text-3xl rounded-xl ' . ($icon ? 'ps-14' : 'ps-5') . ' pe-5',
                        },
                        match ($type) {
                            'color' => 'py-px pe-1 ' . ($icon ? '' : 'ps-1'),
                            'range' => 'py-0 ' . match ($size) {
                                'xs' => 'h-1.5',
                                'sm' => 'h-2.5',
                                default => 'h-3',
                                'lg' => 'h-3.5',
                                'xl' => 'h-4',
                                '2xl' => 'h-4.5',
                                '3xl' => 'h-5',
                            },
                            default => 'focus:ring-blue-500 focus:border-blue-500',
                        },
                        $attributes->pluck('input:class')
                    )
                }}
            />

            @if ($loading || $clearable || $kbd || $copyable || $viewable || $iconTrailing)
                <div class="absolute top-0 bottom-0 flex items-center gap-x-1.5 pe-3 end-0 text-xs text-zinc-400 pointer-events-none">
                    @if ($loading)
                        <tk:loading
                            :attributes="$attributesAfter('loading:')->when(in_livewire(), fn($attrs) => $attrs->merge([
                                'wire:loading' => true,
                                'wire:target' => $wireTarget
                            ]))"
                            :size="$adjustSize()"
                        />
                    @endif

                    @if ($clearable)
                        <tk:input.clearable
                            :attributes="$attributesAfter('clearable:')->classes('pointer-events-auto')"
                            :size="$adjustSize()"
                        />
                    @endif

                    @if ($kbd)
                        <span {{ $attributesAfter('kbd:') }}>
                            {{ $kbd }}
                        </span>
                    @endif

                    @if ($copyable)
                        <tk:input.copyable
                            :attributes="$attributesAfter('copyable:')->classes('pointer-events-auto')"
                            :size="$adjustSize()"
                        />
                    @endif

                    @if ($viewable)
                        <tk:input.viewable
                            :attributes="$attributesAfter('viewable:')->classes('pointer-events-auto')"
                            :size="$adjustSize()"
                        />
                    @endif

                    @if (is_string($iconTrailing) && $iconTrailing !== '')
                        <tk:icon
                            :attributes="$attributesAfter('icon-trailing:')->classes('text-zinc-400/75')"
                            :size="$adjustSize()"
                            :$icon
                        />
                    @elseif ($iconTrailing)
                        <tk:element
                            :attributes="$attributesAfter('icon-trailing:')"
                            :label="$iconTrailing"
                        />
                    @endif
                </div>
            @endif
        </div>
    </tk:field>
@endif
