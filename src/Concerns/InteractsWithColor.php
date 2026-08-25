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
        return in_array($color, $this->colors, true);
    }

    public function colors(): array
    {
        return $this->colors;
    }

    public function controlFocusRing(?string $color, bool $expanded = false): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        $selector = $expanded ? '[&:is(:focus-visible,[aria-expanded=true])]:' : 'focus-visible:';

        return "{$selector}outline-{$color}-700! dark:{$selector}outline-{$color}-300! {$selector}ring-{$color}-700/20! dark:{$selector}ring-{$color}-300/20!";
    }

    public function controlFocusRingNested(?string $color, bool $expanded = false): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        $selector = $expanded
            ? 'has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:'
            : 'has-[[data-tallkit-control]:focus-visible]:';

        return "{$selector}outline-{$color}-700! dark:{$selector}outline-{$color}-300! {$selector}ring-{$color}-700/20! dark:{$selector}ring-{$color}-300/20!";
    }

    public function uploadRing(?string $color): ?string
    {
        return $this->isColor($color) ? "ring-{$color}-700 dark:ring-{$color}-300" : null;
    }

    public function uploadBg(?string $color): ?string
    {
        return $this->isColor($color) ? "bg-{$color}-500/10 dark:bg-{$color}-300/10" : null;
    }

    public function uploadBorder(?string $color): ?string
    {
        return $this->isColor($color) ? "border-{$color}-700 dark:border-{$color}-300" : null;
    }

    public function uploadText(?string $color): ?string
    {
        return $this->isColor($color) ? "text-{$color}-700 dark:text-{$color}-300" : null;
    }

    public function checkedBackground(?string $color, bool $wrapped = false): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        $prefix = $wrapped ? 'has-[input:checked]:' : 'checked:';

        return "{$prefix}bg-{$color}-600 dark:{$prefix}bg-{$color}-500";
    }

    public function background(?string $color): ?string
    {
        return $this->isColor($color) ? "bg-{$color}-600 dark:bg-{$color}-700" : null;
    }

    public function backgroundActive(?string $color): ?string
    {
        return $this->isColor($color) ? "bg-{$color}-500 dark:bg-{$color}-600" : null;
    }

    public function text(?string $color): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        $dark = in_array($color, ['amber', 'yellow', 'lime', 'green'], true) ? '500' : '400';

        return "text-{$color}-600 dark:text-{$color}-{$dark}";
    }

    public function textStrong(?string $color): ?string
    {
        return $this->isColor($color) ? "text-{$color}-600 dark:text-{$color}-700" : null;
    }

    public function frameBackground(?string $color): ?string
    {
        return $this->isColor($color) ? "bg-{$color}-700 dark:bg-{$color}-600 *:text-white" : null;
    }

    public function mutedBackground(?string $color, string $as = 'a'): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        return "bg-{$color}-400/20 dark:bg-{$color}-400/40 [&:is({$as})]:hover:bg-{$color}-400/30 dark:[&:is({$as})]:hover:bg-{$color}-400/50";
    }

    public function mutedText(?string $color): ?string
    {
        return $this->isColor($color) ? "text-{$color}-700 dark:text-{$color}-200" : null;
    }

    public function interactiveBackground(?string $color): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        [$darkBase, $darkHover] = match ($color) {
            'amber' => ['500', '400'],
            'yellow' => ['400', '300'],
            default => ['600', '500'],
        };

        return "
            bg-{$color}-500
            hover:bg-{$color}-600
            [&[data-active]]:bg-{$color}-600

            dark:bg-{$color}-{$darkBase}
            dark:hover:bg-{$color}-{$darkHover}
            dark:[&[data-active]]:bg-{$color}-{$darkHover}
        ";
    }

    public function solidBackground(?string $color): ?string
    {
        return $this->isColor($color)
            ? "text-white dark:text-white bg-{$color}-500 dark:bg-{$color}-600 [&:is(button)]:hover:bg-{$color}-600 dark:[&:is(button)]:hover:bg-{$color}-500"
            : null;
    }

    public function pastelBackground(?string $color): ?string
    {
        return $this->isColor($color) ? "bg-{$color}-200 text-{$color}-800" : null;
    }

    public function sliderFocusRing(?string $color): ?string
    {
        if (! $this->isColor($color)) {
            return null;
        }

        return "
            focus-visible:[&::-webkit-slider-thumb]:outline-{$color}-700 dark:focus-visible:[&::-webkit-slider-thumb]:outline-{$color}-300
            focus-visible:[&::-webkit-slider-thumb]:ring-{$color}-700/20 dark:focus-visible:[&::-webkit-slider-thumb]:ring-{$color}-300/20
            focus-visible:[&::-moz-range-thumb]:outline-{$color}-700 dark:focus-visible:[&::-moz-range-thumb]:outline-{$color}-300
            focus-visible:[&::-moz-range-thumb]:ring-{$color}-700/20 dark:focus-visible:[&::-moz-range-thumb]:ring-{$color}-300/20
        ";
    }
}
