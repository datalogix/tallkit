@if ($label || $description || $help || $prefix || $suffix)
    <tk:field :$variant :$align :attributes="$attributesAfter('field:')">
        @if ($variant === 'inline' && (!$align || Str::contains($align, 'left', true)))
            {{ $slot }}
        @endif

        <tk:label
            :attributes="$attributesAfter('label:')
                ->merge($attributesAfter('information:', prepend: true)->getAttributes())
                ->merge($attributesAfter('badge:', prepend: true)->getAttributes())
            "
            :for="$id"
            :$label
            :$labelAppend
            :$labelPrepend
            :$size
            :$information
            :$badge
        />

        @if ($variant === 'inline' && Str::contains($align, 'right', true))
            {{ $slot }}
        @endif

        <tk:text
            :attributes="$attributesAfter('description:')"
            :label="$description"
            :$size
        />

        @if ($variant !== 'inline')
            @if ($prefix || $suffix)
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
