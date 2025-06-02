@php
    $square ??= !$circle && !($slot->isNotEmpty() || $text);
    $isTypeSubmitAndNotDisabledOnRender = $type === 'submit' && !$attributes->has('disabled');
    $isJsMethod = str_starts_with($attributes->whereStartsWith('wire:click')->first() ?? '', '$js.');
    $loading ??= $isTypeSubmitAndNotDisabledOnRender || $attributes->whereStartsWith('wire:click')->isNotEmpty() && !$isJsMethod;

    if ($loading && $type !== 'submit' && !$isJsMethod) {
        $attributes = $attributes->merge(['wire:loading.attr' => 'data-tallkit-button-loading']);

        if (!$attributes->has('wire:target') && $target = $attributes->whereStartsWith('wire:click')->first()) {
            $attributes = $attributes->merge(['wire:target' => $target], escape: false);
        }
    }
@endphp

<tk:element :attributes="$attributes->whereDoesntStartWith(['icon:', 'kbd:', 'icon-trailing:', 'loading-indicator:', 'loading:'])
        ->classes('relative items-center font-medium justify-center gap-2 whitespace-nowrap')
        ->classes('disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none transition')
        ->classes($circle ? 'rounded-full' : match ($circle) { // Rounded...
            default => 'rounded-lg',
            'sm' => 'rounded-md',
            'xs' => 'rounded-md',
        })
        ->classes(match ($size) { // Size...
            'xl' => 'h-14 text-lg' . ' ' . ($square ? 'w-14' : 'px-8'),
            'lg' => 'h-12 text-base' . ' ' . ($square ? 'w-12' : 'px-6'),
            default => 'h-10 text-sm' . ' ' . ($square ? 'w-10' : 'px-4'),
            'sm' => 'h-8 text-sm' . ' ' . ($square ? 'w-8' : 'px-3'),
            'xs' => 'h-6 text-xs' . ' ' . ($square ? 'w-6' : 'px-2'),
        })
        ->classes('inline-flex')
        ->classes(match ($variant) { // Background color...
            'primary' => 'bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)]',
            'info' => 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600',
            'success' => 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600',
            'danger' => 'bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-500',
            'filled' => 'bg-zinc-800/5 hover:bg-zinc-800/10 dark:bg-white/10 dark:hover:bg-white/20',
            'ghost', 'subtle' => 'bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15',
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
            default => 'bg-white hover:bg-zinc-50 dark:bg-zinc-700 dark:hover:bg-zinc-600/75',
        })
        ->classes(match ($variant) { // Text color...
            'primary' => 'text-[var(--color-accent-foreground)]',
            'filled', 'outline', 'ghost' => 'text-zinc-800 dark:text-white',
            'subtle' => 'text-zinc-400 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white',
            'amber', 'yellow' => 'text-white dark:text-zinc-950',
            default => 'text-white',
        })
        ->classes(match ($variant) { // Border color...
            'primary' => 'border border-black/10 dark:border-0',
            'outline' => 'border border-zinc-200 hover:border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 dark:hover:border-zinc-600',
            default => '',
        })
        ->classes(match ($variant) { // Shadows...
            'primary' => 'shadow-[inset_0px_1px_--theme(--color-white/.2)]',
            'outline' => match ($size) {
                    'xl' => 'shadow',
                    'lg' => 'shadow-sm',
                    'xs' => 'shadow-none',
                    default => 'shadow-xs',
                },
            default => '',
        })
        ->classes(match ($variant) { // Grouped border treatments...
            'primary' => '[[data-tallkit-button-group]_&]:border-e-0 [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-[1px] dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 dark:[:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-s-[1px] [:is([data-tallkit-button-group]>&:not(:first-child),_[data-tallkit-button-group]_:not(:first-child)>&)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]',
            'filled' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-zinc-200/80 dark:[[data-tallkit-button-group]_&]:border-zinc-900/50',
            'outline' => '[[data-tallkit-button-group]_&]:border-s-0 [:is([data-tallkit-button-group]>&:first-child,_[data-tallkit-button-group]_:first-child>&)]:border-s-[1px]',
            'danger' => '[[data-tallkit-button-group]_&]:border-e [:is([data-tallkit-button-group]>&:last-child,_[data-tallkit-button-group]_:last-child>&)]:border-e-0 [[data-tallkit-button-group]_&]:border-red-600 dark:[[data-tallkit-button-group]_&]:border-red-900/25',
            default => '',
        })
        ->when($loading, fn($attrs) => $attrs->classes( // Loading states...
            '*:transition-opacity',
            $type === 'submit' ? '[&[disabled]>:not([data-tallkit-button-loading-indicator])]:opacity-0' : '[&[data-tallkit-button-loading]>:not([data-tallkit-button-loading-indicator])]:opacity-0',
            $type === 'submit' ? '[&[disabled]>[data-tallkit-button-loading-indicator]]:opacity-100' : '[&[data-tallkit-button-loading]>[data-tallkit-button-loading-indicator]]:opacity-100',
            $type === 'submit' ? '[&[disabled]]:pointer-events-none' : 'data-tallkit-button-loading:pointer-events-none',
        ))
        ->merge(['data-tallkit-button-group-target' => !in_array($variant, ['subtle', 'ghost'])])" :$href :$type
    data-tallkit-button>
    @if ($loading)
        <div {{ $attributesAfter('loading-indicator:')->classes('absolute inset-0 flex items-center justify-center opacity-0') }} data-tallkit-button-loading-indicator>
            <tk:loading :attributes="$attributesAfter('loading:')
                ->classes($square && $size !== 'xs' ? 'size-5' : 'size-4')
                ->when(is_string($loading), fn($attrs, $value) => $attrs->merge(['type' => $value]))" />
        </div>
    @endif

    @if (is_string($icon) && $icon !== '')
        <tk:icon :$icon :attributes="$attributesAfter('icon:')->classes($square && $size !== 'xs' ? 'size-5' : 'size-4')"
            data-tallkit-button-icon />
    @elseif($icon)
        <span {{ $attributesAfter('icon:') }}>{{ $icon }}</span>
    @endif

    @if ($loading && ($slot->isNotEmpty() || $text))
        <span>{{ $slot->isEmpty() ? __($text) : $slot }}</span>
    @else
        {{ $slot->isEmpty() ? __($text) : $slot }}
    @endif

    @if ($kbd)
        <div {{ $attributesAfter('kbd:')->classes('text-xs text-zinc-500 dark:text-zinc-400') }} data-tallkit-button-kbd>
            {{ $kbd }}
        </div>
    @endif

    @if (is_string($iconTrailing) && $iconTrailing !== '')
        <tk:icon :icon="$iconTrailing" :attributes="$attributesAfter('icon-trailing:')
                ->classes($square && $size !== 'xs' ? 'size-5' : 'size-4')
                ->classes($square ? '' : '-ms-1')" data-tallkit-icon-trailing />
    @elseif($iconTrailing)
        <span {{ $attributesAfter('icon-trailing:') }}>{{ $iconTrailing }}</span>
    @endif
</tk:element>
