@if ($container)
    <tk:container :$attributes>
        {{ $slot }}
    </tk:container>
@else
    {{ $slot }}
@endif
