@php
$separatorClasses = $classes('mx-1.5 text-zinc-300 dark:text-white/80 group-last/breadcrumb:hidden')
@endphp

<li {{ $attributesAfter('container:')->classes('flex items-center group/breadcrumb') }}>
    <tk:element
        :attributes="$attributes->whereDoesntStartWith(['container:', 'separator:'])
            ->classes($href ? 'text-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-white/80')"
        :$href
        :icon:size="$adjustSize()"
    >
        {{ $slot }}
    </tk:element>

    @if ($separator == null)
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'rtl:inline')"
            icon="chevron-right"
        />
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'hidden rtl:inline')"
            icon="chevron-left"
        />
    @elseif ($isSlot($separator))
        {{ $separator }}
    @elseif ($separator === 'slash')
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses, 'rtl:-scale-x-100')"
            icon="slash"
        />
    @else
        <tk:icon
            :attributes="$attributesAfter('separator:')->classes($separatorClasses)"
            :icon="$separator"
        />
    @endif
</li>
