<div {{
    $attributes
        ->whereDoesntStartWith([
            'image:', 'section:',
            'icon', 'badge', 'container:', 'list:', 'title:', 'subtitle:', 'separator:', 'content:', 'actions:'
        ])
        ->classes(
            '
                [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-800
                border border-zinc-100 dark:border-white/10
                overflow-hidden shadow-sm
            ',
            $fontSize(size: $size),
            $roundedSize(size: $size, mode: 'large'),
        )
}}>
    @if ($image)
        <img
            {{ $attributesAfter('image:')->classes('w-full object-cover') }}
            src="{{ $image }}"
            alt="{{ __($alt ?? (string) $title) }}"
        />
    @endif

    {{ $header }}

    <tk:section
        :attributes="
            $attributesAfter(
                prefix: 'section:',
                prepend: ['icon', 'badge', 'container:', 'list:', 'title:', 'subtitle:', 'separator:', 'content:', 'actions:']
            )->classes('p-6')
        "
        :$size
        :$prepend
        :$title
        :$subtitle
        :$description
        :$append
        :$actions
        :$separator
        :$content
    >
        {{ $slot }}
    </tk:section>

    {{ $footer }}
</div>
