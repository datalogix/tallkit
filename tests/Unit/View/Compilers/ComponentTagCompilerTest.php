<?php

namespace TALLKit\Tests\Unit\View\Compilers;

use TALLKit\Tests\TestCase;
use TALLKit\View\Compilers\ComponentTagCompiler;

class ComponentTagCompilerTest extends TestCase
{
    protected function compile(string $value): string
    {
        return app('tallkit.compiler')->compile($value);
    }

    public function test_container_binding(): void
    {
        $this->assertInstanceOf(ComponentTagCompiler::class, app('tallkit.compiler'));
    }

    public function test_compiles_opening_and_closing_tags(): void
    {
        $compiled = $this->compile('<tk:button type="submit" :disabled="$busy">Save</tk:button>');

        $this->assertStringContainsString("AnonymousComponent', 'tallkit::button'", $compiled);
        $this->assertStringContainsString("'type' => 'submit'", $compiled);
        $this->assertStringContainsString("'disabled' => \$busy", $compiled);
        $this->assertStringContainsString('Save', $compiled);
        $this->assertStringEndsWith(' @endComponentClass##END-COMPONENT-CLASS##', $compiled);
        $this->assertStringNotContainsString('<tk:', $compiled);
        $this->assertStringNotContainsString('</tk:', $compiled);
    }

    public function test_compiles_nested_names_and_closing_with_spaces(): void
    {
        $compiled = $this->compile('<tk:accordion.item>x</ tk:accordion.item >');

        $this->assertStringContainsString("AnonymousComponent', 'tallkit::accordion.item'", $compiled);
        $this->assertStringContainsString('@endComponentClass##END-COMPONENT-CLASS##', $compiled);
    }

    public function test_compiles_self_closing_tags(): void
    {
        $compiled = $this->compile('<tk:icon name="check" />');

        $this->assertStringContainsString("AnonymousComponent', 'tallkit::icon'", $compiled);
        $this->assertStringContainsString("'name' => 'check'", $compiled);
        $this->assertStringEndsWith("\n@endComponentClass##END-COMPONENT-CLASS##", $compiled);
        $this->assertStringNotContainsString('@slot', $compiled);
    }

    public function test_self_closing_tag_with_slot_attribute_is_wrapped_in_a_slot(): void
    {
        $compiled = $this->compile('<tk:icon slot="icon" name="check" />');

        $this->assertStringStartsWith("@slot('icon') ", $compiled);
        $this->assertStringEndsWith('@endComponentClass##END-COMPONENT-CLASS## @endslot', $compiled);
        $this->assertStringNotContainsString("'slot' =>", $compiled);
    }

    public function test_supports_class_style_attributes_echo_and_short_bindings(): void
    {
        $compiled = $this->compile(<<<'BLADE'
            <tk:badge @class(['a', 'b' => $on]) @style(['color: red']) {{ $attributes->except('x') }} :$color data-x=1>Hi</tk:badge>
            BLADE);

        $this->assertStringContainsString("AnonymousComponent', 'tallkit::badge'", $compiled);
        $this->assertStringContainsString("'class' => \Illuminate\Support\Arr::toCssClasses(['a', 'b' => \$on])", $compiled);
        $this->assertStringContainsString("'style' => \Illuminate\Support\Arr::toCssStyles(['color: red'])", $compiled);
        $this->assertStringContainsString("'attributes' => \$attributes->except('x')", $compiled);
        $this->assertStringContainsString("'color' => \$color", $compiled);
        $this->assertStringContainsString("'data-x' => '1'", $compiled);
    }

    public function test_leaves_other_tags_untouched(): void
    {
        $this->assertSame('<div tk:foo="1"></div><tkx:a />', $this->compile('<div tk:foo="1"></div><tkx:a />'));
    }
}
