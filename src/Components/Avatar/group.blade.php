<div {{ $attributes->classes(
    'flex isolate -space-x-2 rtl:space-x-reverse',
    '*:not-first:-ml-2 [&_[data-tallkit-avatar]]:ring-white dark:[&_[data-tallkit-avatar]]:ring-zinc-800',
) }}>
    @foreach (collect($avatars)->take($max) as $avatar)
        <tk:avatar :attributes="$avatar" />
    @endforeach

    @if ($max && collect($avatars)->count() > $max)
        <tk:avatar initials="..." />
    @endif

    {{ $slot }}
</div>
