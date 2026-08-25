@props([
    'items' => null,
    'size' => null,
    'mode' => null,
])
@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav
            aria-label="{{ __('Breadcrumb') }}"
            {{
                $attributes->whereDoesntStartWith(['item:', 'list:'])
                    ->classes(TALLKit::fontSize(size: $size, mode: $mode),)
            }}
        >
            <ol {{ TALLKit::attributesAfter($attributes, 'list:')->classes('flex items-center') }}>
                @foreach (collect($items) as $index => $item)
                    <tk:breadcrumb.item
                        :attributes="TALLKit::attributesAfter($attributes, 'item:')
                            ->merge(is_array($item) ? $item : ['label' => $item], false)
                            ->merge($loop->last ? ['aria-current' => 'page'] : [], false)
                            ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('breadcrumb-item', (string) data_get($item, 'label', $index))] : [], false)
                        "
                        :$size
                    />
                @endforeach

                {{ $slot }}
            </ol>
        </nav>
    @endif
@endif
