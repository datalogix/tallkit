<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use TALLKit\Tests\TestCase;

class ComponentRenderingTest extends TestCase
{
    public function test_renders_tk_tags_through_the_blade_compiler(): void
    {
        $html = Blade::render('<tk:badge data-x="1">Hello</tk:badge>');

        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('data-x="1"', $html);
        $this->assertStringNotContainsString('<tk:', $html);
    }

    public function test_renders_self_closing_tags_in_a_named_slot(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/views', 'tkviewtest');

        $html = Blade::render('<x-tkviewtest::wrapper><tk:badge slot="extra" data-y="2" /> body</x-tkviewtest::wrapper>');

        $this->assertMatchesRegularExpression('/<section>.*data-y="2".*<\/section>\s*<main>\s*body\s*<\/main>/s', $html);
    }

    public function test_scope_directive_passes_arguments_to_the_slot(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/views', 'tkviewtest');

        $html = Blade::render(
            "<x-tkviewtest::rows :items=\"['a', 'b']\">@scope('row', (\$item, \$index), (\$suffix))<i>{{ \$index }}={{ \$item }}{{ \$suffix }}</i>@endscope</x-tkviewtest::rows>",
            ['suffix' => '!'],
        );

        $this->assertStringContainsString('<i>0=a!</i>', $html);
        $this->assertStringContainsString('<i>1=b!</i>', $html);
    }
}
