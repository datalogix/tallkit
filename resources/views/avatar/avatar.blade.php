@aware(['size', 'square'])
@props([
    'size' => null,
    'square' => null,
    'alt' => null,
    'src' => null,
    'initials' => null,
    'icon' => null,
    'tooltip' => null,
    'color' => null,
    'ttl' => null,
])
@php

[$user, $name, $email, $username] = TALLKit::resolveUserContext($attributes);
$initials = TALLKit::generateInitials($initials ?? $name, $attributes->pluck('initials:single'));
$src ??= TALLKit::findAvatar($email ?? $username, $ttl);

if ($color === 'auto') {
    $colors = TALLKit::colors();
    $colorSeed = $attributes->pluck('color:seed') ?? $name ?? $icon ?? $initials;
    $hash = crc32((string) $colorSeed);
    $color = $colors[$hash % count($colors)];
}

if ($tooltip === true) {
    $tooltip = $name ?? false;
}

@endphp
<tk:element
    name="avatar"
    :$tooltip
    :attributes="$attributes
        ->whereDoesntStartWith(['image:', 'initials:', 'icon:'])
        ->classes(
            '
                justify-center
                relative flex-none isolate
                after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/5 dark:after:inset-ring-white/5
                [:where(&)]:bg-zinc-200 dark:[:where(&)]:bg-zinc-800
                [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white
            ',
            TALLKit::fontSize(size: $size, weight: true),
            TALLKit::roundedSize(size: $square ? $size : 'full', after: true),
            TALLKit::widthHeight(size: $size, mode: 'large'),
            match ($color) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'inverse' => 'text-white bg-zinc-800 dark:text-zinc-800 dark:bg-white',
                'filled' => 'bg-zinc-800/5 dark:bg-white/10',
                'outline' => '',
                'ghost' => 'bg-transparent',
                'subtle' => 'bg-transparent text-zinc-500',
                default => TALLKit::pastelBackground($color) ?? '',
            },
        )
    "
>
    @if ($src)
        <img
            {{
                TALLKit::attributesAfter($attributes, 'image:')
                    ->classes(TALLKit::roundedSize(size: $square ? $size : 'full'))
                    ->merge(['src' => $src, 'alt' => (string) ($alt ?? $name)])
            }}
        />
    @elseif (($initials || $slot->hasActualContent()) && !$icon)
        <span {{ TALLKit::attributesAfter($attributes, 'initials:')->classes('select-none truncate m-px') }}>
            {{ $initials ?: $slot }}
        </span>
    @else
        <tk:icon
            :attributes="TALLKit::attributesAfter($attributes, 'icon:')->classes('shrink-0 opacity-75')"
            :icon="is_string($icon) ? $icon : 'user'"
            :$size
        />
    @endif
</tk:element>
