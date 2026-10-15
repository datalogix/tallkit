<?php

namespace TALLKit\Tests\Unit\View;

use TALLKit\Tests\TestCase;
use TALLKit\View\ClassBuilder;

class ClassBuilderTest extends TestCase
{
    public function test_empty_builder_renders_empty_string(): void
    {
        $this->assertSame('', (string) new ClassBuilder);
        $this->assertSame('', (string) new ClassBuilder(''));
        $this->assertSame('', (string) new ClassBuilder([]));
    }

    public function test_constructor_accepts_string_and_array(): void
    {
        $this->assertSame('a b', (string) new ClassBuilder('a  b'));
        $this->assertSame('a b', (string) new ClassBuilder(['a', 'b']));
    }

    public function test_flattens_conditional_nested_and_scalar_values(): void
    {
        $builder = new ClassBuilder([
            'a',
            'on' => true,
            'off' => false,
            ['b', ['c', 'nested' => 1, 'none' => 0]],
            null,
            false,
            0,
            ' d  e ',
        ]);

        $this->assertSame('a on b c nested 0 d e', (string) $builder);
    }

    public function test_add_is_chainable_and_removes_duplicates(): void
    {
        $builder = new ClassBuilder('a b');

        $this->assertSame($builder, $builder->add('b c', ['a', 'd' => true]));
        $this->assertSame('a b c d', (string) $builder);
    }

    public function test_is_conditionable(): void
    {
        $builder = (new ClassBuilder('a'))
            ->when(true, fn ($b) => $b->add('yes'))
            ->when(false, fn ($b) => $b->add('no'));

        $this->assertSame('a yes', (string) $builder);
    }
}
