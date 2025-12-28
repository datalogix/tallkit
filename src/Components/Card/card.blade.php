<div {{
    $attributes->whereDoesntStartWith(['image:', 'section:', 'icon', 'badge', 'title:', 'subtitle:', 'separator:', 'content:', 'actions:'])
        ->classes(
            '[:where(&)]:space-y-6',
            '[:where(&)]:bg-white dark:[:where(&)]:bg-zinc-800',
            'border border-zinc-100 dark:border-white/10',
            'overflow-hidden shadow-sm',
            match($size) {
                'xs' => '[:where(&)]:rounded-md',
                'sm' => '[:where(&)]:rounded-md',
                default => '[:where(&)]:rounded-lg',
                'lg' => '[:where(&)]:rounded-lg',
                'xl' => '[:where(&)]:rounded-lg',
                '2xl' => '[:where(&)]:rounded-xl',
                '3xl' => '[:where(&)]:rounded-xl',
            }
        )
}}>
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
            ->merge($attributesAfter('actions:', prepend: true)->getAttributes())
            ->classes(match($size) {
                'xs' => '[:where(&)]:p-4',
                'sm' => '[:where(&)]:p-4',
                default => '[:where(&)]:p-6',
                'lg' => '[:where(&)]:p-6',
                'xl' => '[:where(&)]:p-6',
                '2xl' => '[:where(&)]:p-8',
                '3xl' => '[:where(&)]:p-8',
            })
        "
        :$title
        :$subtitle
        :$separator
        :$size
        :$content
    >
        @isset ($description)
            <x-slot:description>
                {{ $description }}
            </x-slot:description>
        @endisset

        @isset ($actions)
            <x-slot:actions :attributes="$actions->attributes">
                {{ $actions }}
            </x-slot:actions>
        @endisset

        {{ $slot }}
    </tk:section>

    {{ $footer ?? '' }}
</div>
