<nav {{ $attributes->classes('flex text-sm font-medium') }}>
    @foreach (collect($items) as $item)
        <tk:breadcrumb.item :attributes="$attributesAfter('item:')->merge($item)" />
    @endforeach

    {{ $slot }}
</nav>
