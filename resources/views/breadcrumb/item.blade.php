@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'separator' => null,
    'label' => null,
    'current' => null,
])
<li {{ $attributes->prefixed('container:')->classes(
    '
        flex items-center group/breadcrumb
        opacity-75 [&:has(a,button)]:opacity-100
    '
) }}>
    @if ($slot->isEmpty())
        <tk:element
            :$label
            :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['container:', 'separator:'])->merge(['aria-current' => $current ? 'page' : null])"
            :icon:size="TALLKit::adjustSize(size: $size)"
            :$size
        />
    @else
        {{ $slot }}
    @endif

    <tk:breadcrumb.separator
        :attributes="$attributes->prefixed('separator:')"
        :icon="$separator"
        :$size
    />
</li>
