@props([
    'size' => null,
    'image' => null,
    'alt' => null,
    'header' => null,
    'footer' => null,
    'prepend' => null,
    'title' => null,
    'subtitle' => null,
    'description' => null,
    'append' => null,
    'actions' => null,
    'separator' => null,
    'content' => null,
])
<div
    {{
        $attributes
            ->dataKey('card')
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
                TALLKit::fontSize(size: $size),
                TALLKit::roundedSize(size: $size, mode: 'large'),
            )
    }}
>
    @if ($image)
        <img
            {{ TALLKit::attributesAfter($attributes, 'image:')->classes('w-full object-cover') }}
            src="{{ $image }}"
            alt="{{ __($alt ?? (string) $title) }}"
        />
    @endif

    {{ $header }}

    <tk:section
        :attributes="
            TALLKit::attributesAfter($attributes,
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
