<?php

namespace TALLKit\Tests\Unit\Concerns;

use TALLKit\Facades\TALLKit;
use TALLKit\Tests\TestCase;

class InteractsWithElementTest extends TestCase
{
    public function test_element_props_are_all_null_defaults(): void
    {
        $props = TALLKit::elementProps();

        $this->assertSame(
            ['ariaLabel', 'iconDot', 'label', 'icon', 'prefix', 'suffix', 'iconTrailing', 'badge', 'prepend', 'append', 'kbd'],
            array_keys($props),
        );
        $this->assertSame([], array_filter($props, fn ($value) => $value !== null));
    }
}
