<?php

namespace TALLKit\Tests\Unit\Concerns;

use PHPUnit\Framework\Attributes\DataProvider;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithSizeTest extends TestCase
{
    public static function containerWidths(): array
    {
        return [
            ['xs', '[:where(&)]:max-w-xl'],
            ['sm', '[:where(&)]:max-w-2xl'],
            ['md', '[:where(&)]:max-w-4xl'],
            ['lg', '[:where(&)]:max-w-5xl'],
            ['xl', '[:where(&)]:max-w-6xl'],
            ['2xl', '[:where(&)]:max-w-7xl'],
            ['3xl', '[:where(&)]:max-w-[96rem]'],
            ['full', '[:where(&)]:max-w-none'],
            [null, '[:where(&)]:max-w-7xl'],
            [true, '[:where(&)]:max-w-7xl'],
            ['unknown', '[:where(&)]:max-w-7xl'],
        ];
    }

    #[DataProvider('containerWidths')]
    public function test_container_width(string|bool|null $size, string $expected): void
    {
        $this->assertSame($expected, TALLKit::containerWidth($size));
    }

    public function test_adjust_size(): void
    {
        $this->assertSame('sm', TALLKit::adjustSize());
        $this->assertSame('lg', TALLKit::adjustSize('md', 1));
        $this->assertSame('xs', TALLKit::adjustSize('xs', -1));
        $this->assertSame('3xl', TALLKit::adjustSize('2xl', 5));
        $this->assertSame('sm', TALLKit::adjustSize('unknown'));
        $this->assertSame('md', TALLKit::adjustSize('unknown', 0, ['a', 'md', 'b']));
        $this->assertNull(TALLKit::adjustSize('unknown', 0, ['a', 'b']));
        $this->assertNull(TALLKit::adjustSize('md', 0, ['a', 'b']));
        $this->assertNull(TALLKit::adjustSize(null, 0, ['a', 'b']));
    }

    public function test_font_size(): void
    {
        $this->assertSame('[:where(&)]:text-sm', TALLKit::fontSize());
        $this->assertSame('[:where(&)]:text-sm', TALLKit::fontSize('unknown'));
        $this->assertSame('[:where(&)]:text-[0.625rem]', TALLKit::fontSize('xs', 'small'));
        $this->assertSame('[:where(&)]:text-[0.5rem]', TALLKit::fontSize('xs', 'smallest'));
        $this->assertSame('[:where(&)]:text-4xl', TALLKit::fontSize('3xl', 'largest'));
        $this->assertSame('[:where(&)]:text-3xl', TALLKit::fontSize('3xl', 'large'));
        $this->assertSame('[:where(&)]:text-sm', TALLKit::fontSize('md', null, false));
        $this->assertSame('[:where(&)]:text-sm [:where(&)]:font-semibold', TALLKit::fontSize('md', null, true));
        $this->assertSame('[:where(&)]:text-[0.5rem] [:where(&)]:font-extralight', TALLKit::fontSize('xs', 'smallest', true));
        $this->assertSame('[:where(&)]:text-4xl [:where(&)]:font-extrabold', TALLKit::fontSize('3xl', 'largest', true));
    }

    public function test_width_and_height_use_dimension_scale(): void
    {
        $this->assertSame('[:where(&)]:w-10', TALLKit::width());
        $this->assertSame('[:where(&)]:w-3', TALLKit::width('md', 'smallest'));
        $this->assertSame('[:where(&)]:w-6', TALLKit::width('md', 'small'));
        $this->assertSame('[:where(&)]:w-14', TALLKit::width('md', 'large'));
        $this->assertSame('[:where(&)]:w-22', TALLKit::width('md', 'largest'));
        $this->assertSame('[:where(&)]:w-10', TALLKit::width('md', 'unknown'));
        $this->assertSame('[:where(&)]:w-0', TALLKit::width('none', 'largest'));
        $this->assertSame('[:where(&)]:w-full', TALLKit::width('full'));
        $this->assertSame('[:where(&)]:w-10', TALLKit::width('unknown'));
        $this->assertSame('[:where(&)]:h-40', TALLKit::height('3xl', 'largest'));
        $this->assertSame('[:where(&)]:h-2', TALLKit::height('xs', 'smallest'));
    }

    public function test_width_height(): void
    {
        $this->assertSame('[:where(&)]:size-6', TALLKit::widthHeight());
        $this->assertSame('[:where(&)]:size-28', TALLKit::widthHeight('3xl', 'largest'));
        $this->assertSame('[:where(&)]:size-full', TALLKit::widthHeight('full'));
        $this->assertSame('[:where(&)]:size-0', TALLKit::widthHeight('none'));
    }

    public function test_icon_size(): void
    {
        $this->assertSame('[&_[data-tallkit-icon]]:size-4', TALLKit::iconSize());
        $this->assertSame('[&_[data-tallkit-icon]]:size-8', TALLKit::iconSize('3xl', 'largest'));
        $this->assertSame('[&_[data-tallkit-icon]]:size-2', TALLKit::iconSize('xs', 'smallest'));
        $this->assertSame('[&_[data-tallkit-icon]]:size-full', TALLKit::iconSize('full'));
    }

    public function test_rounded_size(): void
    {
        $this->assertSame('[:where(&)]:rounded-md', TALLKit::roundedSize());
        $this->assertSame('[:where(&)]:rounded-none', TALLKit::roundedSize('xs', 'smallest'));
        $this->assertSame('[:where(&)]:rounded-full', TALLKit::roundedSize('full'));
        $this->assertSame(
            '[:where(&)]:rounded-3xl after:rounded-3xl before:rounded-3xl',
            TALLKit::roundedSize('3xl', 'largest', true, true),
        );
        $this->assertSame('[:where(&)]:rounded-md after:rounded-md', TALLKit::roundedSize(after: true));
        $this->assertSame('[:where(&)]:rounded-md before:rounded-md', TALLKit::roundedSize(before: true));
    }

    public function test_padding(): void
    {
        $this->assertSame('[:where(&)]:p-3', TALLKit::padding());
        $this->assertSame('[:where(&)]:p-8', TALLKit::padding('3xl', 'largest'));
        $this->assertSame('[:where(&)]:p-0', TALLKit::padding('none'));
        $this->assertSame('[:where(&)]:p-3', TALLKit::padding('full'));
        $this->assertSame('[:where(&)]:px-2.5', TALLKit::paddingInline());
        $this->assertSame('[:where(&)]:px-px', TALLKit::paddingInline('xs', 'smallest'));
        $this->assertSame('[:where(&)]:px-0', TALLKit::paddingInline('none'));
    }

    public function test_spacing_scale_utilities(): void
    {
        $this->assertSame('[:where(&)]:py-2', TALLKit::paddingBlock());
        $this->assertSame('[:where(&)]:ps-px', TALLKit::paddingStart('xs', 'smallest'));
        $this->assertSame('[:where(&)]:pe-5', TALLKit::paddingEnd('3xl', 'largest'));
        $this->assertSame('[:where(&)]:mt-2.5', TALLKit::marginTop('md', 'large'));
        $this->assertSame('[:where(&)]:mb-1.5', TALLKit::marginBottom('md', 'small'));
        $this->assertSame('[:where(&)]:gap-0', TALLKit::gap('none'));
        $this->assertSame('[:where(&)]:gap-y-2.5', TALLKit::gapBlock('lg'));
        $this->assertSame('[:where(&)]:gap-x-1.5', TALLKit::gapInline('sm'));
        $this->assertSame('[:where(&)]:space-y-3.5', TALLKit::spaceBlock('2xl'));
        $this->assertSame('[:where(&)]:space-x-3.5', TALLKit::spaceInline('3xl', 'small'));
    }

    public static function borderStyles(): array
    {
        return [
            [true, 'border'],
            ['none', 'border-0'],
            ['solid', 'border border-solid'],
            ['dashed', 'border border-dashed'],
            ['dotted', 'border border-dotted'],
            [null, ''],
            [false, ''],
            ['other', ''],
        ];
    }

    #[DataProvider('borderStyles')]
    public function test_border_style(string|bool|null $style, string $expected): void
    {
        $this->assertSame($expected, TALLKit::borderStyle($style));
    }

    public function test_generate_class_by_size(): void
    {
        $values = ['1', '2', '3', '4', '5', '6', '7'];

        $this->assertSame('', TALLKit::generateClassBySize('md'));
        $this->assertSame('', TALLKit::generateClassBySize('md', 'w'));
        $this->assertSame('', TALLKit::generateClassBySize('md', 'w', []));
        $this->assertSame('', TALLKit::generateClassBySize('md', null, $values));
        $this->assertSame('[:where(&)]:w-3', TALLKit::generateClassBySize(null, 'w', $values));
        $this->assertSame('[:where(&)]:w-1', TALLKit::generateClassBySize('xs', 'w', $values));
        $this->assertSame('[:where(&)]:w-7', TALLKit::generateClassBySize('3xl', 'w', $values));
        $this->assertSame('[:where(&)]:w-3', TALLKit::generateClassBySize('unknown', 'w', $values));
        $this->assertSame('', TALLKit::generateClassBySize('3xl', 'w', ['1', '2', '3']));
    }
}
