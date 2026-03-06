@if ($label || $description || $help || $prefix || $suffix)
    <tk:field :$variant :$align :attributes="$attributesAfter('field:')">
        <tk:label
            :attributes="$attributesAfter('label:')
                ->merge($attributesAfter('info:', prepend: true)->getAttributes())
                ->merge($attributesAfter('badge:', prepend: true)->getAttributes())
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
            :attributes="$attributesAfter('description:')"
            :label="$description"
            :$size
        />

        @if ($variant !== 'inline' && ($prefix || $suffix))
            <tk:field.group
                :attributes="$attributesAfter('group:')
                    ->merge($attributesAfter('prefix:', prepend: true)->getAttributes())
                    ->merge($attributesAfter('suffix:', prepend: true)->getAttributes())
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
            :attributes="$attributesAfter('help:')"
            :label="$help"
            :$size
        />

        @if ($showError !== false)
            <tk:error
                :attributes="$attributesAfter('error:')"
                :$name
                :$size
            />
        @endif
    </tk:field>
@else
    {{ $slot }}
@endif
