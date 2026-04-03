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
<section
    {{
        $attributes
            ->dataKey('section')
            ->whereDoesntStartWith([
                'header:', 'container:', 'title:', 'icon', 'badge', 'subtitle:', 'list:', 'actions:',
                'separator:', 'content:',
            ])
            ->classes([
                '[:where(&)]:space-y-6',
                '[&:has([data-tallkit-section-content]>:is([data-tallkit-card],[data-tallkit-table-container]))>[data-tallkit-separator]]:hidden' => !$separator,
                TALLKit::fontSize(size: $size),
            ])
    }}
>
    @if ($title || $subtitle || $description || $append || $actions)
        <tk:content
            :attributes="TALLKit::attributesAfter($attributes, 'header:', prepend: ['container:', 'title:', 'description:' => 'subtitle:', 'list:', 'actions:'])
                ->merge(TALLKit::attributesAfter($attributes, 'icon', prepend: 'title:icon')->getAttributes())
                ->merge(TALLKit::attributesAfter($attributes, 'badge', prepend: 'title:badge')->getAttributes())
            "
            :$size
            :icon="false"
            :$prepend
            :$title
            :description="$subtitle"
            :$actions
        >
            <x-slot:append>
                {{ $description }}
                {{ $append }}
            </x-slot:append>
        </tk:content>

        @if (
            $separator ||
            (
                $separator === null &&
                ($title && ($subtitle || $description || $append) || $actions) &&
                ($slot->hasActualContent() || $content)
            )
        )
            <tk:separator :attributes="TALLKit::attributesAfter($attributes, 'separator:')" />
        @endif
    @endif

    @if ($slot->hasActualContent() || $content)
        <div {{ TALLKit::attributesAfter($attributes, 'content:')->classes('space-y-6') }}>
            {{ __($content) }}
            {{ $slot }}
        </div>
    @endif
</section>
