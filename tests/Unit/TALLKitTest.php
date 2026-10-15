<?php

namespace TALLKit\Tests\Unit;

use Illuminate\View\ComponentSlot;
use TALLKit\Facades\TALLKit;
use TALLKit\TALLKit as TALLKitClass;
use TALLKit\Tests\TestCase;
use TALLKit\View\ClassBuilder;

class TALLKitTest extends TestCase
{
    public function test_it_is_resolved_as_singleton_and_alias(): void
    {
        $this->assertSame(app(TALLKitClass::class), app('tallkit'));
        $this->assertInstanceOf(TALLKitClass::class, TALLKit::getFacadeRoot());
    }

    public function test_classes_returns_class_builder(): void
    {
        $this->assertInstanceOf(ClassBuilder::class, TALLKit::classes('a', 'b'));
    }

    public function test_is_slot(): void
    {
        $this->assertTrue(TALLKit::isSlot(new ComponentSlot('x')));
        $this->assertFalse(TALLKit::isSlot('x'));
    }

    public function test_has_content(): void
    {
        $this->assertTrue(TALLKit::hasContent(new ComponentSlot('x')));
        $this->assertFalse(TALLKit::hasContent(new ComponentSlot('<!-- c -->')));
        $this->assertTrue(TALLKit::hasContent(0));
        $this->assertTrue(TALLKit::hasContent('a'));
        $this->assertFalse(TALLKit::hasContent(false));
        $this->assertFalse(TALLKit::hasContent(''));
        $this->assertFalse(TALLKit::hasContent(null));
        $this->assertFalse(TALLKit::hasContent([]));
    }
}
