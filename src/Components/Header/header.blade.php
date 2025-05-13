
<header {{ $attributes->merge($sticky ? ['x-data' => 'header'] : [])
    ->classes('z-10 min-h-14')
    ->classes(['flex items-center px-6 lg:px-8' => !$container])
}}>
    @if ($container)
        <div class="mx-auto w-full min-h-14 h-full [:where(&)]:max-w-7xl px-6 lg:px-8 flex items-center">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</header>
