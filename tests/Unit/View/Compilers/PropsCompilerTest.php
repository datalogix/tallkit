<?php

namespace TALLKit\Tests\Unit\View\Compilers;

use Illuminate\View\ComponentAttributeBag;
use TALLKit\Tests\TestCase;
use TALLKit\View\Compilers\PropsCompiler;

class PropsCompilerTest extends TestCase
{
    protected function expected(string $props): string
    {
        return "<?php \$__tkProps = {$props}; "
            .'foreach (\TALLKit\View\Compilers\PropsCompiler::unescape(get_defined_vars(), $__tkProps) as $__tkName => $__tkValue) { $$__tkName = $__tkValue; } '
            .'unset($__tkName, $__tkValue); ?>'."\n"
            .'@props($__tkProps)'."\n"
            .'<?php unset($__tkProps); ?>';
    }

    public function test_compile_rewrites_props_directive(): void
    {
        $this->assertSame(
            "a\n".$this->expected("['size' => null, 'label']")."\nb",
            PropsCompiler::compile("a\n@props(['size' => null, 'label'])\nb"),
        );
    }

    public function test_compile_allows_space_before_parenthesis_and_multiple_directives(): void
    {
        $this->assertSame(
            $this->expected("['a']").' '.$this->expected("['b']"),
            PropsCompiler::compile("@props \t(['a']) @props(['b'])"),
        );
    }

    public function test_compile_handles_nested_parentheses_strings_and_comments(): void
    {
        $props = <<<'PHP'
            [
                'a' => f(1, (2)),
                'b' => ')\'(',
                'c' => "\")",
                // a ) comment
                # another ) comment
                /* block ) comment */
                'd' => null,
            ]
            PHP;

        $this->assertSame($this->expected($props).'tail', PropsCompiler::compile("@props({$props})tail"));
    }

    public function test_compile_ignores_escaped_and_prefixed_directives(): void
    {
        $value = "@@props(['a']) x@props(['b']) {{ \$x }}";

        $this->assertSame($value, PropsCompiler::compile($value));
    }

    public function test_compile_leaves_unbalanced_directive_untouched(): void
    {
        foreach (["@props(['a'", "@props(['a' // open", "@props(['a' /* open"] as $value) {
            $this->assertSame($value, PropsCompiler::compile($value));
        }
    }

    public function test_unescape_without_attribute_bag(): void
    {
        $this->assertSame([], PropsCompiler::unescape([], ['a']));
        $this->assertSame([], PropsCompiler::unescape(['attributes' => ['a' => 1]], ['a']));
    }

    public function test_unescape_returns_nothing_when_unchanged(): void
    {
        $attributes = new ComponentAttributeBag(['label' => 'plain', 'class' => '&amp;', 'items' => [1]]);

        $this->assertSame([], PropsCompiler::unescape(['attributes' => $attributes], ['label', 'items' => []]));
    }

    public function test_unescape_decodes_escaped_prop_values(): void
    {
        $attributes = new ComponentAttributeBag(['label' => 'Tom &amp; &quot;Jerry&#039;', 'class' => '&amp;', 0 => 'x']);

        $result = PropsCompiler::unescape(
            ['attributes' => $attributes, 'label' => 'Tom &amp; &quot;Jerry&#039;'],
            ['label' => null],
        );

        $this->assertSame('Tom & "Jerry\'', $result['label']);
        $this->assertSame(
            ['label' => 'Tom & "Jerry\'', 'class' => '&amp;', 0 => 'x'],
            $result['attributes']->getAttributes(),
        );
    }

    public function test_unescape_does_not_override_variable_from_other_source(): void
    {
        $attributes = new ComponentAttributeBag(['label' => 'a &amp; b']);

        $result = PropsCompiler::unescape(['attributes' => $attributes, 'label' => 'other'], ['label']);

        $this->assertArrayNotHasKey('label', $result);
        $this->assertSame(['label' => 'a & b'], $result['attributes']->getAttributes());
    }

    public function test_unescape_renames_kebab_attribute_to_declared_camel_prop(): void
    {
        $attributes = new ComponentAttributeBag(['icon-trailing' => 'arrow', 'data-x' => '1']);

        $result = PropsCompiler::unescape(['attributes' => $attributes], ['iconTrailing' => null]);

        $this->assertSame(['attributes'], array_keys($result));
        $this->assertSame(['iconTrailing' => 'arrow', 'data-x' => '1'], $result['attributes']->getAttributes());
    }

    public function test_unescape_keeps_kebab_attribute_when_camel_already_set(): void
    {
        $attributes = new ComponentAttributeBag(['icon-trailing' => 'kebab', 'iconTrailing' => 'camel']);

        $this->assertSame([], PropsCompiler::unescape(['attributes' => $attributes], ['iconTrailing' => null]));
    }

    public function test_unescape_keeps_kebab_attribute_when_prop_is_declared_kebab(): void
    {
        $attributes = new ComponentAttributeBag(['icon-trailing' => 'arrow']);

        $this->assertSame([], PropsCompiler::unescape(['attributes' => $attributes], ['icon-trailing']));
    }
}
