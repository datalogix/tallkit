<div {{
    $attributes
        ->whereDoesntStartWith(['image:', 'container:', 'title:', 'subtitle:', 'separator:', 'content:'])
        ->classes(
            'bg-white dark:bg-zinc-800',
            'border border-zinc-300 dark:border-white/10',
            'overflow-hidden shadow-sm [:where(&)]:rounded-xl'
        )
}}>
    @if ($image)
        <img
            {{ $attributesAfter('image:')->classes('w-full object-cover') }}
            src="{{ $image }}"
            alt="{{ __((string) $title) }}"
        />
    @endif

    {{ $header ?? '' }}

    <div {{ $attributesAfter('container:')->classes('[:where(&)]:p-6 flex flex-col gap-6') }}>
        @if ($title || $subtitle || isset($actions))
            <div class="flex justify-between gap-6">
                <div class="flex-1">
                    <tk:heading
                        :attributes="$attributesAfter('title:')"
                        :label="$title"
                        :$size
                    />

                    <tk:text
                        :attributes="$attributesAfter('subtitle:')"
                        :label="$subtitle"
                        :$size
                    />
                </div>

                @isset ($actions)
                    <div {{ $attributesAfter('actions:')->classes('shrink-0') }}>
                        {{ $actions }}
                    </div>
                @endisset
            </div>

            @if ($separator || ($separator === null && ($title && ($subtitle || isset($actions))) && ($slot->isNotEmpty() || $content)))
                <tk:separator :attributes="$attributesAfter('separator:')" />
            @endif
        @endif

        @if ($slot->isNotEmpty() || $content)
            <div {{ $attributesAfter('content:') }}>
                {{ $slot->isEmpty() ? __($content) : $slot }}
            </div>
        @endif
    </div>

    {{ $footer ?? '' }}
</div>
