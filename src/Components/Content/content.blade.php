@if ($icon || $title || $description || $actions || $slot->hasActualContent())
    <div {{ $attributes
        ->whereDoesntStartWith(['container:', 'icon:', 'title:', 'description:', 'list:', 'actions:'])
        ->classes(
            'flex-1 flex gap-2 items-start',
            $fontSize(size: $size),
        )
    }}>
        @if ($isSlot($icon))
            <div {{ $attributesAfter('icon:') }}>
                {{ $icon }}
            </div>
        @elseif ($icon)
            <tk:icon
                :attributes="$attributesAfter('icon:')"
                :$icon
                :$size
            />
        @endif

        <div {{ $attributesAfter('container:')->classes('flex-1 space-y-2') }}>
            {{ $prepend }}

            <tk:heading
                :attributes="$attributesAfter('title:')"
                :label="$title"
                :$size
            />

            @if (is_string($description) || $slot->hasActualContent())
                <tk:text
                    :attributes="$attributesAfter('description:')"
                    :label="is_string($description) ? $description : null"
                    :$size
                >
                    {{ $slot }}
                </tk:text>
            @endif

            @if (is_array($description))
                <tk:list
                    :attributes="$attributesAfter('list:')"
                    :items="$description"
                    :$size
                />
            @endif

            {{ $append }}
        </div>

        @if ($actions)
            <div {{ $attributesAfter('actions:')->classes('shrink-0 flex items-center gap-2') }}>
                {{ $actions }}
            </div>
        @endif
    </div>
@endif
