@props([
    ...TALLKit::fieldProps(),
    'inline' => null,
    'align' => null,
    'name' => null,
])
@php

$hasPrefix = $prefix || TALLKit::attributesAfter($attributes, 'prefix:')->isNotEmpty();
$hasSuffix = $suffix || TALLKit::attributesAfter($attributes, 'suffix:')->isNotEmpty();

@endphp
@if ($label || $description || $help || $hasPrefix || $hasSuffix)
    <tk:field :$inline :$align :attributes="TALLKit::attributesAfter($attributes, 'field:')">
        <tk:label
            :attributes="TALLKit::attributesAfter($attributes, 'label:')
                ->merge(TALLKit::attributesAfter($attributes, 'info:', prepend: true)->getAttributes())
                ->merge(TALLKit::attributesAfter($attributes, 'badge:', prepend: true)->getAttributes())
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
            :attributes="TALLKit::attributesAfter($attributes, 'description:')->merge(['id' => $id ? $id.'-description' : null])"
            :label="$description"
            :$size
        />

        @if (!$inline && ($hasPrefix || $hasSuffix))
            <tk:field.group
                :attributes="TALLKit::attributesAfter($attributes, 'group:')
                    ->merge(TALLKit::attributesAfter($attributes, 'prefix:', prepend: true)->getAttributes())
                    ->merge(TALLKit::attributesAfter($attributes, 'suffix:', prepend: true)->getAttributes())
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
            :attributes="TALLKit::attributesAfter($attributes, 'help:')->merge(['id' => $id ? $id.'-help' : null])"
            :label="$help"
            :$size
        />

        @if ($showError !== false)
            <tk:error
                :attributes="TALLKit::attributesAfter($attributes, 'error:')->merge(['id' => $id ? $id.'-error' : null])"
                :$name
                :$size
            />
        @endif
    </tk:field>
@else
    {{ $slot }}
@endif
