@props([
    ...TALLKit::fieldProps(),
    'inline' => null,
    'align' => null,
    'name' => null,
])
@php

$hasPrefix = $prefix || TALLKit::attributesAfter(attributes: $attributes, prefix: 'prefix:')->isNotEmpty();
$hasSuffix = $suffix || TALLKit::attributesAfter(attributes: $attributes, prefix: 'suffix:')->isNotEmpty();

@endphp
@if ($label || $description || $help || $hasPrefix || $hasSuffix)
    <tk:field :$inline :$align :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'field:')">
        <tk:label
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'label:')
                ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'info:', prepend: true)->getAttributes())
                ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'badge:', prepend: true)->getAttributes())
            "
            :for="$id"
            :$label
            :$labelPrepend
            :$labelAppend
            :$size
            :$info
            :$badge
        />

        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'description:')->merge(['id' => $id ? $id.'-description' : null])"
            :label="$description"
            :$size
        />

        @if (!$inline && ($hasPrefix || $hasSuffix))
            <tk:field.group
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'group:')
                    ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'prefix:', prepend: true)->getAttributes())
                    ->merge(TALLKit::attributesAfter(attributes: $attributes, prefix: 'suffix:', prepend: true)->getAttributes())
                "
                :$prefix
                :$suffix
                :$size
            >
                {{ $slot }}
            </tk:field.group>
        @else
            {{ $slot }}
        @endif

        <tk:text
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'help:')->merge(['id' => $id ? $id.'-help' : null])"
            :label="$help"
            :$size
        />

        @if ($showError !== false)
            <tk:error
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'error:')->merge(['id' => $id ? $id.'-error' : null])"
                :$name
                :$size
            />
        @endif
    </tk:field>
@else
    {{ $slot }}
@endif
