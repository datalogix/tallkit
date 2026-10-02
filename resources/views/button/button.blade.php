@aware(['size'])
@props([
    'size' => null,
    'type' => null,
    'href' => null,
    'loading' => null,
    'circle' => null,
    'square' => null,
    'variant' => null,
    'tooltip' => null,
    'color' => null,
    ...TALLKit::elementProps(),
])
@php

$variant = $variant ?: 'outline';
$isColored = $color
    && TALLKit::isColor($color)
    && ! in_array($color, ['slate', 'gray', 'zinc', 'stone'], true)
    && in_array($variant, ['filled', 'outline', 'ghost', 'subtle'], true);
$solidColor = match (true) {
    $variant === 'primary' && TALLKit::isColor($color) => $color,
    in_array($variant, ['info', 'success', 'danger', 'warning'], true) => ['info' => 'blue', 'success' => 'green', 'danger' => 'red', 'warning' => 'yellow'][$variant],
    default => null,
};
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
    kind="button"
    :type="$type ?? 'button'"
    :icon:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :content:class="$loading && $hasContent ? 'flex-1' : ($badge !== null || $iconTrailing !== null ? 'flex-1' : null)"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['href' => null, 'tooltip' => null])
        ->whereDoesntStartWith(['loading-indicator:', 'loading:'])
        ->classes([
            '
                [:where(&)]:relative [:where(&)]:justify-center [:where(&)]:align-middle
                [:where(&)]:font-medium [:where(&)]:whitespace-nowrap
                [:where(&)]:disabled:opacity-disabled [:where(&)]:aria-disabled:opacity-disabled
                [:where(&)]:aria-disabled:cursor-default [:where(&)]:disabled:cursor-default
                [:where(&)]:transition [:where(&)]:overflow-hidden
                forced-colors:border
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
            $isColored ? 'tk-color-'.$color : null,
            ! $isColored && in_array($variant, ['ghost', 'subtle', 'none'], true) ? 'tk-plain' : null,
            match (true) {
                $isColored && in_array($variant, ['filled', 'outline']) => '[:where(&)]:text-(--tk-on-soft)',
                $isColored && in_array($variant, ['ghost', 'subtle']) => '
                    [:where(&)]:text-(--tk-text)
                    [:where(&:not(:disabled,[aria-disabled=true]))]:hover:text-(--tk-on-soft) [:where(&)]:[&[data-active]]:text-(--tk-on-soft)
                ',
                $solidColor !== null => null,
                $variant === 'primary' => '[:where(&)]:text-[var(--color-accent-foreground)]',
                default => match ($variant) {
                    'accent' => '[:where(&)]:text-[var(--color-accent-foreground)]',
                    'filled', 'outline', 'ghost' => TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
                    'inverse' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-800',
                    'subtle', 'none' => '
                        [:where(&)]:text-zinc-600
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:text-zinc-800
                        [:where(&)]:[&[data-active]]:text-zinc-800

                        dark:[:where(&)]:text-zinc-300
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:text-white
                        dark:[:where(&)]:[&[data-active]]:text-white
                    ',
                    default => '[:where(&)]:text-white',
                },
            },
            match (true) {
                $isColored && $variant === 'outline' => '[:where(&)]:border [:where(&)]:border-(--tk-border)',
                default => match ($variant) {
                    'outline' => '
                        [:where(&)]:border
                        [:where(&)]:border-b-zinc-300/80

                        [:where(&)]:border-zinc-200
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:border-zinc-200
                        [:where(&)]:[&[data-active]]:border-zinc-200

                        dark:[:where(&)]:border-white/10
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:border-white/10
                        dark:[:where(&)]:[&[data-active]]:border-white/10
                    ',
                    'inverse', 'filled', 'subtle', 'ghost', 'none' => '',
                    default => '[:where(&)]:border [:where(&)]:border-black/10',
                },
            },
            match (true) {
                $isColored && $variant === 'filled' => TALLKit::mutedBackground(color: $color, as: $href ? 'a' : 'button'),
                $isColored && $variant === 'outline' => '
                    [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
                    [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-(--tk-soft) [:where(&)]:[&[data-active]]:bg-(--tk-soft)
                ',
                $isColored && $variant === 'ghost' => '
                    [:where(&)]:bg-transparent
                    [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-(--tk-soft) [:where(&)]:[&[data-active]]:bg-(--tk-soft)
                ',
                $isColored && $variant === 'subtle' => '
                    [:where(&)]:bg-transparent
                    [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-(--tk-faint) [:where(&)]:[&[data-active]]:bg-(--tk-faint)
                ',
                $solidColor !== null => TALLKit::interactiveBackground(color: $solidColor),
                $variant === 'primary' => '
                    [:where(&)]:bg-[var(--color-accent)]
                    [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                    [:where(&)]:[&[data-active]]:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                ',
                default => match ($variant) {
                    'accent' => '
                        [:where(&)]:bg-[var(--color-accent)]
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                        [:where(&)]:[&[data-active]]:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                    ',
                    'inverse' => '
                        [:where(&)]:bg-zinc-700
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-600/75
                        [:where(&)]:[&[data-active]]:bg-zinc-600/75

                        dark:[:where(&)]:bg-zinc-200
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-300/75
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-300/75
                    ',
                    'outline' => '
                        [:where(&)]:bg-white
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-800/5
                        [:where(&)]:[&[data-active]]:bg-zinc-800/5

                        dark:[:where(&)]:bg-zinc-700
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-600/85
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-600/85
                    ',
                    'filled' => '
                        [:where(&)]:bg-zinc-800/5
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-800/15
                        [:where(&)]:[&[data-active]]:bg-zinc-800/15

                        dark:[:where(&)]:bg-white/10
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-white/20
                        dark:[:where(&)]:[&[data-active]]:bg-white/20
                    ',
                    'subtle', 'ghost' => '
                        [:where(&)]:bg-transparent
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-800/10
                        [:where(&)]:[&[data-active]]:bg-zinc-800/10

                        dark:[:where(&)]:bg-transparent
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-white/10
                        dark:[:where(&)]:[&[data-active]]:bg-white/10
                    ',
                    'none' => 'bg-transparent',
                    default => '
                        [:where(&)]:border
                        [:where(&)]:border-b-zinc-300/80

                        [:where(&)]:text-zinc-800
                        [:where(&)]:bg-white
                        [:where(&)]:border-zinc-200
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-800/5
                        [:where(&:not(:disabled,[aria-disabled=true]))]:hover:border-zinc-200
                        [:where(&)]:[&[data-active]]:bg-zinc-800/5
                        [:where(&)]:[&[data-active]]:border-zinc-200

                        dark:[:where(&)]:text-white
                        dark:[:where(&)]:bg-zinc-700
                        dark:[:where(&)]:border-white/10
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:bg-zinc-600/85
                        dark:[:where(&:not(:disabled,[aria-disabled=true]))]:hover:border-white/10
                        dark:[:where(&)]:[&[data-active]]:bg-zinc-600/85
                        dark:[:where(&)]:[&[data-active]]:border-white/10
                    ',
                },
            },
            match ($variant) {
                'accent' => 'shadow-[inset_0px_1px_--theme(--color-white/.2)]',
                'filled', 'ghost', 'subtle', 'none' => '',
                default => 'shadow',
            },
            match ($variant) {
                'accent' => '[:is([data-tallkit-button-group]>&:not(:last-child),_[data-tallkit-button-group]_:not(:last-child)>&)]:border-e-[color-mix(in_srgb,var(--color-accent-foreground),transparent_70%)]',
                'filled' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-zinc-200/80 dark:[[data-tallkit-button-group]_&]:border-zinc-800',
                'inverse', 'outline' => '[[data-tallkit-button-group]_&]:border-s-0 [:is([data-tallkit-button-group]>&:first-child,_[data-tallkit-button-group]_:first-child>&)]:border-s-[1px]',
                'danger' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-red-200/80 dark:[[data-tallkit-button-group]_&]:border-red-800',
                default => '',
            },
        ])
        ->when($loading, fn ($attrs) => $attrs->classes(
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
                    $attributes->prefixed('loading-indicator:')
                        ->dataKey('button-loading-indicator')
                        ->classes('absolute inset-0 flex items-center justify-center opacity-0')
                        ->merge(['aria-hidden' => 'true'])
                }}
            >
                <tk:loading
                    :attributes="$attributes->prefixed('loading:')->when(is_string($loading), fn ($attrs, $value) => $attrs->merge(['variant' => $value]))"
                    :size="TALLKit::adjustSize(size: $size)"
                />
            </div>

            {{ $prepend }}
        </x-slot:prepend>
    @endif

    {{ $slot }}
</tk:element>
