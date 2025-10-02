@php
$label = $attributes->has('label');
$hasContent = $slot->isNotEmpty() || $label;
$square ??= !$circle && !($slot->isNotEmpty() || $label);
$isTypeSubmitAndNotDisabledOnRender = $type === 'submit' && !$attributes->has('disabled');
$isJsMethod = Str::startsWith($attributes->whereStartsWith('wire:click')->first() ?? '', '$js.');
$loading ??= $isTypeSubmitAndNotDisabledOnRender || $attributes->whereStartsWith('wire:click')->isNotEmpty() && !$isJsMethod;

if ($loading && $type !== 'submit' && !$isJsMethod) {
    $attributes = $attributes->merge(['wire:loading.attr' => $dataKey('loading')]);

    if (!$attributes->has('wire:target') && $target = $attributes->whereStartsWith('wire:click')->first()) {
        $attributes = $attributes->merge(['wire:target' => $target], escape: false);
    }
} else {
    $attributes = $attributes->merge([$dataKey('loading') => $loading]);
}
@endphp

<tk:element
    :name="$baseComponentKey()"
    :$href
    :type="$type ?? 'button'"
    :icon:size="$adjustSize($size)"
    :icon-trailing:size="$adjustSize($size)"
    :content:class="$loading && $hasContent ? '' : null"
    :attributes="$attributes
        ->whereDoesntStartWith(['loading-indicator:', 'loading:'])
        ->classes(
            '
                relative justify-center
                font-medium whitespace-nowrap
                disabled:opacity-50 dark:disabled:opacity-50
                disabled:cursor-default disabled:pointer-events-none
                transition
            ',
            match ($size) { // Size...
                'xs' => 'h-8 gap-1 text-xs' . ' ' . ($square ? 'w-8' : 'px-2'),
                'sm' => 'h-10 gap-1.5 text-sm' . ' ' . ($square ? 'w-10' : 'px-3'),
                default => 'h-12 gap-2 text-base' . ' ' . ($square ? 'w-12' : 'px-4'),
                'lg' => 'h-14 gap-2.5 text-lg' . ' ' . ($square ? 'w-14' : 'px-5'),
                'xl' => 'h-16 gap-3 text-xl' . ' ' . ($square ? 'w-16' : 'px-6'),
                '2xl' => 'h-18 gap-3.5 text-2xl' . ' ' . ($square ? 'w-18' : 'px-7'),
                '3xl' => 'h-20 gap-4 text-3xl' . ' ' . ($square ? 'w-20' : 'px-8'),
            },

            match ($variant) { // Text color...
                'accent' => 'text-[var(--color-accent-foreground)]',
                'filled', 'outline', 'ghost' => 'text-zinc-800 dark:text-white',
                'subtle', 'none' => 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white',
                'amber', 'yellow' => 'text-white dark:text-zinc-950',
                default => 'text-white',
            },
            match ($variant) { // Border color...
                'outline' => 'border border-zinc-300 hover:border-zinc-300 dark:border-zinc-600 dark:hover:border-zinc-600',
                'subtle', 'ghost', 'none' => '',
                default => 'border border-black/20',
            },
            match ($variant) { // Background color...
                'accent' => 'bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_15%)]',
                'info' => 'bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600',
                'success' => 'bg-green-600 dark:bg-green-700 hover:bg-green-700  dark:hover:bg-green-600',
                'danger' => 'bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600',
                'outline' => 'bg-white hover:bg-zinc-50 dark:bg-zinc-700 dark:hover:bg-zinc-600',
                'filled' => 'bg-zinc-800/5 dark:bg-white/10 hover:bg-zinc-800/10 dark:hover:bg-white/20',
                'subtle', 'ghost' => 'bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15',
                'none' => 'bg-transparent',
                'red' => 'bg-red-500 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-500',
                'orange' => 'bg-orange-500 dark:bg-orange-600 hover:bg-orange-600 dark:hover:bg-orange-500',
                'amber' => 'bg-amber-500 dark:bg-amber-500 hover:bg-amber-600 dark:hover:bg-amber-400',
                'yellow' => 'bg-yellow-500 dark:bg-yellow-400 hover:bg-yellow-600 dark:hover:bg-yellow-300',
                'lime' => 'bg-lime-500 dark:bg-lime-600 hover:bg-lime-600 dark:hover:bg-lime-500',
                'green' => 'bg-green-500 dark:bg-green-600 hover:bg-green-600 dark:hover:bg-green-500',
                'emerald' => 'bg-emerald-500 dark:bg-emerald-600 hover:bg-emerald-600 dark:hover:bg-emerald-500',
                'teal' => 'bg-teal-500 dark:bg-teal-600 hover:bg-teal-600 dark:hover:bg-teal-500',
                'cyan' => 'bg-cyan-500 dark:bg-cyan-600 hover:bg-cyan-600 dark:hover:bg-cyan-500',
                'sky' => 'bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 dark:hover:bg-sky-500',
                'blue' => 'bg-blue-500 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-500',
                'indigo' => 'bg-indigo-500 dark:bg-indigo-600 hover:bg-indigo-600 dark:hover:bg-indigo-500',
                'violet' => 'bg-violet-500 dark:bg-violet-600 hover:bg-violet-600 dark:hover:bg-violet-500',
                'purple' => 'bg-purple-500 dark:bg-purple-600 hover:bg-purple-600 dark:hover:bg-purple-500',
                'fuchsia' => 'bg-fuchsia-500 dark:bg-fuchsia-600 hover:bg-fuchsia-600 dark:hover:bg-fuchsia-500',
                'pink' => 'bg-pink-500 dark:bg-pink-600 hover:bg-pink-600 dark:hover:bg-pink-500',
                'rose' => 'bg-rose-500 dark:bg-rose-600 hover:bg-rose-600 dark:hover:bg-rose-500',
                default => '
                    text-zinc-800 dark:text-white
                    bg-white hover:bg-zinc-100 dark:bg-zinc-700 dark:hover:bg-zinc-600
                    border border-zinc-300 hover:border-zinc-300 dark:border-zinc-600 dark:hover:border-zinc-600
                ',
            },
            match ($variant) { // Shadows...
                'accent' => 'shadow-[inset_0px_1px_--theme(--color-white/.2)]',
                'outline' => match ($size) {
                    'xs' => 'shadow-none',
                    'sm' => 'rounded-xs',
                    default => 'shadow-xs',
                    'lg' => 'shadow-sm',
                    'xl' => 'shadow',
                    '2xl' => 'shadow-lg',
                    '3xl' => 'shadow-xl',
                },
                default => '',
            },
            match ($variant) { // Grouped border treatments...
                'accent' => '[[data-tallkit-button-group]_&]:border-e-0 [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-[1px] dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-s-[1px] [:is([data-tallkit-button-group]>&:not(:first-child),_[data-tallkit-button-group]_:not(:first-child)>&)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]',
                'filled' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-zinc-200/80 dark:[[data-tallkit-button-group]_&]:border-zinc-800',
                'outline' => '[[data-tallkit-button-group]_&]:border-s-0 [:is([data-tallkit-button-group]>&:first-child,_[data-tallkit-button-group]_:first-child>&)]:border-s-[1px]',
                'danger' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-red-200/80 dark:[[data-tallkit-button-group]_&]:border-red-800',
                default => '',
            },
            $circle ? 'rounded-full' : match ($size) { // Rounded...
                'xs' => 'rounded-sm',
                'sm' => 'rounded-md',
                default => 'rounded-lg',
                'lg' => 'rounded-xl',
                'xl' => 'rounded-xl',
                '2xl' => 'rounded-2xl',
                '3xl' => 'rounded-2xl',
            },
            match ($variant) {
                'none' => 'h-auto w-auto p-0 rounded-none',
                default => '',
            },
        )
        ->when($loading, fn ($attrs) => $attrs->classes( // Loading states...
           '*:transition-opacity',
           $type === 'submit' ? '[&[disabled]>:not([data-tallkit-button-loading-indicator])]:opacity-0' : '[&[data-tallkit-button-loading]>:not([data-tallkit-button-loading-indicator])]:opacity-0',
           $type === 'submit' ? '[&[disabled]>[data-tallkit-button-loading-indicator]]:opacity-100' : '[&[data-tallkit-button-loading]>[data-tallkit-button-loading-indicator]]:opacity-100',
           $type === 'submit' ? '[&[disabled]]:pointer-events-none' : 'data-tallkit-button-loading:pointer-events-none',
       ))
        ->merge([$buildDataAttribute('group-target') => !in_array($variant, ['subtle', 'ghost'])])
    "
>
    <?php if ($loading): ?>
        <x-slot name="prepend">
            <div {{ $attributesAfter('loading-indicator:')->classes('absolute inset-0 flex items-center justify-center opacity-0') }}>
                <tk:loading
                    :attributes="$attributesAfter('loading:')->when(is_string($loading), fn ($attrs, $value) => $attrs->merge(['variant' => $value]))"
                    :$size
                />
            </div>
        </x-slot>
    <?php endif; ?>

    {{ $slot }}
</tk:element>
