@aware(['size'])
@props([
    'size' => null,
    'type' => null,
    'href' => null,
    'loading' => null,
    'circle' => null,
    'square' => null,
    'variant' => null,
    'label' => null,
    'tooltip' => null,
    'iconTrailing' => null,
    'badge' => null,
    'color' => null,
])
@php

$variant = $variant ?: 'outline';
$isColored = $color
    && TALLKit::isColor($color)
    && ! in_array($color, ['slate', 'gray', 'zinc', 'stone'], true)
    && in_array($variant, ['filled', 'outline', 'ghost', 'subtle'], true);
$hasContent = $slot->hasActualContent() || $label !== null;
$square ??= ! $circle && !($hasContent || $badge !== null);
$isTypeSubmitAndNotDisabledOnRender = $type === 'submit' && ! $attributes->has('disabled');
$isJsMethod = Str::startsWith($attributes->whereStartsWith('wire:click')->first() ?? '', '$js.');
$loading ??= $isTypeSubmitAndNotDisabledOnRender || $attributes->whereStartsWith('wire:click')->isNotEmpty() && ! $isJsMethod;

if ($loading && $type !== 'submit' && ! $isJsMethod) {
    $attributes = $attributes->merge(['wire:loading.attr' => TALLKit::dataKey(name: 'button-loading')]);

    if (! $attributes->has('wire:target') && $target = $attributes->whereStartsWith('wire:click')->first()) {
        $attributes = $attributes->merge(['wire:target' => $target], escape: false);
    }
} else {
    $attributes = $attributes->merge([TALLKit::dataKey(name: 'button-loading') => $loading]);
}

@endphp
<tk:element
    name="button"
    :$href
    :$label
    :$tooltip
    :$iconTrailing
    :$badge
    :type="$type ?? 'button'"
    :icon:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :content:class="$loading && $hasContent ? 'flex-1' : ($badge !== null || $iconTrailing !== null ? 'flex-1' : null)"
    :attributes="$attributes
        ->whereDoesntStartWith(['loading-indicator:', 'loading:'])
        ->classes([
            '
                [:where(&)]:relative [:where(&)]:justify-center
                [:where(&)]:font-medium [:where(&)]:whitespace-nowrap
                [:where(&)]:disabled:opacity-disabled
                [:where(&)]:disabled:cursor-default [:where(&)]:disabled:pointer-events-none
                [:where(&)]:transition [:where(&)]:overflow-hidden
            ',
            TALLKit::fontSize(size: $size),
            TALLKit::gap(size: $size),
            ...match ($variant) {
                'none' => [],
                default => [
                    TALLKit::roundedSize(size: $circle ? 'full': $size),
                    TALLKit::height(size: $size),
                    $square
                        ? TALLKit::width(size: $size)
                        : TALLKit::paddingInline(size: $size, mode: 'largest'),
                ],
            },
            match (true) { // Text color...
                $isColored && in_array($variant, ['filled', 'outline']) => TALLKit::mutedText(color: $color),
                $isColored && $variant === 'ghost' => TALLKit::text(color: $color),
                $isColored && $variant === 'subtle' => '
                    [:where(&)]:text-'.$color.'-400
                    [:where(&)]:hover:text-'.$color.'-600
                    [:where(&)]:[&[data-active]]:text-'.$color.'-600

                    dark:[:where(&)]:text-'.$color.'-300
                    dark:[:where(&)]:hover:text-'.$color.'-400
                    dark:[:where(&)]:[&[data-active]]:text-'.$color.'-400
                ',
                $variant === 'primary' && ! $color => '[:where(&)]:text-[var(--color-accent-foreground)]',
                default => match ($variant) {
                    'accent' => '[:where(&)]:text-[var(--color-accent-foreground)]',
                    'filled', 'outline', 'ghost' => TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
                    'inverse' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-800',
                    'subtle', 'none' => '
                        [:where(&)]:text-zinc-500
                        [:where(&)]:hover:text-zinc-800
                        [:where(&)]:[&[data-active]]:text-zinc-800

                        dark:[:where(&)]:text-zinc-400
                        dark:[:where(&)]:hover:text-white
                        dark:[:where(&)]:[&[data-active]]:text-white
                    ',
                    'amber', 'yellow', 'warning' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-950',
                    default => '[:where(&)]:text-white',
                },
            },
            match (true) { // Border color...
                $isColored && $variant === 'outline' => '[:where(&)]:border [:where(&)]:border-'.$color.'-400/40 dark:[:where(&)]:border-'.$color.'-400/20',
                default => match ($variant) {
                    'outline' => '
                        [:where(&)]:border
                        [:where(&)]:border-b-zinc-300/80

                        [:where(&)]:border-zinc-200
                        [:where(&)]:hover:border-zinc-200
                        [:where(&)]:[&[data-active]]:border-zinc-200

                        dark:[:where(&)]:border-white/10
                        dark:[:where(&)]:hover:border-white/10
                        dark:[:where(&)]:[&[data-active]]:border-white/10
                    ',
                    'inverse', 'filled', 'subtle', 'ghost', 'none' => '',
                    default => '[:where(&)]:border [:where(&)]:border-black/10',
                },
            },
            match (true) { // Background color...
                $isColored && $variant === 'filled' => TALLKit::mutedBackground(color: $color, as: $href ? 'a' : 'button'),
                $isColored && $variant === 'outline' => '
                    [:where(&)]:bg-white
                    [:where(&)]:hover:bg-'.$color.'-400/20
                    [:where(&)]:[&[data-active]]:bg-'.$color.'-400/20

                    dark:[:where(&)]:bg-zinc-700
                    dark:[:where(&)]:hover:bg-'.$color.'-400/40
                    dark:[:where(&)]:[&[data-active]]:bg-'.$color.'-400/40
                ',
                $isColored && $variant === 'ghost' => '
                    [:where(&)]:bg-transparent
                    [:where(&)]:hover:bg-'.$color.'-400/20
                    [:where(&)]:[&[data-active]]:bg-'.$color.'-400/20

                    dark:[:where(&)]:bg-transparent
                    dark:[:where(&)]:hover:bg-'.$color.'-400/40
                    dark:[:where(&)]:[&[data-active]]:bg-'.$color.'-400/40
                ',
                $isColored && $variant === 'subtle' => '
                    [:where(&)]:bg-transparent
                    [:where(&)]:hover:bg-'.$color.'-400/10
                    [:where(&)]:[&[data-active]]:bg-'.$color.'-400/10

                    dark:[:where(&)]:bg-transparent
                    dark:[:where(&)]:hover:bg-'.$color.'-400/20
                    dark:[:where(&)]:[&[data-active]]:bg-'.$color.'-400/20
                ',
                $variant === 'primary' => TALLKit::interactiveBackground(color: $color) ?? '
                    [:where(&)]:bg-[var(--color-accent)]
                    [:where(&)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                    [:where(&)]:[&[data-active]]:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                ',
                default => match ($variant) {
                    'accent' => '
                        [:where(&)]:bg-[var(--color-accent)]
                        [:where(&)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                        [:where(&)]:[&[data-active]]:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                    ',
                    'inverse' => '
                        [:where(&)]:bg-zinc-700
                        [:where(&)]:hover:bg-zinc-600/75
                        [:where(&)]:[&[data-active]]:bg-zinc-600/75

                        dark:[:where(&)]:bg-zinc-200
                        dark:[:where(&)]:hover:bg-zinc-300/75
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-300/75
                    ',
                    'info' => '
                        [:where(&)]:bg-blue-600
                        [:where(&)]:hover:bg-blue-700
                        [:where(&)]:[&[data-active]]:bg-blue-700

                        dark:[:where(&)]:bg-blue-700
                        dark:[:where(&)]:hover:bg-blue-600
                        dark:[:where(&)]:[&[data-active]]:bg-blue-600
                    ',
                    'success' => '
                        [:where(&)]:bg-green-600
                        [:where(&)]:hover:bg-green-700
                        [:where(&)]:[&[data-active]]:bg-green-700

                        dark:[:where(&)]:bg-green-700
                        dark:[:where(&)]:hover:bg-green-600
                        dark:[:where(&)]:[&[data-active]]:bg-green-600
                    ',
                    'danger' => '
                        [:where(&)]:bg-red-600
                        [:where(&)]:hover:bg-red-700
                        [:where(&)]:[&[data-active]]:bg-red-700

                        dark:[:where(&)]:bg-red-700
                        dark:[:where(&)]:hover:bg-red-600
                        dark:[:where(&)]:[&[data-active]]:bg-red-600
                    ',
                    'outline' => '
                        [:where(&)]:bg-white
                        [:where(&)]:hover:bg-zinc-800/5
                        [:where(&)]:[&[data-active]]:bg-zinc-800/5

                        dark:[:where(&)]:bg-zinc-700
                        dark:[:where(&)]:hover:bg-zinc-600/85
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-600/85
                    ',
                    'filled' => '
                        [:where(&)]:bg-zinc-800/5
                        [:where(&)]:hover:bg-zinc-800/15
                        [:where(&)]:[&[data-active]]:bg-zinc-800/15

                        dark:[:where(&)]:bg-white/10
                        dark:[:where(&)]:hover:bg-white/20
                        dark:[:where(&)]:[&[data-active]]:bg-white/20
                    ',
                    'subtle', 'ghost' => '
                        [:where(&)]:bg-transparent
                        [:where(&)]:hover:bg-zinc-800/10
                        [:where(&)]:[&[data-active]]:bg-zinc-800/10

                        dark:[:where(&)]:bg-transparent
                        dark:[:where(&)]:hover:bg-white/10
                        dark:[:where(&)]:[&[data-active]]:bg-white/10
                    ',
                    'none' => 'bg-transparent',
                    default => TALLKit::interactiveBackground(color: $variant === 'warning' ? 'yellow' : $variant) ?? '
                        [:where(&)]:border
                        [:where(&)]:border-b-zinc-300/80

                        [:where(&)]:text-zinc-800
                        [:where(&)]:bg-white
                        [:where(&)]:border-zinc-200
                        [:where(&)]:hover:bg-zinc-800/5
                        [:where(&)]:hover:border-zinc-200
                        [:where(&)]:[&[data-active]]:bg-zinc-800/5
                        [:where(&)]:[&[data-active]]:border-zinc-200

                        dark:[:where(&)]:text-white
                        dark:[:where(&)]:bg-zinc-700
                        dark:[:where(&)]:border-white/10
                        dark:[:where(&)]:hover:bg-zinc-600/85
                        dark:[:where(&)]:hover:border-white/10
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-600/85
                        dark:[:where(&)]:[&[data-active]]:border-white/10
                    ',
                },
            },
            match ($variant) { // Shadows...
                'accent' => 'shadow-[inset_0px_1px_--theme(--color-white/.2)]',
                'filled', 'ghost', 'subtle', 'none' => '',
                default => 'shadow',
            },
            match ($variant) { // Grouped border treatments...
                'accent' => '[[data-tallkit-button-group]_&]:border-e-0 [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-[1px] dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-s-[1px] [:is([data-tallkit-button-group]>&:not(:first-child),_[data-tallkit-button-group]_:not(:first-child)>&)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]',
                'filled' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-zinc-200/80 dark:[[data-tallkit-button-group]_&]:border-zinc-800',
                'inverse', 'outline' => '[[data-tallkit-button-group]_&]:border-s-0 [:is([data-tallkit-button-group]>&:first-child,_[data-tallkit-button-group]_:first-child>&)]:border-s-[1px]',
                'danger' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-red-200/80 dark:[[data-tallkit-button-group]_&]:border-red-800',
                default => '',
            },
        ])
        ->when($loading, fn ($attrs) => $attrs->classes( // Loading states...
           '*:transition-opacity',
           $type === 'submit' ? '[&[disabled]>:not([data-tallkit-button-loading-indicator])]:opacity-0' : '[&[data-tallkit-button-loading]>:not([data-tallkit-button-loading-indicator])]:opacity-0',
           $type === 'submit' ? '[&[disabled]>[data-tallkit-button-loading-indicator]]:opacity-100' : '[&[data-tallkit-button-loading]>[data-tallkit-button-loading-indicator]]:opacity-100',
           $type === 'submit' ? '[&[disabled]]:pointer-events-none' : 'data-tallkit-button-loading:pointer-events-none',
       ))
        ->merge([TALLKit::dataKey(name: 'group-target') => !in_array($variant, ['subtle', 'ghost'])])
    "
>
    @if ($loading)
        <x-slot:prepend>
            <div
                {{
                    TALLKit::attributesAfter(attributes: $attributes, prefix: 'loading-indicator:')
                        ->dataKey('button-loading-indicator')
                        ->classes('absolute inset-0 flex items-center justify-center opacity-0')
                }}
            >
                <tk:loading
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'loading:')->when(is_string($loading), fn ($attrs, $value) => $attrs->merge(['variant' => $value]))"
                    :$size
                />
            </div>
        </x-slot:prepend>
    @endif

    {{ $slot }}
</tk:element>
