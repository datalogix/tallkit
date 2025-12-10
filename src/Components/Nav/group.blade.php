@aware(['size'])
@props(['size'])

@if ($expandable && $heading)
    <div
        x-data="disclosure"
        {{ $attributes->whereDoesntStartWith(['heading:', 'container:', 'line:'])->classes('group/disclosure') }}
        @if ($expanded !== false) data-open @endif
    >
        <tk:button
            :attributes="$attributesAfter('heading:')->classes('w-full justify-start p-2.5')"
            :$size
            :label="$heading"
            variant="subtle"
            icon="chevron-right"
            icon:class="me-1.5 group-data-[open]/disclosure:rotate-90 {{ $collapse === true || is_string($collapse) ? 'transition': '  ' }}"
        />

        <div
            {{
                $attributesAfter('container:')
                ->classes('relative hidden group-data-[open]/disclosure:block space-y-[2px]')
                ->when($line !== false, fn($attrs) => $attrs->classes(match($size) {
                    'xs' => 'ps-9',
                    'sm' => 'ps-9',
                    default => 'ps-10',
                    'lg' => 'ps-11',
                    'xl' => 'ps-12',
                    '2xl' => 'ps-13',
                    '3xl' => 'ps-14',
                }))
                ->when($collapse === true, fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse' => '']))
                ->when(is_string($collapse), fn($attrs) => $attrs->merge(['x-show' => 'opened', 'x-collapse.'.$collapse => '']))
            }}
        >
            @if ($line !== false)
                <div {{ $attributesAfter('line:')->classes(
                    'absolute inset-y-[3px] w-px bg-zinc-200 dark:bg-white/20 start-0',
                    match($size) {
                        'xs' => 'ms-4.5',
                        'sm' => 'ms-4.5',
                        default => 'ms-5',
                        'lg' => 'ms-5.5',
                        'xl' => 'ms-6',
                        '2xl' => 'ms-6.5',
                        '3xl' => 'ms-7',
                    }
                ) }}></div>
            @endif

            {{ $slot }}
        </div>
    </div>
@elseif ($heading)
    <div {{ $attributes->whereDoesntStartWith(['heading:', 'container:'])->classes('block space-y-[2px]') }}>
        <tk:heading
            :attributes="$attributesAfter('heading:')->classes('leading-none text-zinc-400 p-2.5')"
            :size="$adjustSize($size)"
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
