<tk:section :attributes="$attributes->whereDoesntStartWith(['container:', 'menu:', 'nav:', 'select:', 'area:'])">
    @isset ($header)
        <x-slot:header>
            {{ $header }}
        </x-slot:header>
    @endisset

    @isset ($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endisset

    <div {{ $attributesAfter('container:')->classes(
        'flex items-start',
        match ($breakpoint) {
            'sm' => 'max-sm:flex-col',
            default => 'max-md:flex-col',
            'lg' => 'max-lg:flex-col',
            'xl' => 'max-xl:flex-col',
            '2xl' => 'max-2xl:flex-col',
        },
    ) }}>
        @if (collect($menu)->isNotEmpty())
            <div {{ $attributesAfter('menu:')->classes(
                'w-full me-16 pb-6',
                match ($breakpoint) {
                    'sm' => 'sm:w-[220px]',
                    default => 'md:w-[220px]',
                    'lg' => 'lg:w-[220px]',
                    'xl' => 'xl:w-[220px]',
                    '2xl' => '2xl:w-[220px]',
                },
            ) }}>
                <tk:nav
                    list
                    :attributes="$attributesAfter('nav:')->classes(
                        match ($breakpoint) {
                            'sm' => 'max-sm:hidden',
                            default => 'max-md:hidden',
                            'lg' => 'max-lg:hidden',
                            'xl' => 'max-xl:hidden',
                            '2xl' => 'max-2xl:hidden',
                        },
                    )"
                    :items="$menu"
                />

                @php
                $options = collect($menu)->map(fn ($item) => [
                    'id' => data_get($item, 'href') ?? route_detect(data_get($item, 'route'), data_get($item, 'parameters'), null),
                    'name' => data_get($item, 'label') ?? data_get($item, 'name') ?? data_get($item, 'title'),
                    'current' => data_get($item, 'current'),
                ]);
                @endphp

                <tk:select
                    :attributes="$attributesAfter('select:')->classes(
                        match ($breakpoint) {
                            'sm' => 'sm:hidden',
                            default => 'md:hidden',
                            'lg' => 'lg:hidden',
                            'xl' => 'xl:hidden',
                            '2xl' => '2xl:hidden',
                        },
                    )"
                    :options="$options"
                    :value="data_get($options->first(fn($item) => data_get($item, 'current') ?? is_current_href(data_get($item, 'id'))), 'id')"
                    :placeholder="false"
                    x-data
                    x-on:change="{{ in_livewire() ? 'Livewire.navigate($el.value)' : 'window.location.href = $el.value' }}"
                />
            </div>
        @endif

        <div {{ $attributesAfter('area:')->classes('flex-1 w-full space-y-6') }}>
            {{ $slot }}
        </div>
    </div>
</tk:section>
