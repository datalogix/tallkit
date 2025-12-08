@aware(['size'])
@props(['size'])

@php
$separatorClasses = $classes('text-zinc-300 dark:text-white/70 group-last/breadcrumb:hidden', match($size) {
    'xs' => 'mx-1',
    'sm' => 'mx-1',
    default => 'mx-1.5',
    'lg' => 'mx-1.5',
    'xl' => 'mx-2',
    '2xl' => 'mx-2',
    '3xl' => 'mx-2.5',
})
@endphp

<li {{ $attributesAfter('container:')->classes('flex items-center group/breadcrumb') }}>
    @if ($slot->isEmpty())
        <tk:element
            :attributes="$attributes
                ->whereDoesntStartWith(['container:', 'separator:'])
                ->classes($href ? 'text-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-white/70')
            "
            :$href
            :icon:size="$adjustSize($size)"
        />
    @else
        {{ $slot }}
    @endif

    @if ($separator == null)
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'rtl:inline')"
            :$size
            icon="chevron-right"
        />
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'hidden rtl:inline')"
            :$size
            icon="chevron-left"
        />
    @elseif ($isSlot($separator))
        {{ $separator }}
    @elseif ($separator === 'slash')
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'rtl:-scale-x-100')"
            :$size
            icon="slash"
        />
    @else
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses)"
            :$size
            :icon="$separator"
        />
    @endif
</li>
