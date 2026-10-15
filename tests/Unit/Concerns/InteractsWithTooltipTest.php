<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
use PHPUnit\Framework\Attributes\DataProvider;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithTooltipTest extends TestCase
{
    private function tipId(string $text, ?string $kbd = null): string
    {
        return 'tallkit-tip-'.substr(md5($text."\n".$kbd), 0, 10);
    }

    public static function emptyTooltips(): array
    {
        return [
            [null],
            [false],
            [true],
            [''],
            [['array']],
            [new HtmlString('<b>  </b>')],
            [new HtmlString('')],
        ];
    }

    #[DataProvider('emptyTooltips')]
    public function test_empty_tooltip_returns_nothing(mixed $tooltip): void
    {
        $this->assertSame(
            ['attributes' => [], 'describedBy' => null, 'html' => null],
            TALLKit::tooltip($tooltip, new ComponentAttributeBag(['tooltip:position' => 'bottom'])),
        );
    }

    public function test_plain_text_tooltip_describes_the_element(): void
    {
        $id = $this->tipId('Save');

        $this->assertSame([
            'attributes' => [
                'data-tallkit-tooltip' => 'Save',
                'data-tallkit-tooltip-id' => $id,
            ],
            'describedBy' => $id,
            'html' => null,
        ], TALLKit::tooltip('Save', new ComponentAttributeBag));
    }

    public function test_numeric_tooltip_is_cast_to_string(): void
    {
        $this->assertSame('42', TALLKit::tooltip(42, new ComponentAttributeBag)['attributes']['data-tallkit-tooltip']);
    }

    public function test_htmlable_tooltip_keeps_html(): void
    {
        $result = TALLKit::tooltip(new HtmlString(' <b>Bold</b> &amp; more '), new ComponentAttributeBag);

        $this->assertSame('Bold & more', $result['attributes']['data-tallkit-tooltip']);
        $this->assertSame('<b>Bold</b> &amp; more', $result['html']);
        $this->assertSame($this->tipId('Bold & more'), $result['describedBy']);
    }

    public function test_htmlable_tooltip_without_tags_has_no_html(): void
    {
        $result = TALLKit::tooltip(new HtmlString('Plain'), new ComponentAttributeBag);

        $this->assertSame('Plain', $result['attributes']['data-tallkit-tooltip']);
        $this->assertNull($result['html']);
    }

    public function test_translated_tooltip(): void
    {
        app('translator')->addLines([
            'tips.save' => 'Save <kbd>S</kbd>',
            'tips.plain' => 'Translated plain',
        ], 'en');

        $html = TALLKit::tooltip('tips.save', new ComponentAttributeBag);

        $this->assertSame('Save S', $html['attributes']['data-tallkit-tooltip']);
        $this->assertSame('Save <kbd>S</kbd>', $html['html']);

        $plain = TALLKit::tooltip('tips.plain', new ComponentAttributeBag);

        $this->assertSame('Translated plain', $plain['attributes']['data-tallkit-tooltip']);
        $this->assertNull($plain['html']);
    }

    public function test_translation_returning_array_falls_back_to_the_key(): void
    {
        app('translator')->addLines(['tips.save' => 'Save'], 'en');

        $result = TALLKit::tooltip('tips', new ComponentAttributeBag);

        $this->assertSame('tips', $result['attributes']['data-tallkit-tooltip']);
        $this->assertNull($result['html']);
    }

    public function test_options_become_data_attributes(): void
    {
        $attributes = new ComponentAttributeBag([
            'tooltip:position' => 'bottom',
            'tooltip:align' => 'start',
            'tooltip:delay' => '300',
            'tooltip:variant' => 'inverse',
            'tooltip:color' => 'red',
            'tooltip:size' => 'sm',
            'tooltip:arrow' => 'false',
            'tooltip:kbd' => 'Ctrl+S',
            'tooltip:open' => 'hover',
            'tooltip:class' => 'p-2',
            'class' => 'ignored',
        ]);

        $id = $this->tipId('Save', 'Ctrl+S');

        $this->assertSame([
            'attributes' => [
                'data-tallkit-tooltip' => 'Save',
                'data-tallkit-tooltip-id' => $id,
                'data-tallkit-tooltip-position' => 'bottom',
                'data-tallkit-tooltip-align' => 'start',
                'data-tallkit-tooltip-delay' => 300,
                'data-tallkit-tooltip-variant' => 'inverse',
                'data-tallkit-tooltip-color' => 'red',
                'data-tallkit-tooltip-size' => 'sm',
                'data-tallkit-tooltip-arrow' => 'false',
                'data-tallkit-tooltip-kbd' => 'Ctrl+S',
                'data-tallkit-tooltip-open' => 'hover',
                'data-tallkit-tooltip-class' => 'p-2',
            ],
            'describedBy' => $id,
            'html' => null,
        ], TALLKit::tooltip('Save', $attributes));
    }

    public function test_arrow_options(): void
    {
        $arrow = fn (array $options) => TALLKit::tooltip('Tip', new ComponentAttributeBag($options))['attributes']['data-tallkit-tooltip-arrow'] ?? null;

        $this->assertNull($arrow([]));
        $this->assertSame('true', $arrow(['tooltip:arrow' => true]));
        $this->assertSame('true', $arrow(['tooltip:arrow' => 'yes']));
        $this->assertSame('false', $arrow(['tooltip:arrow' => false]));
        $this->assertSame('false', $arrow(['tooltip:no-arrow' => true]));
        $this->assertSame('false', $arrow(['tooltip:arrow' => true, 'tooltip:no-arrow' => '1']));
        $this->assertSame('true', $arrow(['tooltip:arrow' => true, 'tooltip:no-arrow' => 'false']));
        $this->assertNull($arrow(['tooltip:no-arrow' => false]));
    }

    public static function delays(): array
    {
        return [
            [100, 100],
            ['250', 250],
            ['1.5', 1],
            ['300ms', '300ms'],
            [' 1.5s ', '1.5s'],
            ['.5s', '.5s'],
            ['soon', null],
            ['10m', null],
            [true, null],
            [null, null],
        ];
    }

    #[DataProvider('delays')]
    public function test_delay(mixed $delay, int|string|null $expected): void
    {
        $attributes = TALLKit::tooltip('Tip', new ComponentAttributeBag(['tooltip:delay' => $delay]))['attributes'];

        $this->assertSame($expected, $attributes['data-tallkit-tooltip-delay'] ?? null);
    }

    public function test_kbd_must_be_a_non_empty_string(): void
    {
        $kbd = fn (mixed $value) => TALLKit::tooltip('Tip', new ComponentAttributeBag(['tooltip:kbd' => $value]))['attributes']['data-tallkit-tooltip-kbd'] ?? null;

        $this->assertNull($kbd(''));
        $this->assertNull($kbd(true));
        $this->assertSame('K', $kbd('K'));
    }

    public function test_tooltip_does_not_describe_when_it_repeats_the_name(): void
    {
        $result = TALLKit::tooltip('<b>Save</b>  File', new ComponentAttributeBag, ' save FILE ');

        $this->assertNull($result['describedBy']);
        $this->assertArrayNotHasKey('data-tallkit-tooltip-id', $result['attributes']);

        $this->assertNotNull(TALLKit::tooltip('Save', new ComponentAttributeBag, 'Delete')['describedBy']);
    }

    public function test_tooltip_does_not_describe_when_it_is_the_name_or_manual(): void
    {
        $this->assertNull(TALLKit::tooltip('Save', new ComponentAttributeBag, isName: true)['describedBy']);

        $manual = TALLKit::tooltip('Save', new ComponentAttributeBag(['tooltip:open' => 'manual']));

        $this->assertNull($manual['describedBy']);
        $this->assertSame('manual', $manual['attributes']['data-tallkit-tooltip-open']);
    }

    public function test_with_tooltip_returns_same_bag_when_there_is_nothing(): void
    {
        $attributes = new ComponentAttributeBag(['class' => 'a']);
        $tooltip = TALLKit::tooltip(null, $attributes);

        $this->assertSame($attributes, TALLKit::withTooltip($attributes, $tooltip));
    }

    public function test_with_tooltip_strips_options_even_without_tooltip(): void
    {
        $attributes = new ComponentAttributeBag(['class' => 'a', 'tooltip:position' => 'top']);

        $result = TALLKit::withTooltip($attributes, TALLKit::tooltip(null, $attributes));

        $this->assertSame(['class' => 'a'], $result->getAttributes());
    }

    public function test_with_tooltip_merges_attributes_and_described_by(): void
    {
        $attributes = new ComponentAttributeBag(['aria-describedby' => 'hint', 'tooltip:position' => 'left']);
        $tooltip = TALLKit::tooltip('Save', $attributes);
        $id = $tooltip['describedBy'];

        $result = TALLKit::withTooltip($attributes, $tooltip);

        $this->assertSame("hint {$id}", $result->get('aria-describedby'));
        $this->assertSame('Save', $result->get('data-tallkit-tooltip'));
        $this->assertSame('left', $result->get('data-tallkit-tooltip-position'));
        $this->assertNull($result->get('tooltip:position'));

        $undescribed = TALLKit::withTooltip($attributes, $tooltip, describe: false);

        $this->assertSame('hint', $undescribed->get('aria-describedby'));
        $this->assertSame('Save', $undescribed->get('data-tallkit-tooltip'));
    }

    public function test_with_described_by(): void
    {
        $attributes = new ComponentAttributeBag(['aria-describedby' => 'a  b', 'id' => 'x']);

        $this->assertSame($attributes, TALLKit::withDescribedBy($attributes, null));
        $this->assertSame($attributes, TALLKit::withDescribedBy($attributes, ''));
        $this->assertSame('a b c', TALLKit::withDescribedBy($attributes, 'c')->get('aria-describedby'));
        $this->assertSame('a b', TALLKit::withDescribedBy($attributes, 'b')->get('aria-describedby'));
        $this->assertSame('c', TALLKit::withDescribedBy(new ComponentAttributeBag, 'c')->get('aria-describedby'));
        $this->assertSame('x', TALLKit::withDescribedBy($attributes, 'c')->get('id'));
    }
}
