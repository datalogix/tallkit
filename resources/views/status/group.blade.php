@props([
    'statuses' => null,
    'size' => null,
])
<div {{ $attributes->classes(
    'flex flex-wrap [&>*]:flex-1 [&>*]:basis-40',
    TALLKit::gap(size: $size),
) }}>
    @foreach (collect($statuses) as $status)
        <tk:status
            :attributes="$status"
            :$size
        />
    @endforeach

    {{ $slot }}
</div>
