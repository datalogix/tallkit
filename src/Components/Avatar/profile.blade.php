<div {{ $attributesAfter('container:')->classes('flex items-center gap-2.5') }}>
    <tk:avatar
        :attributes="$attributes->whereDoesntStartWith(['container:', 'content:', 'name:', 'description:'])"
        :$size
        :src="$image"
    />

    @if ($name || $description || $slot->hasActualContent())
        <div {{ $attributesAfter('content:')->classes('grid flex-1 -space-y-px! truncate') }}>
            @if ($name)
                <tk:heading
                    :attributes="$attributesAfter('name:')->classes('truncate block')"
                    :label="$name"
                    :size="$adjustSize(move: -2)"
                />
            @endif

            @if ($slot->hasActualContent() || $description)
                <tk:text
                    as="span"
                    :attributes="$attributesAfter('description:')->classes('truncate block')"
                    :label="$description"
                    :size="$adjustSize()"
                >
                    {{ $slot }}
                </tk:text>
            @endif
        </div>
    @endif
</div>
