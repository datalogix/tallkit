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

[$user, $name, $email, $username] = TALLKit::userContext(attributes: $attributes);
$initials = TALLKit::avatarInitials(value: $initials ?? $name, singleInitials: $attributes->pluck('initials:single'));
$src ??= TALLKit::avatarUrl(value: $email ?? $username, ttl: $ttl);

if ($color === 'auto') {
    $colors = TALLKit::colors();
    $colorSeed = $attributes->pluck('color:seed') ?? $name ?? $icon ?? $initials;
    $hash = crc32((string) $colorSeed);
    $color = $colors[$hash % count($colors)];
}

if ($tooltip === true) {
    $tooltip = $name ?? false;
}

$avatarName = $alt ?? $name ?? (is_string($tooltip) && $tooltip !== '' ? $tooltip : null);

@endphp
<tk:element
    kind="avatar"
    :$tooltip
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['ariaLabel' => null, 'iconDot' => null, 'iconTrailing' => null])
        ->whereDoesntStartWith(['image:', 'initials:', 'icon:'])
        ->merge($alt === '' ? ['aria-hidden' => 'true'] : ($avatarName ? ['role' => 'img', 'aria-label' => (string) $avatarName] : []))
        ->classes(
            '
                justify-center
                relative flex-none isolate
                after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/5 dark:after:inset-ring-white/5
                [:where(&)]:bg-zinc-200 dark:[:where(&)]:bg-zinc-800
            ',
            TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
            TALLKit::fontSize(size: $size, weight: true),
            TALLKit::roundedSize(size: $square ? $size : 'full', after: true),
            TALLKit::widthHeight(size: $size, mode: 'large'),
            match ($color) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'inverse' => 'text-white bg-zinc-800 dark:text-zinc-800 dark:bg-white',
                'filled' => TALLKit::backgroundNeutral(variant: 'faint'),
                'outline' => '',
                'ghost' => 'bg-transparent',
                'subtle' => 'bg-transparent text-zinc-500',
                default => TALLKit::pastelBackground(color: $color) ?? '',
            },
        )
    "
>
    @if ($src)
        <img
            {{
                $attributes->prefixed('image:')
                    ->classes(TALLKit::roundedSize(size: $square ? $size : 'full'))
                    ->merge([
                        'src' => $src,
                        'alt' => $avatarName ? '' : (string) $alt,
                        'x-data' => '',
                        'x-init' => "const fail = () => { \$el.nextElementSibling?.removeAttribute('hidden'); \$el.remove() }; \$el.complete ? \$el.decode().catch(fail) : \$el.addEventListener('error', fail, { once: true })",
                    ])
            }}
        />
    @endif
    @if (($initials || $slot->hasActualContent()) && ! $icon)
        <span {{ $attributes->prefixed('initials:')->classes('select-none truncate m-px')->merge(['hidden' => (bool) $src]) }}>
            {{ $initials ?: $slot }}
        </span>
    @else
        <tk:icon
            :attributes="$attributes->prefixed('icon:')->classes('shrink-0 opacity-75')->merge(['hidden' => (bool) $src])"
            :icon="is_string($icon) ? $icon : 'user'"
            :$size
        />
    @endif
</tk:element>
