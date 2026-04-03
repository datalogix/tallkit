@props([
    'size' => null,
    'icon' => null,
    'prepend' => null,
    'title' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
])
@if ($icon || $title || $description || $actions || $slot->hasActualContent())
    <div {{ $attributes
        ->whereDoesntStartWith(['container:', 'icon:', 'title:', 'description:', 'list:', 'actions:'])
        ->classes(
            'flex-1 flex gap-2',
            $prepend || $title || $append ? 'items-start' : 'items-center',
            TALLKit::fontSize(size: $size),
        )
    }}>
        @if (TALLKit::isSlot($icon))
            <div {{ TALLKit::attributesAfter($attributes, 'icon:') }}>
                {{ $icon }}
            </div>
        @elseif ($icon)
            <tk:icon
                :attributes="TALLKit::attributesAfter($attributes, 'icon:')"
                :$icon
                :$size
            />
        @endif

        <div {{ TALLKit::attributesAfter($attributes, 'container:')->classes('flex-1 space-y-2') }}>
            {{ $prepend }}

            <tk:heading
                :attributes="TALLKit::attributesAfter($attributes, 'title:')"
                :label="$title"
                :$size
            />

            @if (is_string($description) || $slot->hasActualContent())
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'description:')"
                    :label="is_string($description) ? $description : null"
                    :$size
                >
                    {{ $slot }}
                </tk:text>
            @endif

            @if (is_array($description))
                <tk:list
                    :attributes="TALLKit::attributesAfter($attributes, 'list:')"
                    :items="$description"
                    :$size
                />
            @endif

            {{ $append }}
        </div>

        @if ($actions)
            <div {{ TALLKit::attributesAfter($attributes, 'actions:')->classes('shrink-0 flex items-center gap-2') }}>
                {{ $actions }}
            </div>
        @endif
    </div>
@endif
