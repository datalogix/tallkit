<?php

namespace TALLKit\View\Concerns;

trait HandlesSize
{
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

    public function fontSize(?string $size = null, ?bool $weight = null)
    {
        return match ($size) {
            'xs' => '[:where(&)]:text-[11px] '.($weight ? '[:where(&)]:font-normal' : ''),
            'sm' => '[:where(&)]:text-xs '.($weight ? '[:where(&)]:font-normal' : ''),
            default => '[:where(&)]:text-sm '.($weight ? '[:where(&)]:font-medium' : ''),
            'lg' => '[:where(&)]:text-base '.($weight ? '[:where(&)]:font-medium' : ''),
            'xl' => '[:where(&)]:text-lg '.($weight ? '[:where(&)]:font-semibold' : ''),
            '2xl' => '[:where(&)]:text-xl '.($weight ? '[:where(&)]:font-semibold' : ''),
            '3xl' => '[:where(&)]:text-2xl '.($weight ? '[:where(&)]:font-bold' : ''),
        };
    }
}
