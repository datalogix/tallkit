@if ($expandable && $heading)
    <div
        x-data="disclosure"
        {{ $attributes->whereDoesntStartWith(['heading:', 'container:', 'line:'])->classes('group/disclosure') }}
        @if ($expanded !== false) data-open @endif
    >
        <tk:button
            :attributes="$attributesAfter('heading:')->classes('w-full justify-start')"
            variant="subtle"
            icon:class="me-2"
            :size="$adjustSize()"
            :label="$heading"
        >
            <x-slot name="icon">
                <tk:icon icon="chevron-down" :size="$adjustSize(move: -2)" class="hidden group-data-[open]/disclosure:block" />
                <tk:icon icon="chevron-right" :size="$adjustSize(move: -2)" class="block group-data-[open]/disclosure:hidden" />
            </x-slot>
        </tk:button>

        <div {{ $attributesAfter('container:')->classes(
            'relative hidden group-data-[open]/disclosure:block space-y-[2px]',
        )->when($line !== false, fn($attrs) => $attrs->classes(match($size) {
            'xs' => 'ps-7',
            'sm' => 'ps-7',
            default => 'ps-8',
            'lg' => 'ps-11',
            'xl' => 'ps-14',
            '2xl' => 'ps-16',
            '3xl' => 'ps-20',
        })) }}>
            @if ($line !== false)
                <div {{ $attributesAfter('line:')->classes(
                    'absolute inset-y-[3px] w-px bg-zinc-200 dark:bg-white/30 start-0',
                    match($size) {
                        'xs' => 'ms-4',
                        'sm' => 'ms-4',
                        default => 'ms-5',
                        'lg' => 'ms-6.5',
                        'xl' => 'ms-8',
                        '2xl' => 'ms-10',
                        '3xl' => 'ms-12',
                    }
                ) }}></div>
            @endif

            {{ $slot }}
        </div>
    </div>
@elseif ($heading)
    <div {{ $attributes->whereDoesntStartWith(['heading:', 'container:'])->classes('block space-y-[2px]') }}>
        <tk:heading
            :attributes="$attributesAfter('heading:')->classes('leading-none text-zinc-500 dark:text-zinc-400 px-3 py-2')"
            :size="$adjustSize(move: -2)"
            :label="$heading"
        />

        <div {{ $attributesAfter('container:') }}>
            {{ $slot }}
        </div>
    </div>
@else
    <div {{ $attributes->whereDoesntStartWith(['heading:'])->classes('block space-y-[2px]') }}>
        {{ $slot }}
    </div>
@endif
