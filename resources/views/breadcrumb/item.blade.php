@aware(['size', 'separator'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'separator' => null,
    'label' => null,
    'current' => null,
])
@php

$elementAttributes = $attributes->withElementProps(get_defined_vars())
    ->whereDoesntStartWith(['container:', 'separator:']);

@endphp
<li {{ $attributes->prefixed('container:')->classes(
    '
        flex items-center min-w-0 group/breadcrumb
        [&>:is(a,button)]:opacity-75 [&>:is(a,button)]:transition-opacity [&>:is(a,button):hover]:opacity-100
        [&>[aria-current=page]]:font-medium
    '
) }}>
    @if (has_content($slot) && $current === null && $elementAttributes->isEmpty())
        {{ $slot }}
    @else
        <tk:element
            :$label
            :attributes="$elementAttributes->classes('min-w-0')"
            :$current
            exact
            content:class="truncate"
            :icon:size="TALLKit::adjustSize(size: $size)"
        >{{ $slot }}</tk:element>
    @endif

    <tk:breadcrumb.separator
        :attributes="$attributes->prefixed('separator:')"
        :icon="$separator"
        :$size
    />
</li>
