<?php

namespace TALLKit\Tests\Unit\Concerns;

use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithAttributesTest extends TestCase
{
    public function test_is_attribute_enabled(): void
    {
        foreach ([null, false, '', '0', 0, 'false'] as $value) {
            $this->assertFalse(TALLKit::isAttributeEnabled($value), var_export($value, true));
        }

        foreach ([true, 1, '1', 'true', 'yes', [], 0.0] as $value) {
            $this->assertTrue(TALLKit::isAttributeEnabled($value), var_export($value, true));
        }
    }

    public function test_attributes_merge_without_sets_returns_an_empty_bag(): void
    {
        $this->assertSame([], TALLKit::attributesMerge()->getAttributes());
        $this->assertSame([], TALLKit::attributesMerge(null, null)->getAttributes());
    }

    public function test_attributes_merge_drops_nulls_and_later_sets_win(): void
    {
        $bag = TALLKit::attributesMerge(
            ['id' => 'a', 'title' => 'first', 'class' => 'p-1', 'hidden' => null],
            null,
            new ComponentAttributeBag(['title' => 'second', 'class' => 'm-1', 'role' => null]),
        );

        $this->assertSame('a', $bag->get('id'));
        $this->assertSame('second', $bag->get('title'));
        $this->assertSame('p-1 m-1', $bag->get('class'));
        $this->assertFalse($bag->has('hidden'));
        $this->assertFalse($bag->has('role'));
    }

    public function test_attributes_merge_does_not_escape_values(): void
    {
        $bag = TALLKit::attributesMerge(['x-on:click' => "open = 'a'"], ['id' => 'b']);

        $this->assertSame("open = 'a'", $bag->get('x-on:click'));
    }

    public function test_attributes_from_item(): void
    {
        $bag = new ComponentAttributeBag(['label' => '<b>']);

        $this->assertSame(['label' => '<b>'], TALLKit::attributesFromItem($bag));
        $this->assertSame(['label' => '&lt;b&gt;'], TALLKit::attributesFromItem('<b>'));
        $this->assertSame(['title' => 'Home'], TALLKit::attributesFromItem('Home', 'title'));
        $this->assertSame(['label' => 5], TALLKit::attributesFromItem(5));
        $this->assertSame(
            ['label' => 'A &amp; B', 'disabled' => true, 'count' => 2],
            TALLKit::attributesFromItem(['label' => 'A & B', 'disabled' => true, 'count' => 2]),
        );
    }
}
