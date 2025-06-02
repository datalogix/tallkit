<div {{ $attributes->classes(
    '[grid-area:main] p-6 lg:p-8 [[data-tallkit-container]_&]:px-0',
    $container ? 'mx-auto w-full [:where(&)]:max-w-7xl' : ''
) }} data-tallkit-main>
    {{ $slot }}
</div>
