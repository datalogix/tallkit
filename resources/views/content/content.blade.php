@props([
    'size' => null,
    'icon' => null,
    'prepend' => null,
    'title' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
])
@php

$hasContent = $slot->hasActualContent();

@endphp
@if ($icon || $prepend || $title || $description || $append || $actions || $hasContent)
    <div {{ $attributes
        ->whereDoesntStartWith(['container:', 'icon:', 'title:', 'description:', 'list:', 'actions:'])
        ->classes(
            'flex-1 flex',
            collect([$prepend, $title, $description, $append, $hasContent])->filter()->count() > 1 ? 'items-start' : 'items-center',
            TALLKit::fontSize(size: $size),
            TALLKit::gap(size: $size),
        )
    }}>
        @if (TALLKit::isSlot($icon))
            @php($iconAttrs = TALLKit::attributesAfter($attributes, 'icon:'))
            <div {{
                $iconAttrs->when(
                    !$iconAttrs->has('aria-label') && !$iconAttrs->has('aria-labelledby'),
                    fn ($attrs) => $attrs->merge(['aria-hidden' => 'true'])
                )
            }}>
                {{ $icon }}
            </div>
        @elseif ($icon)
            <tk:icon
                :attributes="TALLKit::attributesAfter($attributes, 'icon:')"
                :$icon
                :$size
            />
        @endif

        <div {{ TALLKit::attributesAfter($attributes, 'container:')->classes('flex-1', TALLKit::spaceBlock(size: $size)) }}>
            {{ $prepend }}

            <tk:heading
                :attributes="TALLKit::attributesAfter($attributes, 'title:')"
                :label="$title"
                :$size
            />

            @if (is_string($description) || $hasContent)
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
            <div {{ TALLKit::attributesAfter($attributes, 'actions:')->classes(
                'shrink-0 flex items-center',
                TALLKit::gap(size: $size)
            ) }}>
                {{ $actions }}
            </div>
        @endif
    </div>
@endif
