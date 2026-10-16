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
$singleInitials = $attributes->pluck('initials:single');
$initials = match (true) {
    has_content($initials) => trim((string) $initials),
    $slot->hasActualContent() => null,
    default => TALLKit::avatarInitials(value: $name ?? $email ?? $username, singleInitials: $singleInitials),
};
$src ??= data_get($user, 'image') ?: TALLKit::avatarUrl(value: $email ?? $username, ttl: $ttl);

if ($color === 'auto') {
    $colors = TALLKit::colors();
    $colorSeed = $attributes->pluck('color:seed') ?? $name ?? $icon ?? $initials;
    $hash = crc32((string) $colorSeed);
    $color = $colors[$hash % count($colors)];
}

if ($tooltip === true) {
    $tooltip = $name ?? false;
}

$avatarName = $alt ?? $name ?? string_value($tooltip) ?? $email ?? $username;

@endphp
<tk:element
    kind="avatar"
    :$tooltip
    :attributes="$attributes->withProps(get_defined_vars(), ['ariaLabel' => null, 'iconDot' => null, 'iconTrailing' => null])
        ->whereDoesntStartWith(['image:', 'initials:', 'icon:'])
        ->merge($alt === '' ? ['aria-hidden' => 'true'] : ($avatarName ? ['role' => $attributes->hasAny(['href', 'route', 'action', 'type']) ? null : 'img', 'aria-label' => (string) $avatarName] : []))
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
                'outline' => 'bg-transparent after:inset-ring-zinc-300 dark:after:inset-ring-zinc-600',
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
                    ->classes('size-full object-cover', TALLKit::roundedSize(size: $square ? $size : 'full'))
                    ->merge([
                        'src' => $src,
                        'loading' => 'lazy',
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
            :attributes="$attributes->prefixed('icon:')->classes('shrink-0 opacity-75')"
            :hidden="(bool) $src"
            :icon="string_value($icon, 'user')"
            :$size
        />
    @endif
</tk:element>
