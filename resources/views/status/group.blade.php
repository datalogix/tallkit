@props([
    'items' => null,
    'size' => null,
])
<div {{ $attributes->classes(
    'flex flex-wrap [&>*]:flex-1 [&>*]:basis-40',
    TALLKit::gap(size: $size),
) }}>
    @foreach (collect($items) as $status)
        <tk:status
            :attributes="TALLKit::attributesMerge(TALLKit::attributesFromItem($status, 'title'))"
            :$size
        />
    @endforeach

    {{ $slot }}
</div>
