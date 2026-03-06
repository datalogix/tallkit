<?php

namespace TALLKit\View\Concerns;

trait HandlesSizes
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

    public function fontSize(
        ?string $size = null,
        ?string $mode = null,
        ?bool $weight = null,
    ) {
        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:text-[9px]'.($weight ? ' [:where(&)]:font-extralight' : ''),
                'sm' => '[:where(&)]:text-[10px]'.($weight ? ' [:where(&)]:font-extralight' : ''),
                default => '[:where(&)]:text-[11px]'.($weight ? ' [:where(&)]:font-light' : ''),
                'lg' => '[:where(&)]:text-xs'.($weight ? ' [:where(&)]:font-light' : ''),
                'xl' => '[:where(&)]:text-sm'.($weight ? ' [:where(&)]:font-normal' : ''),
                '2xl' => '[:where(&)]:text-base'.($weight ? ' [:where(&)]:font-normal' : ''),
                '3xl' => '[:where(&)]:text-lg'.($weight ? ' [:where(&)]:font-medium' : ''),
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:text-[10px]'.($weight ? ' [:where(&)]:font-light' : ''),
                'sm' => '[:where(&)]:text-[11px]'.($weight ? ' [:where(&)]:font-light' : ''),
                default => '[:where(&)]:text-xs'.($weight ? ' [:where(&)]:font-normal' : ''),
                'lg' => '[:where(&)]:text-sm'.($weight ? ' [:where(&)]:font-normal' : ''),
                'xl' => '[:where(&)]:text-base'.($weight ? ' [:where(&)]:font-medium' : ''),
                '2xl' => '[:where(&)]:text-lg'.($weight ? ' [:where(&)]:font-medium' : ''),
                '3xl' => '[:where(&)]:text-xl'.($weight ? ' [:where(&)]:font-semibold' : ''),
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:text-xs'.($weight ? ' [:where(&)]:font-normal' : ''),
                'sm' => '[:where(&)]:text-sm'.($weight ? ' [:where(&)]:font-medium' : ''),
                default => '[:where(&)]:text-base'.($weight ? ' [:where(&)]:font-medium' : ''),
                'lg' => '[:where(&)]:text-lg'.($weight ? ' [:where(&)]:font-semibold' : ''),
                'xl' => '[:where(&)]:text-xl'.($weight ? ' [:where(&)]:font-semibold' : ''),
                '2xl' => '[:where(&)]:text-2xl'.($weight ? ' [:where(&)]:font-bold' : ''),
                '3xl' => '[:where(&)]:text-3xl'.($weight ? ' [:where(&)]:font-bold' : ''),
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:text-sm'.($weight ? ' [:where(&)]:font-medium' : ''),
                'sm' => '[:where(&)]:text-base'.($weight ? ' [:where(&)]:font-medium' : ''),
                default => '[:where(&)]:text-lg'.($weight ? ' [:where(&)]:font-semibold' : ''),
                'lg' => '[:where(&)]:text-xl'.($weight ? ' [:where(&)]:font-semibold' : ''),
                'xl' => '[:where(&)]:text-2xl'.($weight ? ' [:where(&)]:font-bold' : ''),
                '2xl' => '[:where(&)]:text-3xl'.($weight ? ' [:where(&)]:font-bold' : ''),
                '3xl' => '[:where(&)]:text-4xl'.($weight ? ' [:where(&)]:font-extrabold' : ''),
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:text-[11px]'.($weight ? ' [:where(&)]:font-normal' : ''),
            'sm' => '[:where(&)]:text-xs'.($weight ? ' [:where(&)]:font-normal' : ''),
            default => '[:where(&)]:text-sm'.($weight ? ' [:where(&)]:font-medium' : ''),
            'lg' => '[:where(&)]:text-base'.($weight ? ' [:where(&)]:font-medium' : ''),
            'xl' => '[:where(&)]:text-lg'.($weight ? ' [:where(&)]:font-semibold' : ''),
            '2xl' => '[:where(&)]:text-xl'.($weight ? ' [:where(&)]:font-semibold' : ''),
            '3xl' => '[:where(&)]:text-2xl'.($weight ? ' [:where(&)]:font-bold' : ''),
        };
    }

    public function width(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($size === 'full') {
            return '[:where(&)]:w-full';
        }

        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:w-2',
                'sm' => '[:where(&)]:w-2.5',
                default => '[:where(&)]:w-3',
                'lg' => '[:where(&)]:w-3.5',
                'xl' => '[:where(&)]:w-4',
                '2xl' => '[:where(&)]:w-4.5',
                '3xl' => '[:where(&)]:w-5',
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:w-4',
                'sm' => '[:where(&)]:w-5',
                default => '[:where(&)]:w-6',
                'lg' => '[:where(&)]:w-7',
                'xl' => '[:where(&)]:w-8',
                '2xl' => '[:where(&)]:w-9',
                '3xl' => '[:where(&)]:w-10',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:w-10',
                'sm' => '[:where(&)]:w-12',
                default => '[:where(&)]:w-14',
                'lg' => '[:where(&)]:w-16',
                'xl' => '[:where(&)]:w-18',
                '2xl' => '[:where(&)]:w-20',
                '3xl' => '[:where(&)]:w-22',
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:w-14',
                'sm' => '[:where(&)]:w-18',
                default => '[:where(&)]:w-22',
                'lg' => '[:where(&)]:w-26',
                'xl' => '[:where(&)]:w-30',
                '2xl' => '[:where(&)]:w-36',
                '3xl' => '[:where(&)]:w-40',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:w-8',
            'sm' => '[:where(&)]:w-9',
            default => '[:where(&)]:w-10',
            'lg' => '[:where(&)]:w-12',
            'xl' => '[:where(&)]:w-14',
            '2xl' => '[:where(&)]:w-16',
            '3xl' => '[:where(&)]:w-18',
        };
    }

    public function height(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($size === 'full') {
            return '[:where(&)]:h-full';
        }

        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:h-2',
                'sm' => '[:where(&)]:h-2.5',
                default => '[:where(&)]:h-3',
                'lg' => '[:where(&)]:h-3.5',
                'xl' => '[:where(&)]:h-4',
                '2xl' => '[:where(&)]:h-4.5',
                '3xl' => '[:where(&)]:h-5',
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:h-4',
                'sm' => '[:where(&)]:h-5',
                default => '[:where(&)]:h-6',
                'lg' => '[:where(&)]:h-7',
                'xl' => '[:where(&)]:h-8',
                '2xl' => '[:where(&)]:h-9',
                '3xl' => '[:where(&)]:h-10',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:h-10',
                'sm' => '[:where(&)]:h-12',
                default => '[:where(&)]:h-14',
                'lg' => '[:where(&)]:h-16',
                'xl' => '[:where(&)]:h-18',
                '2xl' => '[:where(&)]:h-20',
                '3xl' => '[:where(&)]:h-22',
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:h-14',
                'sm' => '[:where(&)]:h-18',
                default => '[:where(&)]:h-22',
                'lg' => '[:where(&)]:h-26',
                'xl' => '[:where(&)]:h-30',
                '2xl' => '[:where(&)]:h-36',
                '3xl' => '[:where(&)]:h-40',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:h-8',
            'sm' => '[:where(&)]:h-9',
            default => '[:where(&)]:h-10',
            'lg' => '[:where(&)]:h-12',
            'xl' => '[:where(&)]:h-14',
            '2xl' => '[:where(&)]:h-16',
            '3xl' => '[:where(&)]:h-18',
        };
    }

    public function widthHeight(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($size === 'full') {
            return '[:where(&)]:size-full';
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:size-3',
                'sm' => '[:where(&)]:size-3.5',
                default => '[:where(&)]:size-4',
                'lg' => '[:where(&)]:size-4.5',
                'xl' => '[:where(&)]:size-5',
                '2xl' => '[:where(&)]:size-5.5',
                '3xl' => '[:where(&)]:size-6',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:size-6',
                'sm' => '[:where(&)]:size-8',
                default => '[:where(&)]:size-10',
                'lg' => '[:where(&)]:size-12',
                'xl' => '[:where(&)]:size-16',
                '2xl' => '[:where(&)]:size-20',
                '3xl' => '[:where(&)]:size-24',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:size-4',
            'sm' => '[:where(&)]:size-5',
            default => '[:where(&)]:size-6',
            'lg' => '[:where(&)]:size-7',
            'xl' => '[:where(&)]:size-8',
            '2xl' => '[:where(&)]:size-9',
            '3xl' => '[:where(&)]:size-10',
        };
    }

    public function iconSize(
        ?string $size = null
    ) {
        if ($size === 'full') {
            return '[&_[data-tallkit-icon]]:size-full';
        }

        return match ($size) {
            'xs' => '[&_[data-tallkit-icon]]:size-3',
            'sm' => '[&_[data-tallkit-icon]]:size-3.5',
            default => '[&_[data-tallkit-icon]]:size-4',
            'lg' => '[&_[data-tallkit-icon]]:size-4.5',
            'xl' => '[&_[data-tallkit-icon]]:size-5',
            '2xl' => '[&_[data-tallkit-icon]]:size-5.5',
            '3xl' => '[&_[data-tallkit-icon]]:size-6',
        };
    }

    public function roundedSize(
        ?string $size = null,
        ?string $mode = null,
        ?bool $after = null,
    ) {
        if ($size === 'full') {
            return '[:where(&)]:rounded-full'.($after ? ' after:rounded-full' : '');
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:rounded-xs'.($after ? ' after:rounded-xs' : ''),
                'sm' => '[:where(&)]:rounded-xs'.($after ? ' after:rounded-xs' : ''),
                default => '[:where(&)]:rounded-sm'.($after ? ' after:rounded-sm' : ''),
                'lg' => '[:where(&)]:rounded-sm'.($after ? ' after:rounded-sm' : ''),
                'xl' => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
                '2xl' => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
                '3xl' => '[:where(&)]:rounded-lg'.($after ? ' after:rounded-lg' : ''),
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
                'sm' => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
                default => '[:where(&)]:rounded-lg'.($after ? ' after:rounded-lg' : ''),
                'lg' => '[:where(&)]:rounded-lg'.($after ? ' after:rounded-lg' : ''),
                'xl' => '[:where(&)]:rounded-xl'.($after ? ' after:rounded-xl' : ''),
                '2xl' => '[:where(&)]:rounded-xl'.($after ? ' after:rounded-xl' : ''),
                '3xl' => '[:where(&)]:rounded-2xl'.($after ? ' after:rounded-2xl' : ''),
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:rounded-sm'.($after ? ' after:rounded-sm' : ''),
            'sm' => '[:where(&)]:rounded-sm'.($after ? ' after:rounded-sm' : ''),
            default => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
            'lg' => '[:where(&)]:rounded-md'.($after ? ' after:rounded-md' : ''),
            'xl' => '[:where(&)]:rounded-lg'.($after ? ' after:rounded-lg' : ''),
            '2xl' => '[:where(&)]:rounded-lg'.($after ? ' after:rounded-lg' : ''),
            '3xl' => '[:where(&)]:rounded-xl'.($after ? ' after:rounded-xl' : ''),
        };
    }

    public function padding(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:p-1',
                'sm' => '[:where(&)]:p-1.5',
                default => '[:where(&)]:p-2',
                'lg' => '[:where(&)]:p-2.5',
                'xl' => '[:where(&)]:p-3',
                '2xl' => '[:where(&)]:p-3.5',
                '3xl' => '[:where(&)]:p-4',
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:p-2',
                'sm' => '[:where(&)]:p-3',
                default => '[:where(&)]:p-4',
                'lg' => '[:where(&)]:p-5',
                'xl' => '[:where(&)]:p-6',
                '2xl' => '[:where(&)]:p-7',
                '3xl' => '[:where(&)]:p-8',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:p-6',
                'sm' => '[:where(&)]:p-8',
                default => '[:where(&)]:p-10',
                'lg' => '[:where(&)]:p-12',
                'xl' => '[:where(&)]:p-14',
                '2xl' => '[:where(&)]:p-16',
                '3xl' => '[:where(&)]:p-18',
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:p-10',
                'sm' => '[:where(&)]:p-14',
                default => '[:where(&)]:p-18',
                'lg' => '[:where(&)]:p-22',
                'xl' => '[:where(&)]:p-26',
                '2xl' => '[:where(&)]:p-30',
                '3xl' => '[:where(&)]:p-34',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:p-4',
            'sm' => '[:where(&)]:p-5',
            default => '[:where(&)]:p-6',
            'lg' => '[:where(&)]:p-7',
            'xl' => '[:where(&)]:p-8',
            '2xl' => '[:where(&)]:p-9',
            '3xl' => '[:where(&)]:p-10',
        };
    }

    public function paddingInline(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:px-1',
                'sm' => '[:where(&)]:px-1.5',
                default => '[:where(&)]:px-2',
                'lg' => '[:where(&)]:px-2.5',
                'xl' => '[:where(&)]:px-3',
                '2xl' => '[:where(&)]:px-3.5',
                '3xl' => '[:where(&)]:px-4',
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:px-2',
                'sm' => '[:where(&)]:px-3',
                default => '[:where(&)]:px-4',
                'lg' => '[:where(&)]:px-5',
                'xl' => '[:where(&)]:px-6',
                '2xl' => '[:where(&)]:px-7',
                '3xl' => '[:where(&)]:px-8',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:px-6',
                'sm' => '[:where(&)]:px-8',
                default => '[:where(&)]:px-10',
                'lg' => '[:where(&)]:px-12',
                'xl' => '[:where(&)]:px-14',
                '2xl' => '[:where(&)]:px-16',
                '3xl' => '[:where(&)]:px-18',
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:px-10',
                'sm' => '[:where(&)]:px-14',
                default => '[:where(&)]:px-18',
                'lg' => '[:where(&)]:px-22',
                'xl' => '[:where(&)]:px-26',
                '2xl' => '[:where(&)]:px-30',
                '3xl' => '[:where(&)]:px-34',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:px-4',
            'sm' => '[:where(&)]:px-5',
            default => '[:where(&)]:px-6',
            'lg' => '[:where(&)]:px-7',
            'xl' => '[:where(&)]:px-8',
            '2xl' => '[:where(&)]:px-9',
            '3xl' => '[:where(&)]:px-10',
        };
    }

    public function paddingBlock(
        ?string $size = null,
        ?string $mode = null,
    ) {
        if ($mode === 'smallest') {
            return match ($size) {
                'xs' => '[:where(&)]:py-px',
                'sm' => '[:where(&)]:py-0.5',
                default => '[:where(&)]:py-0.5',
                'lg' => '[:where(&)]:py-1',
                'xl' => '[:where(&)]:py-1',
                '2xl' => '[:where(&)]:py-1.5',
                '3xl' => '[:where(&)]:py-1.5',
            };
        }

        if ($mode === 'small') {
            return match ($size) {
                'xs' => '[:where(&)]:py-0.5',
                'sm' => '[:where(&)]:py-1',
                default => '[:where(&)]:py-1',
                'lg' => '[:where(&)]:py-1.5',
                'xl' => '[:where(&)]:py-1.5',
                '2xl' => '[:where(&)]:py-2',
                '3xl' => '[:where(&)]:py-2',
            };
        }

        if ($mode === 'large') {
            return match ($size) {
                'xs' => '[:where(&)]:py-2',
                'sm' => '[:where(&)]:py-2.5',
                default => '[:where(&)]:py-3',
                'lg' => '[:where(&)]:py-3.5',
                'xl' => '[:where(&)]:py-4',
                '2xl' => '[:where(&)]:py-4.5',
                '3xl' => '[:where(&)]:py-5',
            };
        }

        if ($mode === 'largest') {
            return match ($size) {
                'xs' => '[:where(&)]:py-3',
                'sm' => '[:where(&)]:py-4',
                default => '[:where(&)]:py-6',
                'lg' => '[:where(&)]:py-8',
                'xl' => '[:where(&)]:py-10',
                '2xl' => '[:where(&)]:py-12',
                '3xl' => '[:where(&)]:py-14',
            };
        }

        return match ($size) {
            'xs' => '[:where(&)]:py-1',
            'sm' => '[:where(&)]:py-1.5',
            default => '[:where(&)]:py-2',
            'lg' => '[:where(&)]:py-2.5',
            'xl' => '[:where(&)]:py-3',
            '2xl' => '[:where(&)]:py-3.5',
            '3xl' => '[:where(&)]:py-4',
        };
    }

    public function gap(
        ?string $size = null,
    ) {
        return match ($size) {
            'xs' => 'gap-1',
            'sm' => 'gap-1.5',
            default => 'gap-2',
            'lg' => 'gap-2.5',
            'xl' => 'gap-3',
            '2xl' => 'gap-3.5',
            '3xl' => 'gap-4',
        };
    }

    public function borderStyle(
        string|bool|null $style = null,
    ) {
        return match ($style) {
            true => 'border',
            'solid' => 'border border-solid',
            'dashed' => 'border border-dashed',
            'dotted' => 'border border-dotted',
            default => '',
        };
    }
}
