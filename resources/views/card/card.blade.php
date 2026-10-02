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
            {{ $attributes->prefixed('image:')->classes('w-full object-cover') }}
            src="{{ $image }}"
            alt="{{ $alt === null ? '' : __($alt) }}"
        />
    @endif

    {{ $header }}

    <tk:section
        :attributes="
            $attributes->prefixed('section:',
                with: ['icon', 'badge', 'container:', 'list:', 'title:', 'subtitle:', 'separator:', 'content:', 'actions:']
            )->classes(TALLKit::padding(size: $size, mode: 'largest'))
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
