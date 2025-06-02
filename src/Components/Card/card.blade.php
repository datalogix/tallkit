<div {{ $attributes->whereDoesntStartWith(['image:', 'container:', 'title:', 'subtitle:', 'content:'])->classes(
    'bg-white dark:bg-white/10',
    'border border-zinc-200 dark:border-white/10',
    'overflow-hidden shadow-sm [:where(&)]:rounded-xl'
) }} data-tallkit-card>
    @if ($image)
        <img src="{{ $image }}" {{ $attributesAfter('image:')->merge(['alt' => __((string) $title)])->classes('w-full object-cover') }} data-tallkit-card-image />
    @endif

    {{ $header ?? '' }}

    <div {{ $attributesAfter('container:')->classes('[:where(&)]:p-6 flex flex-col gap-6') }}
        data-tallkit-card-container>

        @if ($title || $subtitle || isset($actions))
            <div class="flex justify-between gap-6">
                <div class="flex-1">
                    @if ($title)
                        <tk:heading :attributes="$attributesAfter('title:')" data-tallkit-card-title>
                            {!! $isSlot($title) ? $title : __($title) !!}
                        </tk:heading>
                    @endif

                    @if ($subtitle)
                        <tk:text :attributes="$attributesAfter('subtitle:')" data-tallkit-card-subtitle>
                            {!! $isSlot($subtitle) ? $subtitle : __($subtitle) !!}
                        </tk:text>
                    @endif
                </div>

                @isset ($actions)
                    <div {{ $attributesAfter('actions:')->classes('shrink-0') }} data-tallkit-card-actions>
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        @endif

        @if ($slot->isNotEmpty() || $text)
            <div {{ $attributesAfter('content:') }} data-tallkit-card-content>
                {{ $slot->isEmpty() ? __($text) : $slot }}
            </div>
        @endif
    </div>

    {{ $footer ?? '' }}
</div>
