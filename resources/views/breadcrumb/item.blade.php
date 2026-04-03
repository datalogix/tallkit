@aware(['size'])
@props([
    'size' => null,
    'separator' => null,
])
<li {{ TALLKit::attributesAfter($attributes, 'container:')->classes(
    '
        flex items-center group/breadcrumb
        opacity-75 [&:has(a,button)]:opacity-100
    '
) }}>
    @if ($slot->isEmpty())
        <tk:element
            :attributes="$attributes->whereDoesntStartWith(['container:', 'separator:'])"
            :icon:size="TALLKit::adjustSize(size: $size)"
            :$size
        />
    @else
        {{ $slot }}
    @endif

    <tk:breadcrumb.separator
        :attributes="TALLKit::attributesAfter($attributes, 'separator:')"
        :icon="$separator"
        :$size
    />
</li>
