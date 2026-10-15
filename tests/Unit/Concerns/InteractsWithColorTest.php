<?php

namespace TALLKit\Tests\Unit\Concerns;

use PHPUnit\Framework\Attributes\DataProvider;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithColorTest extends TestCase
{
    public function test_is_color(): void
    {
        $this->assertTrue(TALLKit::isColor('red'));
        $this->assertTrue(TALLKit::isColor('stone'));
        $this->assertFalse(TALLKit::isColor('brand'));
        $this->assertFalse(TALLKit::isColor('Red'));
        $this->assertFalse(TALLKit::isColor(''));
        $this->assertFalse(TALLKit::isColor(null));
    }

    public function test_colors_include_configured_ones_without_duplicates(): void
    {
        $this->assertCount(21, TALLKit::colors());

        config(['tallkit.colors' => ['brand', 'red']]);

        $colors = TALLKit::colors();

        $this->assertCount(22, $colors);
        $this->assertSame('brand', end($colors));
        $this->assertSame(array_values($colors), $colors);
        $this->assertTrue(TALLKit::isColor('brand'));
    }

    public function test_colors_accept_null_config(): void
    {
        config(['tallkit.colors' => null]);

        $this->assertCount(21, TALLKit::colors());
    }

    public static function colorMethods(): array
    {
        return [
            ['controlFocusRing', [], 'focus-visible:outline-(--tk-ring)! focus-visible:ring-(--tk-ring-soft)!'],
            ['controlFocusRing', [true], '[&:is(:focus-visible,[aria-expanded=true])]:outline-(--tk-ring)! [&:is(:focus-visible,[aria-expanded=true])]:ring-(--tk-ring-soft)!'],
            ['controlFocusRingNested', [], 'has-[[data-tallkit-control]:focus-visible]:outline-(--tk-ring)! has-[[data-tallkit-control]:focus-visible]:ring-(--tk-ring-soft)!'],
            ['controlFocusRingNested', [true], 'has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:outline-(--tk-ring)! has-[[data-tallkit-control]:is(:focus-visible,[aria-expanded=true])]:ring-(--tk-ring-soft)!'],
            ['ring', [], 'ring-(--tk-ring)'],
            ['faintBackground', [], 'bg-(--tk-faint)'],
            ['ringBorder', [], 'border-(--tk-ring)'],
            ['checkedBackground', [], 'checked:bg-(--tk-fill)'],
            ['checkedBackground', [true], 'has-[input:checked]:bg-(--tk-fill)'],
            ['checkedForeground', [], 'text-(--tk-on-fill)'],
            ['checkedDot', [], 'bg-(--tk-on-fill)'],
            ['background', [], 'bg-(--tk-fill) text-(--tk-on-fill)'],
            ['border', [], 'border-(--tk-fill)'],
            ['text', [], 'text-(--tk-text)'],
            ['text', ['hover:'], 'hover:text-(--tk-text)'],
            ['textStrong', [], 'text-(--tk-solid)'],
            ['mutedBackground', [], 'bg-(--tk-soft) [&:is(a)]:hover:bg-(--tk-soft-hover)'],
            ['mutedBackground', ['button'], 'bg-(--tk-soft) [&:is(button)]:hover:bg-(--tk-soft-hover)'],
            ['mutedText', [], 'text-(--tk-on-soft)'],
            ['solidBackground', [], 'bg-(--tk-solid) text-(--tk-on-solid) [&:is(button)]:hover:bg-(--tk-solid-hover)'],
            ['solidBackground', ['a'], 'bg-(--tk-solid) text-(--tk-on-solid) [&:is(a)]:hover:bg-(--tk-solid-hover)'],
            ['pastelBackground', [], 'bg-(--tk-pastel) text-(--tk-on-pastel)'],
        ];
    }

    #[DataProvider('colorMethods')]
    public function test_color_methods(string $method, array $arguments, string $expected): void
    {
        $this->assertSame("tk-color-blue {$expected}", TALLKit::$method('blue', ...$arguments));
        $this->assertNull(TALLKit::$method('brand', ...$arguments));
        $this->assertNull(TALLKit::$method(null, ...$arguments));
    }

    public function test_multiline_color_methods(): void
    {
        $interactive = preg_split('/\s+/', trim(TALLKit::interactiveBackground('red')));

        $this->assertSame([
            'tk-color-red', 'bg-(--tk-solid)', 'text-(--tk-on-solid)',
            'hover:bg-(--tk-solid-hover)', '[&[data-active]]:bg-(--tk-solid-hover)',
        ], $interactive);
        $this->assertNull(TALLKit::interactiveBackground('brand'));

        $slider = preg_split('/\s+/', trim(TALLKit::sliderFocusRing('red')));

        $this->assertSame([
            'tk-color-red',
            'focus-visible:[&::-webkit-slider-thumb]:outline-(--tk-ring)',
            'focus-visible:[&::-webkit-slider-thumb]:ring-(--tk-ring-soft)',
            'focus-visible:[&::-moz-range-thumb]:outline-(--tk-ring)',
            'focus-visible:[&::-moz-range-thumb]:ring-(--tk-ring-soft)',
        ], $slider);
        $this->assertNull(TALLKit::sliderFocusRing(null));
    }

    public function test_configured_color_gets_classes(): void
    {
        config(['tallkit.colors' => ['brand']]);

        $this->assertSame('tk-color-brand ring-(--tk-ring)', TALLKit::ring('brand'));
    }

    public static function frameVariants(): array
    {
        return [
            ['none', ''],
            ['accent', 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)]'],
            ['inverse', 'bg-zinc-800 dark:bg-zinc-100 [:where(&)]:text-white/85 dark:[:where(&)]:text-zinc-900'],
            ['strong', 'bg-zinc-200 dark:bg-zinc-950 [:where(&)]:text-zinc-900 dark:[:where(&)]:text-white'],
            ['subtle', 'bg-white dark:bg-zinc-900 [:where(&)]:text-zinc-700/70 dark:[:where(&)]:text-white/70'],
            ['ghost', 'bg-transparent [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85'],
            ['green', 'tk-color-green tk-frame bg-(--tk-frame) text-(--tk-on-frame)'],
            [null, 'bg-zinc-50 dark:bg-zinc-800 [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85'],
            ['unknown', 'bg-zinc-50 dark:bg-zinc-800 [:where(&)]:text-zinc-800/85 dark:[:where(&)]:text-white/85'],
        ];
    }

    #[DataProvider('frameVariants')]
    public function test_frame_classes(?string $variant, string $expected): void
    {
        $this->assertSame($expected, TALLKit::frameClasses($variant));
    }

    public function test_text_neutral(): void
    {
        $this->assertSame('text-zinc-900 dark:text-white', TALLKit::textNeutral('emphasis'));
        $this->assertSame('text-zinc-800 dark:text-white', TALLKit::textNeutral('strong'));
        $this->assertSame('text-zinc-500 dark:text-zinc-400', TALLKit::textNeutral('subtle'));
        $this->assertSame('text-zinc-500 dark:text-zinc-400', TALLKit::textNeutral('muted'));
        $this->assertSame('text-zinc-700 dark:text-white/80', TALLKit::textNeutral());
        $this->assertSame('hover:text-zinc-900 dark:hover:text-white', TALLKit::textNeutral('emphasis', 'hover:'));
    }

    public function test_background_neutral(): void
    {
        $this->assertSame('bg-zinc-800/5 dark:bg-white/10', TALLKit::backgroundNeutral('faint'));
        $this->assertSame('bg-zinc-800/20 dark:bg-white/20', TALLKit::backgroundNeutral('strong'));
        $this->assertSame('bg-zinc-800/10 dark:bg-white/10', TALLKit::backgroundNeutral());
        $this->assertSame('hover:bg-zinc-800/10 dark:hover:bg-white/10', TALLKit::backgroundNeutral(null, 'hover:'));
    }
}
