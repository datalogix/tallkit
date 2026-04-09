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
])
@php

[$user, $name, $email, $username] = TALLKit::resolveUserContext($attributes);
$initials = TALLKit::generateInitials($initials ?? $name, $attributes->pluck('initials:single'));
$ttl = $attributes->pluck('ttl');
$src ??= TALLKit::findAvatar($email ?? $username, $ttl);

if ($color === 'auto') {
    $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose', 'slate', 'gray', 'zinc', 'stone', 'taupe', 'mauve', 'mist', 'olive'];
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
                after:absolute after:inset-0 after:inset-ring-[1px] after:inset-ring-black/7 dark:after:inset-ring-white/10
                [:where(&)]:bg-zinc-200 dark:[:where(&)]:bg-zinc-800
                [:where(&)]:text-zinc-800 dark:[:where(&)]:text-white
            ',
            TALLKit::fontSize(size: $size, weight: true),
            TALLKit::roundedSize(size: !$square ? 'full' : $size, after: true),
            TALLKit::widthHeight(size: $size, mode: 'large'),
            match ($color) {
                'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
                'inverse' => 'text-white bg-zinc-800 dark:text-zinc-800 dark:bg-white',
                'filled' => 'bg-zinc-800/5 dark:bg-white/10',
                'outline' => '',
                'ghost' => 'bg-transparent',
                'subtle' => 'bg-transparent text-zinc-500',
                default => '',
                'red' => 'bg-red-200 text-red-800',
                'orange' => 'bg-orange-200 text-orange-800',
                'amber' => 'bg-amber-200 text-amber-800',
                'yellow' => 'bg-yellow-200 text-yellow-800',
                'lime' => 'bg-lime-200 text-lime-800',
                'green' => 'bg-green-200 text-green-800',
                'emerald' => 'bg-emerald-200 text-emerald-800',
                'teal' => 'bg-teal-200 text-teal-800',
                'cyan' => 'bg-cyan-200 text-cyan-800',
                'sky' => 'bg-sky-200 text-sky-800',
                'blue' => 'bg-blue-200 text-blue-800',
                'indigo' => 'bg-indigo-200 text-indigo-800',
                'violet' => 'bg-violet-200 text-violet-800',
                'purple' => 'bg-purple-200 text-purple-800',
                'fuchsia' => 'bg-fuchsia-200 text-fuchsia-800',
                'pink' => 'bg-pink-200 text-pink-800',
                'rose' => 'bg-rose-200 text-rose-800',
            },
        )
    "
>
    @if ($src)
        <img
            {{
                TALLKit::attributesAfter($attributes, 'image:')
                    ->classes(TALLKit::roundedSize(size: !$square ? 'full' : $size))
                    ->merge(['src' => $src, 'alt' => $alt ?? $name])
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
