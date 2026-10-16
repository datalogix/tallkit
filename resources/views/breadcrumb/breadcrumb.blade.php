@props([
    'items' => null,
    'size' => null,
    'scale' => null,
    'separator' => null,
])
@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<'.'nav'))
        {{ $slot }}
    @else
        <nav
            {{
                $attributes->whereDoesntStartWith(['item:', 'list:'])
                    ->classes(TALLKit::fontSize(size: $size, mode: $scale),)
                    ->merge(['aria-label' => __('Breadcrumb')])
            }}
        >
            <ol {{ $attributes->prefixed('list:')->classes('flex flex-wrap items-center gap-y-1 min-w-0') }}>
                @foreach (collect($items) as $index => $item)
                    <tk:breadcrumb.item
                        :attributes="$attributes->prefixed('item:')
                            ->merge(TALLKit::attributesFromItem($item), false)
                            ->merge($loop->last ? ['aria-current' => 'page'] : [], false)
                            ->wireKey(TALLKit::generateId(prefix: 'breadcrumb-item', name: (string) data_get($item, 'label', $index)))
                        "
                        :$size
                    />
                @endforeach

                {{ $slot }}
            </ol>
        </nav>
    @endif
@endif
