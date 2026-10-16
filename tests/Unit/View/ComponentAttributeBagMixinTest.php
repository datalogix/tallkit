<?php

namespace TALLKit\Tests\Unit\View;

use Illuminate\View\ComponentAttributeBag;
use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;
use TALLKit\View\ClassCache;

class ComponentAttributeBagMixinTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['tallkit.cache_classes' => false]);
        ClassCache::flush();
    }

    public function test_pluck_returns_and_removes_the_attribute(): void
    {
        $bag = new ComponentAttributeBag(['name' => 'email', 'type' => 'text']);

        $this->assertSame('email', $bag->pluck('name'));
        $this->assertFalse($bag->has('name'));
        $this->assertEquals(['type' => 'text'], $bag->getAttributes());
    }

    public function test_pluck_falls_back_to_default_or_closure(): void
    {
        $bag = new ComponentAttributeBag;

        $this->assertNull($bag->pluck('missing'));
        $this->assertSame('x', $bag->pluck('missing', 'x'));
        $this->assertSame('y', $bag->pluck('missing', fn () => 'y'));
    }

    public function test_classes_merges_defaults_with_attribute_class(): void
    {
        $bag = new ComponentAttributeBag(['class' => 'p-4 font-bold']);

        $this->assertSame($bag, $bag->classes('p-2', ['text-sm' => true, 'hidden' => false]));
        $this->assertSame('text-sm p-4 font-bold', $bag->get('class'));
    }

    public function test_classes_without_attribute_class(): void
    {
        $bag = (new ComponentAttributeBag)->classes('a', ['b']);

        $this->assertSame('a b', $bag->get('class'));
    }

    public function test_enabled(): void
    {
        $bag = new ComponentAttributeBag(['a' => true, 'b' => 'false', 'c' => '0', 'd' => 'yes', 'e' => '']);

        $this->assertTrue($bag->enabled('a'));
        $this->assertFalse($bag->enabled('b'));
        $this->assertFalse($bag->enabled('c'));
        $this->assertTrue($bag->enabled('d'));
        $this->assertFalse($bag->enabled('e'));
        $this->assertFalse($bag->enabled('missing'));
    }

    public function test_with_props_forwards_scope_and_attribute_values(): void
    {
        $bag = new ComponentAttributeBag(['icon-trailing' => 'x', 'size' => 'sm', 'class' => 'c']);

        $result = $bag->withProps(
            ['label' => '<b>', 'size' => 'lg'],
            ['label' => null, 'iconTrailing' => null, 'size' => null],
            ['label' => 'ignored', 'missing' => null],
        );

        $this->assertEquals(
            ['class' => 'c', 'label' => '<b>', 'iconTrailing' => 'x', 'size' => 'lg'],
            $result->getAttributes(),
        );
    }

    public function test_with_element_field_and_field_control_props(): void
    {
        $bag = new ComponentAttributeBag(['icon-trailing' => 'arrow', 'data-x' => '1']);

        $this->assertEquals(['data-x' => '1', 'iconTrailing' => 'arrow', 'label' => 'L'], $bag->withElementProps(['label' => 'L'])->getAttributes());
        $this->assertEquals(['icon-trailing' => 'arrow', 'data-x' => '1', 'help' => 'H'], $bag->withFieldProps(['help' => 'H'])->getAttributes());
        $this->assertEquals(['data-x' => '1', 'iconTrailing' => 'arrow', 'kbd' => 'K'], $bag->withFieldControlProps(['kbd' => 'K'])->getAttributes());
        $this->assertEquals(['data-x' => '1', 'extra' => 'E'], $bag->withFieldControlProps(['extra' => 'E', 'iconTrailing' => null], ['extra' => null])->except('iconTrailing')->getAttributes());
    }

    public function test_without_field_prefixes(): void
    {
        $bag = new ComponentAttributeBag([
            'class' => 'a',
            'label:class' => 'b',
            'icon:size' => 'sm',
            'tooltip:position' => 'top',
            'custom:x' => 'y',
            'id' => 'i',
        ]);

        $this->assertEquals(['class' => 'a', 'custom:x' => 'y', 'id' => 'i'], $bag->withoutFieldPrefixes()->getAttributes());
        $this->assertEquals(['class' => 'a', 'id' => 'i'], $bag->withoutFieldPrefixes(['custom:'])->getAttributes());
    }

    public function test_field_context(): void
    {
        $bag = new ComponentAttributeBag(['name' => 'user.first_name', 'placeholder' => true]);

        [$name, $fieldName, $label, $placeholder, $invalid, $wireModel, $id] = $bag->fieldContext(null, 'my-id');

        $this->assertSame('user.first_name', $name);
        $this->assertSame('user.first_name', $fieldName);
        $this->assertSame('First Name', $label);
        $this->assertSame('First Name', $placeholder);
        $this->assertFalse($invalid);
        $this->assertFalse($wireModel);
        $this->assertSame('my-id', $id);
    }

    public function test_wire_key_only_while_rendering_livewire(): void
    {
        $bag = new ComponentAttributeBag(['id' => 'a']);

        $this->assertSame($bag, $bag->wireKey('k'));

        TALLKit::partialMock()->shouldReceive('livewireRendering')->andReturn(true);

        $this->assertEquals(['id' => 'a', 'wire:key' => 'k'], $bag->wireKey('k')->getAttributes());
        $this->assertSame($bag, $bag->wireKey(null));
    }

    public function test_prefixed_strips_prefix(): void
    {
        $bag = new ComponentAttributeBag(['icon:class' => 'a', 'icon:size' => 'sm', 'label:class' => 'b', 'id' => 'x']);

        $this->assertEquals(['class' => 'a', 'size' => 'sm'], $bag->prefixed('icon:')->getAttributes());
        $this->assertEquals(['icon:class' => 'a', 'icon:size' => 'sm'], $bag->prefixed('icon:', true)->getAttributes());
        $this->assertEquals(['i-class' => 'a', 'i-size' => 'sm'], $bag->prefixed('icon:', 'i-')->getAttributes());
        $this->assertEquals([], $bag->prefixed('none:')->getAttributes());
    }

    public function test_prefixed_with_nested_prefixes(): void
    {
        $bag = new ComponentAttributeBag([
            'field:class' => 'f',
            'label:class' => '<l>',
            'help:id' => 'h',
        ]);

        $this->assertEquals(
            ['class' => 'f', 'label:class' => '<l>', 'hint:id' => 'h'],
            $bag->prefixed('field:', with: ['label:', 'hint:' => 'help:'])->getAttributes(),
        );
    }

    public function test_data_key(): void
    {
        $bag = new ComponentAttributeBag(['id' => 'a']);

        $this->assertSame($bag, $bag->dataKey());
        $this->assertEquals(['id' => 'a', 'data-tallkit-button' => true], $bag->dataKey('button')->getAttributes());
        $this->assertEquals(['id' => 'a', 'data-tallkit-size' => 'sm'], $bag->dataKey('size', 'sm')->getAttributes());
    }
}
