@props([
    'prefix' => null,
    'suffix' => null,
    'size' => null,
    'label' => null,
    'labelAppend' => null,
    'labelPrepend' => null,
    'description' => null,
    'help' => null,
    'badge' => null,
])
@php
$groupId = ($label || $description) && ! $attributes->has('label:for') ? ($attributes->get('id') ?? TALLKit::stableId('field-group')) : null;
@endphp
<tk:field.wrapper
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['label' => null, 'labelAppend' => null, 'labelPrepend' => null, 'description' => null, 'help' => null, 'badge' => null, 'info' => null, 'showError' => null])->whereDoesntStartWith(['prefix:', 'suffix:'])"
    :prefix="null"
    :suffix="null"
    :id="$groupId"
    label:as="{{ $attributes->has('label:for') ? 'label' : 'span' }}"
>
    <div {{ $attributes->only('class')->classes('tk-field-group')->merge($groupId ? [
        'role' => 'group',
        'aria-labelledby' => $label ? $groupId.'-label' : null,
        'aria-describedby' => $description ? $groupId.'-description' : null,
    ] : []) }}>
        @if ($prefix || $attributes->prefixed('prefix:')->isNotEmpty())
            <tk:field.group.prefix
                :attributes="$attributes->prefixed('prefix:')"
                :$size
            >
                {{ $prefix }}
            </tk:field.group.prefix>
        @endif

        {{ $slot }}

        @if ($suffix || $attributes->prefixed('suffix:')->isNotEmpty())
            <tk:field.group.suffix
                :attributes="$attributes->prefixed('suffix:')"
                :$size
            >
                {{ $suffix }}
            </tk:field.group.suffix>
        @endif
    </div>
</tk:field.wrapper>
