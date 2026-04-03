@props([
    'items' => null,
    'size' => null,
    'mode' => null,
])
@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{ $attributes->whereDoesntStartWith(['item:'])->classes(
            'flex',
            TALLKit::fontSize(size: $size, mode: $mode, weight: true),
        ) }}>
            @foreach (collect($items) as $item)
                <tk:breadcrumb.item
                    :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                    :$size
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
