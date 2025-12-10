<section {{ $attributes->whereDoesntStartWith(['header:', 'title:', 'icon', 'badge', 'subtitle:', 'actions:', 'separator:', 'content:'])->classes([
    'space-y-6',
    '[&:has([data-tallkit-section-content]>:is([data-tallkit-card],[data-tallkit-table-container]))>[data-tallkit-separator]]:hidden' => !$separator,
]) }}>
    @if ($title || $subtitle || isset($header) || isset($actions))
        <div {{ $attributesAfter('header:')->classes('flex justify-between items-start gap-4') }}>
            @if ($title || $subtitle || isset($header))
                <div class="flex-1">
                    <tk:heading
                        :attributes="$attributesAfter('title:')
                            ->merge($attributesAfter('icon', prepend: true)->getAttributes())
                            ->merge($attributesAfter('badge', prepend: true)->getAttributes())"
                        :label="$title"
                        :$size
                    />

                    <tk:text
                        :attributes="$attributesAfter('subtitle:')"
                        :label="$subtitle"
                        :$size
                    />

                    {{ $header ?? '' }}
                </div>
            @endif

            @isset ($actions)
                <div {{ $attributesAfter('actions:')->classes('ms-auto shrink-0 flex items-center gap-2') }}>
                    {{ $actions }}
                </div>
            @endisset
        </div>

        @if ($separator || ($separator === null && ($title && ($subtitle || isset($header)) || isset($actions)) && ($slot->hasActualContent() || $content)))
            <tk:separator :attributes="$attributesAfter('separator:')" />
        @endif
    @endif

    @if ($slot->hasActualContent() || $content)
        <div {{ $attributesAfter('content:') }}>
            {{ $slot->isEmpty() ? __($content) : $slot }}
        </div>
    @endif
</section>
