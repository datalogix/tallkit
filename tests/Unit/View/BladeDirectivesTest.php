<?php

namespace TALLKit\Tests\Unit\View;

use Illuminate\Support\Facades\Blade;
use TALLKit\Tests\TestCase;
use TALLKit\View\BladeDirectives;

class BladeDirectivesTest extends TestCase
{
    public function test_directives_are_registered(): void
    {
        $directives = Blade::getCustomDirectives();

        $this->assertArrayHasKey('scope', $directives);
        $this->assertArrayHasKey('endscope', $directives);
    }

    public function test_scope_with_name_only(): void
    {
        $this->assertSame(
            "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot('row', function () use (\$__env, \$__bladeCompiler) { ?>",
            BladeDirectives::scope("'row'"),
        );
    }

    public function test_scope_with_plain_arguments(): void
    {
        $this->assertSame(
            "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot('row', function (\$item, \$index) use (\$__env, \$__bladeCompiler) { ?>",
            BladeDirectives::scope("'row', \$item, \$index"),
        );
    }

    public function test_scope_with_parenthesized_arguments_and_uses(): void
    {
        $this->assertSame(
            "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot('row', function (\$item, array \$options = ['a' => [1, 2]]) use (\$user, \$__env, \$__bladeCompiler) { ?>",
            BladeDirectives::scope("'row', (\$item, array \$options = ['a' => [1, 2]]), (\$user, \$__env)"),
        );
    }

    public function test_scope_keeps_commas_inside_nested_expressions(): void
    {
        $this->assertSame(
            "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot(\$names['a,b'] ?? f(1, 2), function (\$x) use (\$__env, \$__bladeCompiler) { ?>",
            BladeDirectives::scope("\$names['a,b'] ?? f(1, 2), \$x"),
        );
    }

    public function test_endscope(): void
    {
        $this->assertSame('<?php }); ?>', BladeDirectives::endscope());
    }

    public function test_compiled_scope_renders_a_callable_slot(): void
    {
        $compiled = Blade::compileString("@scope('row', \$item)<b>{{ \$item }}</b>@endscope");

        $this->assertStringContainsString("\$__env->slot('row', function (\$item) use (\$__env, \$__bladeCompiler) { ?>", $compiled);
        $this->assertStringContainsString('<?php }); ?>', $compiled);
    }
}
