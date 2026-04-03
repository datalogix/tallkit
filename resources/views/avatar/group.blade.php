@props([
    'avatars' => null,
    'max' => null,
    'size' => null,
    'square' => null,
])
<div {{ $attributes->classes(
    'flex isolate rtl:space-x-reverse',
     match ($size) {
        'xs' => '-space-x-3',
        'sm' => '-space-x-4',
        default => '-space-x-5',
        'lg' => '-space-x-6',
        'xl' => '-space-x-8',
        '2xl' => '-space-x-10',
        '3xl' => '-space-x-12',
     },
) }}>
    @foreach (collect($avatars)->take($max) as $avatar)
        <tk:avatar
            :attributes="$avatar"
            :$size
            :$square
        />
    @endforeach

    @if ($max && collect($avatars)->count() > $max)
        <tk:avatar
            initials="..."
            :$size
            :$square
        />
    @endif

    {{ $slot }}
</div>
