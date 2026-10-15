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
$hasIcon = has_content($icon);
$hasPrepend = has_content($prepend);
$hasTitle = has_content($title);
$hasDescription = has_content($description);
$hasAppend = has_content($append);
$hasActions = has_content($actions);
$descriptionIsString = is_string($description) && filled($description);

@endphp
@if ($hasIcon || $hasPrepend || $hasTitle || $hasDescription || $hasAppend || $hasActions || $hasContent)
    <div {{ $attributes
        ->whereDoesntStartWith(['container:', 'icon:', 'title:', 'description:', 'list:', 'actions:'])
        ->classes(
            'flex-1 flex',
            collect([$hasPrepend, $hasTitle, $hasDescription, $hasAppend, $hasContent])->filter()->count() > 1 ? 'items-start' : 'items-center',
            TALLKit::fontSize(size: $size),
            TALLKit::gap(size: $size),
        )
    }}>
        @if ($hasIcon && TALLKit::isSlot(slot: $icon))
            @php($iconAttrs = $attributes->prefixed('icon:'))
            <div {{
                $iconAttrs->classes('shrink-0')->when(
                    ! $iconAttrs->has('aria-label') && ! $iconAttrs->has('aria-labelledby'),
                    fn ($attrs) => $attrs->merge(['aria-hidden' => 'true'])
                )
            }}>
                {{ $icon }}
            </div>
        @elseif ($hasIcon)
            <tk:icon
                :attributes="$attributes->prefixed('icon:')"
                :$icon
                :$size
            />
        @endif

        <div
            {{
                $attributes->prefixed('container:')
                    ->classes(
                        'flex-1',
                        TALLKit::spaceBlock(size: $size)
                    )
            }}
        >
            {{ $prepend }}

            <tk:heading
                :attributes="$attributes->prefixed('title:')"
                :label="$title"
                :$size
            />

            @php($descriptionIsContent = $description !== null && ! is_string($description) && ! is_array($description) && filled((string) $description))
            @if ($descriptionIsString || $descriptionIsContent)
                <tk:text
                    :attributes="$attributes->prefixed('description:')"
                    :label="$descriptionIsString ? $description : null"
                    :$size
                >
                    @if ($descriptionIsContent)
                        {{ $description }}
                    @endif
                </tk:text>
            @endif

            @if ($hasContent)
                <tk:text
                    :attributes="$attributes->prefixed('description:')->when($descriptionIsString || $descriptionIsContent, fn ($attrs) => $attrs->except('id'))"
                    :$size
                >
                    {{ $slot }}
                </tk:text>
            @endif

            @if (is_array($description))
                <tk:list
                    :attributes="$attributes->prefixed('list:')"
                    :items="$description"
                    :$size
                />
            @endif

            {{ $append }}
        </div>

        @if ($hasActions)
            <div {{ $attributes->prefixed('actions:')->classes(
                'shrink-0 flex items-center',
                TALLKit::gap(size: $size)
            ) }}>
                {{ $actions }}
            </div>
        @endif
    </div>
@endif
