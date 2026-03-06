<section {{ $attributes->whereDoesntStartWith([
        'header:', 'container:', 'title:', 'icon', 'badge', 'subtitle:', 'list:', 'actions:',
        'separator:', 'content:',
    ])
    ->classes([
        '[:where(&)]:space-y-6',
        '[&:has([data-tallkit-section-content]>:is([data-tallkit-card],[data-tallkit-table-container]))>[data-tallkit-separator]]:hidden' => !$separator,
        $fontSize(size: $size),
    ])
}}>
    @if ($title || $subtitle || $description || $append || $actions)
        <tk:content
            :attributes="$attributesAfter('header:', prepend: ['container:', 'title:', 'description:' => 'subtitle:', 'list:', 'actions:'])
                ->merge($attributesAfter('icon', prepend: 'title:icon')->getAttributes())
                ->merge($attributesAfter('badge', prepend: 'title:badge')->getAttributes())
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
            <tk:separator :attributes="$attributesAfter('separator:')" />
        @endif
    @endif

    @if ($slot->hasActualContent() || $content)
        <div {{ $attributesAfter('content:')->classes('space-y-6') }}>
            {{ __($content) }}
            {{ $slot }}
        </div>
    @endif
</section>
