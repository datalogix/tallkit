<?php

namespace TALLKit\Concerns;

use TALLKit\Facades\TALLKit;

trait InteractsWithSize
{
    protected function modeIncrement(?string $mode = null)
    {
        return match ($mode) {
            'smallest' => -2,
            'small' => -1,
            'large' => 1,
            'largest' => 2,
            default => 0,
        };
    }

    protected function incrementalByMode(
        ?string $size,
        ?string $mode,
        array $scaleBySize,
    ) {
        if (is_string($size) && array_key_exists($size, $scaleBySize)) {
            $value = $scaleBySize[$size];

            if (! is_array($value)) {
                return (string) $value;
            }
        }

        $sizeKey = is_string($size) && isset($scaleBySize[$size]) && is_array($scaleBySize[$size])
            ? $size
            : 'md';

        $modeIndex = max(0, min(4, 2 + $this->modeIncrement($mode)));

        return $scaleBySize[$sizeKey][$modeIndex];
    }

    public function adjustSize(
        ?string $size = null,
        array $sizes = ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl'],
        int $move = -1
    ) {
        $default = 'md';
        $size ??= $this->size ?? $default;
        $index = array_search($size, $sizes);

        if ($index === false && $size !== $default) {
            $index = array_search($default, $sizes);
        }

        if ($index === false) {
            return null;
        }

        return $sizes[max(0, min(count($sizes) - 1, $index + $move))];
    }

    public function fontSize(
        ?string $size = null,
        ?string $mode = null,
        ?bool $weight = null,
    ) {
        $sizes = ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl'];
        $textScale = [
            'text-[0.5rem]',
            'text-[0.625rem]',
            'text-[0.6875rem]',
            'text-xs',
            'text-sm',
            'text-base',
            'text-lg',
            'text-xl',
            'text-2xl',
            'text-3xl',
            'text-4xl',
        ];
        $weightScale = [
            'font-extralight',
            'font-light',
            'font-normal',
            'font-medium',
            'font-semibold',
            'font-bold',
            'font-extrabold',
        ];

        $increment = $this->modeIncrement($mode);
        $resolvedSize = $size ?? $this->size ?? 'md';
        $sizeIndex = array_search($resolvedSize, $sizes, true);

        if ($sizeIndex === false) {
            $sizeIndex = array_search('md', $sizes, true);
        }

        $textIndex = max(0, min(count($textScale) - 1, $sizeIndex + 2 + $increment));
        $textClass = '[:where(&)]:'.$textScale[$textIndex];

        if (! $weight) {
            return $textClass;
        }

        $weightIndex = max(0, min(count($weightScale) - 1, $sizeIndex + 2 + $increment));

        return $textClass.' [:where(&)]:'.$weightScale[$weightIndex];
    }

    public function width(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['2', '4', '8', '10', '14'],
            'sm' => ['2.5', '5', '9', '12', '18'],
            'md' => ['3', '6', '10', '14', '22'],
            'lg' => ['3.5', '7', '12', '16', '26'],
            'xl' => ['4', '8', '14', '18', '30'],
            '2xl' => ['4.5', '9', '16', '20', '36'],
            '3xl' => ['5', '10', '18', '22', '40'],
            'none' => '0',
            'full' => 'full',
        ]);

        return '[:where(&)]:w-'.$value;
    }

    public function height(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['2', '4', '8', '10', '14'],
            'sm' => ['2.5', '5', '9', '12', '18'],
            'md' => ['3', '6', '10', '14', '22'],
            'lg' => ['3.5', '7', '12', '16', '26'],
            'xl' => ['4', '8', '14', '18', '30'],
            '2xl' => ['4.5', '9', '16', '20', '36'],
            '3xl' => ['5', '10', '18', '22', '40'],
            'none' => '0',
            'full' => 'full',
        ]);

        return '[:where(&)]:h-'.$value;
    }

    public function widthHeight(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['2', '3', '4', '6', '8'],
            'sm' => ['2.5', '3.5', '5', '7', '10'],
            'md' => ['3', '4', '6', '9', '12'],
            'lg' => ['3.5', '5', '7', '10', '16'],
            'xl' => ['4', '5.5', '8', '12', '20'],
            '2xl' => ['4.5', '6', '9', '14', '24'],
            '3xl' => ['5', '7', '10', '16', '28'],
            'none' => '0',
            'full' => 'full',
        ]);

        return '[:where(&)]:size-'.$value;
    }

    public function iconSize(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['2', '2.5', '3', '3.5', '4'],
            'sm' => ['2.5', '3', '3.5', '4', '4.5'],
            'md' => ['3', '3.5', '4', '4.5', '5'],
            'lg' => ['3.5', '4', '4.5', '5', '5.5'],
            'xl' => ['4', '4.5', '5', '5.5', '6'],
            '2xl' => ['4.5', '5', '5.5', '6', '7'],
            '3xl' => ['5', '5.5', '6', '7', '8'],
            'none' => '0',
            'full' => 'full',
        ]);

        return '[&_['.TALLKit::dataKey('icon').']]:size-'.$value;
    }

    public function roundedSize(
        ?string $size = null,
        ?string $mode = null,
        ?bool $after = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['rounded-none', 'rounded-xs', 'rounded-sm', 'rounded-md', 'rounded-lg'],
            'sm' => ['rounded-xs', 'rounded-xs', 'rounded-sm', 'rounded-md', 'rounded-lg'],
            'md' => ['rounded-xs', 'rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-xl'],
            'lg' => ['rounded-sm', 'rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-xl'],
            'xl' => ['rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-2xl'],
            '2xl' => ['rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-2xl'],
            '3xl' => ['rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-2xl', 'rounded-3xl'],
            'none' => 'rounded-none',
            'full' => 'rounded-full',
        ]);

        return '[:where(&)]:'.$value.($after ? ' after:'.$value : '');
    }

    public function padding(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['1', '1.5', '2', '2.5', '3'],
            'sm' => ['1.5', '2', '2.5', '3', '3.5'],
            'md' => ['2', '2.5', '3', '3.5', '4'],
            'lg' => ['2.5', '3', '3.5', '4', '5'],
            'xl' => ['3', '3.5', '4', '5', '6'],
            '2xl' => ['3.5', '4', '5', '6', '7'],
            '3xl' => ['4', '5', '6', '7', '8'],
            'none' => '0',
        ]);

        return '[:where(&)]:p-'.$value;
    }

    public function paddingInline(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['px', '0.5', '1', '1.5', '2'],
            'sm' => ['1', '1.5', '2', '2.5', '3'],
            'md' => ['1.5', '2', '2.5', '3', '3.5'],
            'lg' => ['2', '2.5', '3', '3.5', '4'],
            'xl' => ['2.5', '3', '3.5', '4', '5'],
            '2xl' => ['3', '3.5', '4', '5', '6'],
            '3xl' => ['3.5', '4', '5', '6', '7'],
            'none' => '0',
        ]);

        return '[:where(&)]:px-'.$value;
    }

    public function paddingBlock(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['px', '0.5', '1', '1.5', '2'],
            'sm' => ['0.5', '1', '1.5', '2', '2.5'],
            'md' => ['1', '1.5', '2', '2.5', '3'],
            'lg' => ['1.5', '2', '2.5', '3', '3.5'],
            'xl' => ['2', '2.5', '3', '3.5', '4'],
            '2xl' => ['2.5', '3', '3.5', '4', '4.5'],
            '3xl' => ['3', '3.5', '4', '4.5', '5'],
            'none' => '0',
        ]);

        return '[:where(&)]:py-'.$value;
    }

    public function gap(
        ?string $size = null,
        ?string $mode = null,
    ) {
        $value = $this->incrementalByMode($size, $mode, [
            'xs' => ['px', '0.5', '1', '1.5', '2'],
            'sm' => ['0.5', '1', '1.5', '2', '2.5'],
            'md' => ['1', '1.5', '2', '2.5', '3'],
            'lg' => ['1.5', '2', '2.5', '3', '3.5'],
            'xl' => ['2', '2.5', '3', '3.5', '4'],
            '2xl' => ['2.5', '3', '3.5', '4', '5'],
            '3xl' => ['3', '3.5', '4', '5', '6'],
            'none' => '0',
        ]);

        return '[:where(&)]:gap-'.$value;
    }

    public function borderStyle(
        string|bool|null $style = null,
    ) {
        return match ($style) {
            true => 'border',
            'none' => 'border-0',
            'solid' => 'border border-solid',
            'dashed' => 'border border-dashed',
            'dotted' => 'border border-dotted',
            default => '',
        };
    }
}
