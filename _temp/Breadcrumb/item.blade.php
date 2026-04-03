@aware(['size'])
@props(['size'])

<li {{ $attributesAfter('container:')->classes(
    '
        flex items-center group/breadcrumb
        opacity-75 [&:has(a,button)]:opacity-100
    '
) }}>
    @if ($slot->isEmpty())
        <tk:element
            :attributes="$attributes->whereDoesntStartWith(['container:', 'separator:'])"
            :icon:size="$adjustSize(size: $size)"
            :$size
        />
    @else
        {{ $slot }}
    @endif

    <tk:breadcrumb.separator
        :attributes="$attributesAfter('separator:')"
        :icon="$separator"
        :$size
    />
</li>
