<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'brand:', 'menu:', 'appearance:', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
    ->classes('min-h-screen')
}}>
    <tk:header :attributes="$attributesAfter('header:')->classes('gap-2')">
        <tk:sidebar.toggle :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')" />
        <tk:brand :attributes="$attributesAfter('brand:')->classes('max-lg:hidden me-4')" />
        <tk:spacer />
        <tk:nav :items="$menu" :attributes="$attributesAfter('menu:')" />
        {{ $header ?? '' }}
        <tk:spacer />
        @if ($appearance !== false) <tk:appearance.toggle :attributes="$attributesAfter('appearance:')" /> @endif
        <tk:avatar.menu :items="$userMenu" :attributes="$attributesAfter('user-menu:')" />
    </tk:header>

    <tk:sidebar sticky stashable :attributes="$attributesAfter('sidebar:')->classes('lg:hidden')">
        <tk:sidebar.toggle icon="x-mark" :attributes="$attributesAfter('sidebar-close:')->classes('lg:hidden')" />
        <tk:brand :attributes="$attributesAfter('sidebar-brand:')" />
        <tk:nav list :items="$menu" :attributes="$attributesAfter('sidebar-menu:')" />
        {{ $sidebar ?? '' }}
    </tk:sidebar>

    <tk:main container :attributes="$attributesAfter('main:')">
        {{ $slot }}
    </tk:main>
</div>
