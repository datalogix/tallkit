@props([
    'variant' => null,
    'align' => null,
    'name' => null,
    'id' => null,
    'label' => null,
    'labelAppend' => null,
    'labelPrepend' => null,
    'description' => null,
    'help' => null,
    'badge' => null,
    'info' => null,
    'prefix' => null,
    'suffix' => null,
    'size' => null,
    'showError' => null,
])
@if ($label || $description || $help || $prefix || $suffix)
    <tk:field :$variant :$align :attributes="TALLKit::attributesAfter($attributes, 'field:')">
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
            :attributes="TALLKit::attributesAfter($attributes, 'description:')"
            :label="$description"
            :$size
        />

        @if ($variant !== 'inline' && ($prefix || $suffix))
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
            :attributes="TALLKit::attributesAfter($attributes, 'help:')"
            :label="$help"
            :$size
        />

        @if ($showError !== false)
            <tk:error
                :attributes="TALLKit::attributesAfter($attributes, 'error:')"
                :$name
                :$size
            />
        @endif
    </tk:field>
@else
    {{ $slot }}
@endif
