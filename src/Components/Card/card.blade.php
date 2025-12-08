<div {{ $attributes->whereDoesntStartWith(['image:', 'section:', 'icon', 'badge', 'title:', 'subtitle:', 'separator:', 'content:'])->classes(
    'bg-white dark:bg-zinc-800',
    'border border-zinc-100 dark:border-white/10',
    'overflow-hidden shadow-sm [:where(&)]:rounded-lg',
) }}>
    @if ($image)
        <img
            {{ $attributesAfter('image:')->classes('w-full object-cover') }}
            src="{{ $image }}"
            alt="{{ __($alt ?? (string) $title) }}"
        />
    @endif

    {{ $header ?? '' }}

    <tk:section
        :attributes="$attributesAfter('section:')
            ->merge($attributesAfter('icon', prepend: true)->getAttributes())
            ->merge($attributesAfter('badge', prepend: true)->getAttributes())
            ->merge($attributesAfter('title:', prepend: true)->getAttributes())
            ->merge($attributesAfter('subtitle:', prepend: true)->getAttributes())
            ->merge($attributesAfter('separator:', prepend: true)->getAttributes())
            ->merge($attributesAfter('content:', prepend: true)->getAttributes())
            ->classes('[:where(&)]:p-6')
        "
        :$title
        :$subtitle
        :$separator
        :$size
        :$content
    >
        @isset ($actions)
            <x-slot:actions>
                {{ $actions }}
            </x-slot:actions>
        @endisset

        {{ $slot }}
    </tk:section>

    {{ $footer ?? '' }}
</div>
