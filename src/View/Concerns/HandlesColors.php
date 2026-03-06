<?php

namespace TALLKit\View\Concerns;

trait HandlesColors
{
    public function textColor(
        ?string $variant = null,
        ?string $contrast = null,
    ) {
        if ($variant === 'none') {
            return 'text-inherit';
        }

        if ($variant === 'current') {
            return 'text-current';
        }

        if ($variant === 'accent') {
            return 'text-[var(--color-accent-content)]';
        }

        if ($contrast === 'light') {
            return match ($variant) {
                'inverse' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-700',
                'strong' => '[:where(&)]:text-zinc-700 dark:[:where(&)]:text-white',
                'subtle' => '[:where(&)]:text-zinc-400/90 dark:[:where(&)]:text-white/40',
                default => '[:where(&)]:text-zinc-500/90 dark:[:where(&)]:text-white/70',
                'red' => 'text-red-500 dark:text-red-300',
                'orange' => 'text-orange-500 dark:text-orange-300',
                'amber' => 'text-amber-500 dark:text-amber-300',
                'yellow' => 'text-yellow-500 dark:text-yellow-300',
                'lime' => 'text-lime-500 dark:text-lime-300',
                'green' => 'text-green-500 dark:text-green-300',
                'emerald' => 'text-emerald-500 dark:text-emerald-300',
                'teal' => 'text-teal-500 dark:text-teal-300',
                'cyan' => 'text-cyan-500 dark:text-cyan-300',
                'sky' => 'text-sky-500 dark:text-sky-300',
                'blue' => 'text-blue-500 dark:text-blue-300',
                'indigo' => 'text-indigo-500 dark:text-indigo-300',
                'violet' => 'text-violet-500 dark:text-violet-300',
                'purple' => 'text-purple-500 dark:text-purple-300',
                'fuchsia' => 'text-fuchsia-500 dark:text-fuchsia-300',
                'pink' => 'text-pink-500 dark:text-pink-300',
                'rose' => 'text-rose-500 dark:text-rose-300',
            };
        }

        if ($contrast === 'strong') {
            return match ($variant) {
                'inverse' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-900',
                'strong' => '[:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
                'subtle' => '[:where(&)]:text-zinc-500 dark:[:where(&)]:text-white/70',
                default => '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white/90',
                'red' => 'text-red-700 dark:text-red-500',
                'orange' => 'text-orange-700 dark:text-orange-500',
                'amber' => 'text-amber-700 dark:text-amber-500',
                'yellow' => 'text-yellow-700 dark:text-yellow-500',
                'lime' => 'text-lime-700 dark:text-lime-500',
                'green' => 'text-green-700 dark:text-green-500',
                'emerald' => 'text-emerald-700 dark:text-emerald-500',
                'teal' => 'text-teal-700 dark:text-teal-500',
                'cyan' => 'text-cyan-700 dark:text-cyan-500',
                'sky' => 'text-sky-700 dark:text-sky-500',
                'blue' => 'text-blue-700 dark:text-blue-500',
                'indigo' => 'text-indigo-700 dark:text-indigo-500',
                'violet' => 'text-violet-700 dark:text-violet-500',
                'purple' => 'text-purple-700 dark:text-purple-500',
                'fuchsia' => 'text-fuchsia-700 dark:text-fuchsia-500',
                'pink' => 'text-pink-700 dark:text-pink-500',
                'rose' => 'text-rose-700 dark:text-rose-500',
            };
        }

        return match ($variant) {
            'inverse' => '[:where(&)]:text-white dark:[:where(&)]:text-zinc-800',
            'strong' => '[:where(&)]:text-zinc-800 dark:[:where(&)]:text-white',
            'subtle' => '[:where(&)]:text-zinc-400 dark:[:where(&)]:text-white/50',
            default => '[:where(&)]:text-zinc-500 dark:[:where(&)]:text-white/80',
            'red' => 'text-red-600 dark:text-red-400',
            'orange' => 'text-orange-600 dark:text-orange-400',
            'amber' => 'text-amber-600 dark:text-amber-400',
            'yellow' => 'text-yellow-600 dark:text-yellow-400',
            'lime' => 'text-lime-600 dark:text-lime-400',
            'green' => 'text-green-600 dark:text-green-400',
            'emerald' => 'text-emerald-600 dark:text-emerald-400',
            'teal' => 'text-teal-600 dark:text-teal-400',
            'cyan' => 'text-cyan-600 dark:text-cyan-400',
            'sky' => 'text-sky-600 dark:text-sky-400',
            'blue' => 'text-blue-600 dark:text-blue-400',
            'indigo' => 'text-indigo-600 dark:text-indigo-400',
            'violet' => 'text-violet-600 dark:text-violet-400',
            'purple' => 'text-purple-600 dark:text-purple-400',
            'fuchsia' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'pink' => 'text-pink-600 dark:text-pink-400',
            'rose' => 'text-rose-600 dark:text-rose-400',
        };
    }

    public function backgroundColor(
        ?string $variant = null,
        ?string $contrast = null,
    ) {
        if ($variant === 'ghost') {
            return 'bg-transparent';
        }

        if ($variant === 'accent') {
            return 'bg-[var(--color-accent)]';
        }

        if ($contrast === 'light') {
            return match ($variant) {
                'inverse' => '[:where(&)]:bg-black/10 dark:[:where(&)]:bg-zinc-700/5',
                'strong' => '[:where(&)]:bg-zinc-700/15 dark:[:where(&)]:bg-black/20',
                'subtle' => '[:where(&)]:bg-zinc-700/10 dark:[:where(&)]:bg-black/15',
                default => '[:where(&)]:bg-zinc-700/5 dark:[:where(&)]:bg-black/10',
                'red' => 'bg-red-500 dark:bg-red-300',
                'orange' => 'bg-orange-500 dark:bg-orange-300',
                'amber' => 'bg-amber-500 dark:bg-amber-300',
                'yellow' => 'bg-yellow-500 dark:bg-yellow-300',
                'lime' => 'bg-lime-500 dark:bg-lime-300',
                'green' => 'bg-green-500 dark:bg-green-300',
                'emerald' => 'bg-emerald-500 dark:bg-emerald-300',
                'teal' => 'bg-teal-500 dark:bg-teal-300',
                'cyan' => 'bg-cyan-500 dark:bg-cyan-300',
                'sky' => 'bg-sky-500 dark:bg-sky-300',
                'blue' => 'bg-blue-500 dark:bg-blue-300',
                'indigo' => 'bg-indigo-500 dark:bg-indigo-300',
                'violet' => 'bg-violet-500 dark:bg-violet-300',
                'purple' => 'bg-purple-500 dark:bg-purple-300',
                'fuchsia' => 'bg-fuchsia-500 dark:bg-fuchsia-300',
                'pink' => 'bg-pink-500 dark:bg-pink-300',
                'rose' => 'bg-rose-500 dark:bg-rose-300',
            };
        }

        if ($contrast === 'strong') {
            return match ($variant) {
                'inverse' => '[:where(&)]:bg-black/70 dark:[:where(&)]:bg-zinc-700/50',
                'strong' => '[:where(&)]:bg-zinc-700/90 dark:[:where(&)]:bg-black',
                'subtle' => '[:where(&)]:bg-zinc-700/70 dark:[:where(&)]:bg-black/90',
                default => '[:where(&)]:bg-zinc-700/50 dark:[:where(&)]:bg-black/70',
                'red' => 'bg-red-700 dark:bg-red-500',
                'orange' => 'bg-orange-700 dark:bg-orange-500',
                'amber' => 'bg-amber-700 dark:text-amber-500',
                'yellow' => 'bg-yellow-700 dark:bg-yellow-500',
                'lime' => 'bg-lime-700 dark:bg-lime-500',
                'green' => 'bg-green-700 dark:bg-green-500',
                'emerald' => 'bg-emerald-700 dark:bg-emerald-500',
                'teal' => 'bg-teal-700 dark:bg-teal-500',
                'cyan' => 'bg-cyan-700 dark:bg-cyan-500',
                'sky' => 'bg-sky-700 dark:bg-sky-500',
                'blue' => 'bg-blue-700 dark:bg-blue-500',
                'indigo' => 'bg-indigo-700 dark:bg-indigo-500',
                'violet' => 'bg-violet-700 dark:bg-violet-500',
                'purple' => 'bg-purple-700 dark:bg-purple-500',
                'fuchsia' => 'bg-fuchsia-700 dark:bg-fuchsia-500',
                'pink' => 'bg-pink-700 dark:bg-pink-500',
                'rose' => 'bg-rose-700 dark:bg-rose-500',
            };
        }

        return match ($variant) {
            'inverse' => '[:where(&)]:bg-black/30 dark:[:where(&)]:bg-zinc-700/20',
            'strong' => '[:where(&)]:bg-zinc-700/40 dark:[:where(&)]:bg-black/50',
            'subtle' => '[:where(&)]:bg-zinc-700/30 dark:[:where(&)]:bg-black/40',
            default => '[:where(&)]:bg-zinc-700/20 dark:[:where(&)]:bg-black/30',
            'red' => 'bg-red-600 dark:bg-red-400',
            'orange' => 'bg-orange-600 dark:bg-orange-400',
            'amber' => 'bg-amber-600 dark:bg-amber-400',
            'yellow' => 'bg-yellow-600 dark:bg-yellow-400',
            'lime' => 'bg-lime-600 dark:bg-lime-400',
            'green' => 'bg-green-600 dark:bg-green-400',
            'emerald' => 'bg-emerald-600 dark:bg-emerald-400',
            'teal' => 'bg-teal-600 dark:bg-teal-400',
            'cyan' => 'bg-cyan-600 dark:bg-cyan-400',
            'sky' => 'bg-sky-600 dark:bg-sky-400',
            'blue' => 'bg-blue-600 dark:bg-blue-400',
            'indigo' => 'bg-indigo-600 dark:bg-indigo-400',
            'violet' => 'bg-violet-600 dark:bg-violet-400',
            'purple' => 'bg-purple-600 dark:bg-purple-400',
            'fuchsia' => 'bg-fuchsia-600 dark:bg-fuchsia-400',
            'pink' => 'bg-pink-600 dark:bg-pink-400',
            'rose' => 'bg-rose-600 dark:bg-rose-400',
        };
    }

    public function borderColor(
        ?string $variant = null,
        ?string $contrast = null,
    ) {
        if ($variant === 'accent') {
            return 'border-[var(--color-accent)]';
        }

        if ($contrast === 'light') {
            return match ($variant) {
                default => 'border-zinc-300',
                'red' => 'border-red-300',
                'orange' => 'border-orange-300',
                'amber' => 'border-amber-300',
                'yellow' => 'border-yellow-300',
                'lime' => 'border-lime-300',
                'green' => 'border-green-300',
                'emerald' => 'border-emerald-300',
                'teal' => 'border-teal-300',
                'cyan' => 'border-cyan-300',
                'sky' => 'border-sky-300',
                'blue' => 'border-blue-300',
                'indigo' => 'border-indigo-300',
                'violet' => 'border-violet-300',
                'purple' => 'border-purple-300',
                'fuchsia' => 'border-fuchsia-300',
                'pink' => 'border-pink-300',
                'rose' => 'border-rose-300',
            };
        }

        if ($contrast === 'strong') {
            return match ($variant) {
                default => 'border-zinc-500',
                'red' => 'border-red-500',
                'orange' => 'border-orange-500',
                'amber' => 'border-amber-500',
                'yellow' => 'border-yellow-500',
                'lime' => 'border-lime-500',
                'green' => 'border-green-500',
                'emerald' => 'border-emerald-500',
                'teal' => 'border-teal-500',
                'cyan' => 'border-cyan-500',
                'sky' => 'border-sky-500',
                'blue' => 'border-blue-500',
                'indigo' => 'border-indigo-500',
                'violet' => 'border-violet-500',
                'purple' => 'border-purple-500',
                'fuchsia' => 'border-fuchsia-500',
                'pink' => 'border-pink-500',
                'rose' => 'border-rose-500',
            };
        }

        return match ($variant) {
            default => 'border-zinc-400',
            'red' => 'border-red-400',
            'orange' => 'border-orange-400',
            'amber' => 'border-amber-400',
            'yellow' => 'border-yellow-400',
            'lime' => 'border-lime-400',
            'green' => 'border-green-400',
            'emerald' => 'border-emerald-400',
            'teal' => 'border-teal-400',
            'cyan' => 'border-cyan-400',
            'sky' => 'border-sky-400',
            'blue' => 'border-blue-400',
            'indigo' => 'border-indigo-400',
            'violet' => 'border-violet-400',
            'purple' => 'border-purple-400',
            'fuchsia' => 'border-fuchsia-400',
            'pink' => 'border-pink-400',
            'rose' => 'border-rose-400',
        };
    }
}
