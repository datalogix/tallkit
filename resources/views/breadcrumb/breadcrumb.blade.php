@props([
    'items' => null,
    'size' => null,
    'scale' => null,
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
            <ol {{ $attributes->prefixed('list:')->classes('flex items-center') }}>
                @foreach (collect($items) as $index => $item)
                    <tk:breadcrumb.item
                        :attributes="$attributes->prefixed('item:')
                            ->merge(TALLKit::attributesFromItem($item), false)
                            ->mergeDefined(['aria-current' => $loop->last ? 'page' : null], false)
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
