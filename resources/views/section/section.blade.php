@props([
    'size' => null,
    'prepend' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'separator' => null,
    'content' => null,
])
@php

$hasTitle = has_content($title);
$hasDetails = has_content($subtitle, $description, $append);
$hasActions = has_content($actions);

@endphp
<section
    {{
        $attributes
            ->dataKey('section')
            ->whereDoesntStartWith([
                'header:', 'container:', 'title:', 'icon', 'badge', 'subtitle:', 'list:', 'actions:',
                'separator:', 'content:',
            ])
            ->classes([
                '[&:has([data-tallkit-section-content]>:is([data-tallkit-card],[data-tallkit-table-container]))>[data-tallkit-separator]]:hidden' => ! $separator,
                TALLKit::fontSize(size: $size),
                TALLKit::generateClassBySize(size: $size, name: 'space-y', values: [4, 5, 6, 7, 8, 9, 10]),
            ])
    }}
>
    @if ($hasTitle || $hasDetails || $hasActions)
        <tk:content
            :attributes="TALLKit::attributesMerge(
                $attributes->prefixed('header:', with: ['container:', 'title:', 'description:' => 'subtitle:', 'list:', 'actions:']),
                $attributes->prefixed('icon', keepPrefix: 'title:icon'),
                $attributes->prefixed('badge', keepPrefix: 'title:badge'),
            )"
            :$size
            :title:icon:size="$size"
            :title:badge:size="$size"
            :icon="false"
            :$prepend
            :$title
            :description="$subtitle"
            :$actions
        >
            <x-slot:append>
                @if (is_string($description))
                    <tk:text :label="$description" :$size />
                @else
                    {{ $description }}
                @endif
                {{ $append }}
            </x-slot:append>
        </tk:content>

        @if (
            $separator ||
            (
                $separator === null &&
                ($hasTitle && $hasDetails || $hasActions) &&
                ($slot->hasActualContent() || has_content($content))
            )
        )
            <tk:separator :attributes="$attributes->prefixed('separator:')" />
        @endif
    @endif

    @if ($slot->hasActualContent() || $content)
        <div
            {{
                $attributes->prefixed('content:')
                    ->dataKey('section-content')
                    ->classes(TALLKit::generateClassBySize(size: $size, name: 'space-y', values: [4, 5, 6, 7, 8, 9, 10]))
            }}
        >
            {{ is_string($content) ? __($content) : $content }}
            {{ $slot }}
        </div>
    @endif
</section>
