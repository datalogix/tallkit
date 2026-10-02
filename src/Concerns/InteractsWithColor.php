<?php

namespace TALLKit\Concerns;

trait InteractsWithColor
{
    protected array $colors = [
        'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal',
        'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose',
        'slate', 'gray', 'zinc', 'stone',
    ];

    public function isColor(?string $color): bool
    {
        return $color !== null && in_array($color, $this->colors(), true);
    }

    public function colors(): array
    {
        return array_values(array_unique([...$this->colors, ...(array) config('tallkit.colors', [])]));
    }

    private function colorClass(?string $color, \Closure $build): ?string
    {
        return $this->isColor($color) ? "tk-color-{$color} ".$build() : null;
    }

    public function controlFocusRing(?string $color, bool $expanded = false): ?string
    {
        return $this->colorClass($color, function () use ($expanded) {
            $selector = $expanded ? '[&:is(:focus-visible,[aria-expanded=true])]:' : 'focus-visible:';

            return "{$selector}outline-(--tk-ring)! {$selector}ring-(--tk-ring-soft)!";
        });
    }

    public function controlFocusRingNested(?string $color, bool $expanded = false): ?string
    {
        return $this->colorClass($color, function () use ($expanded) {
            $selector = $expanded
                ? 'has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:'
                : 'has-[[data-tallkit-control]:focus-visible]:';

            return "{$selector}outline-(--tk-ring)! {$selector}ring-(--tk-ring-soft)!";
        });
    }

    public function ring(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'ring-(--tk-ring)');
    }

    public function faintBackground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'bg-(--tk-faint)');
    }

    public function ringBorder(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'border-(--tk-ring)');
    }

    public function checkedBackground(?string $color, bool $wrapped = false): ?string
    {
        return $this->colorClass($color, fn () => ($wrapped ? 'has-[input:checked]:' : 'checked:').'bg-(--tk-fill)');
    }

    public function checkedForeground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'text-(--tk-on-fill)');
    }

    public function checkedDot(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'bg-(--tk-on-fill)');
    }

    public function background(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'bg-(--tk-fill) text-(--tk-on-fill)');
    }

    public function border(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'border-(--tk-fill)');
    }

    public function text(?string $color, ?string $prefix = null): ?string
    {
        return $this->colorClass($color, fn () => "{$prefix}text-(--tk-text)");
    }

    public function textStrong(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'text-(--tk-solid)');
    }

    protected function frameBackground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'tk-frame bg-(--tk-frame) text-(--tk-on-frame)');
    }

    public function frameClasses(?string $variant): string
    {
        return match ($variant) {
            'none' => '',
            'accent' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]',
            'inverse' => 'bg-zinc-800 dark:bg-zinc-100 [:where(&)]:text-white/85 dark:[:where(&)]:text-zinc-900',
            'strong' => 'bg-zinc-200 dark:bg-zinc-950 [:where(&)]:text-zinc-900 dark:[:where(&)]:text-white',
            'subtle' => 'bg-white dark:bg-zinc-900 [:where(&)]:text-zinc-700/70 dark:[:where(&)]:text-white/70',
            'ghost' => 'bg-transparent [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
            default => $this->frameBackground($variant) ?? 'bg-zinc-50 dark:bg-zinc-800 [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85',
        };
    }

    public function mutedBackground(?string $color, string $as = 'a'): ?string
    {
        return $this->colorClass($color, fn () => "bg-(--tk-soft) [&:is({$as})]:hover:bg-(--tk-soft-hover)");
    }

    public function mutedText(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'text-(--tk-on-soft)');
    }

    public function textNeutral(?string $variant = null, ?string $prefix = null): string
    {
        $value = match ($variant) {
            'emphasis' => 'text-zinc-900 dark:text-white',
            'strong' => 'text-zinc-800 dark:text-white',
            'subtle' => 'text-zinc-500 dark:text-zinc-400',
            'muted' => 'text-zinc-500 dark:text-zinc-400',
            default => 'text-zinc-700 dark:text-white/80',
        };

        return $prefix ? str_replace('text-', "{$prefix}text-", $value) : $value;
    }

    public function backgroundNeutral(?string $variant = null, ?string $prefix = null): string
    {
        $value = match ($variant) {
            'faint' => 'bg-zinc-800/5 dark:bg-white/10',
            'strong' => 'bg-zinc-800/20 dark:bg-white/20',
            default => 'bg-zinc-800/10 dark:bg-white/10',
        };

        return $prefix ? str_replace('bg-', "{$prefix}bg-", $value) : $value;
    }

    public function interactiveBackground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => '
            bg-(--tk-solid) text-(--tk-on-solid)
            hover:bg-(--tk-solid-hover) [&[data-active]]:bg-(--tk-solid-hover)
        ');
    }

    public function solidBackground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'bg-(--tk-solid) text-(--tk-on-solid) [&:is(button)]:hover:bg-(--tk-solid-hover)');
    }

    public function pastelBackground(?string $color): ?string
    {
        return $this->colorClass($color, fn () => 'bg-(--tk-pastel) text-(--tk-on-pastel)');
    }

    public function sliderFocusRing(?string $color): ?string
    {
        return $this->colorClass($color, fn () => '
            focus-visible:[&::-webkit-slider-thumb]:outline-(--tk-ring) focus-visible:[&::-webkit-slider-thumb]:ring-(--tk-ring-soft)
            focus-visible:[&::-moz-range-thumb]:outline-(--tk-ring) focus-visible:[&::-moz-range-thumb]:ring-(--tk-ring-soft)
        ');
    }
}
