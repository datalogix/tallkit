<section {{ $attributes->whereDoesntStartWith(['header:', 'title:', 'subtitle:', 'actions:']) }} data-tallkit-section>
    @if ($title || $subtitle || isset($header) || isset($actions))
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            @if ($title || $subtitle || isset($header))
                <header {{ $attributesAfter('header:')->classes('flex-1') }} data-tallkit-section-header>
                    @if ($title)
                        <tk:heading :attributes="$attributesAfter('title:')" data-tallkit-section-title>
                            {!! $isSlot($title) ? $title : __($title) !!}
                        </tk:heading>
                    @endif

                    @if ($subtitle)
                        <tk:text :attributes="$attributesAfter('subtitle:')" data-tallkit-section-subtitle>
                            {!! $isSlot($subtitle) ? $subtitle : __($subtitle) !!}
                        </tk:text>
                    @endif

                    {{ $header ?? '' }}
                </header>
            @endif

            @isset ($actions)
                <div {{ $attributesAfter('actions:')->classes('shrink-0') }} data-tallkit-section-actions>
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</section>
