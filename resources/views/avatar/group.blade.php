@props([
    'items' => null,
    'max' => null,
    'size' => null,
    'square' => null,
])
<div {{ $attributes->classes(
    'flex isolate [&>*]:ring-2 [&>*]:ring-white dark:[&>*]:ring-zinc-900',
     match ($size) {
        'xs' => '-space-x-1.5',
        'sm' => '-space-x-2',
        'lg' => '-space-x-2.5',
        'xl' => '-space-x-3',
        '2xl' => '-space-x-3.5',
        '3xl' => '-space-x-4',
        default => '-space-x-2',
     },
) }}>
    @foreach (collect($items)->take($max) as $avatar)
        <tk:avatar
            :attributes="TALLKit::attributesMerge(is_array($avatar) || $avatar instanceof Illuminate\View\ComponentAttributeBag ? $avatar : ['name' => $avatar])"
            :$size
            :$square
        />
    @endforeach

    @if ($max && collect($items)->count() > $max)
        <tk:avatar
            initials="+{{ collect($items)->count() - $max }}"
            :tooltip="__(':count more', ['count' => collect($items)->count() - $max])"
            :$size
            :$square
        />
    @endif

    {{ $slot }}
</div>
