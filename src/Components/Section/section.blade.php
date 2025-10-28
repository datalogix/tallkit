<section {{ $attributes->whereDoesntStartWith(['header:', 'title:', 'subtitle:', 'actions:', 'separator:'])
    ->classes([
        'space-y-6',
        '[&:has(>[data-tallkit-separator]+:is([data-tallkit-card],[data-tallkit-table-container]))>[data-tallkit-separator]]:hidden' => !$separator
    ])
}}>
    @if ($title || $subtitle || isset($header) || isset($actions))
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            @if ($title || $subtitle || isset($header))
                <header {{ $attributesAfter('header:')->classes('flex-1') }}>
                    <tk:heading
                        :attributes="$attributesAfter('title:')"
                        :label="$title"
                    />

                    <tk:text
                        :attributes="$attributesAfter('subtitle:')"
                        :label="$subtitle"
                    />

                    {{ $header ?? '' }}
                </header>
            @endif

            @isset ($actions)
                <div {{ $attributesAfter('actions:')->classes('shrink-0') }}>
                    {{ $actions }}
                </div>
            @endisset
        </div>

        @if ($separator || ($separator === null && ($title && ($subtitle || isset($header) || isset($actions))) && $slot->isNotEmpty()))
            <tk:separator :attributes="$attributesAfter('separator:')" />
        @endif
    @endif

    {{ $slot }}
</section>

