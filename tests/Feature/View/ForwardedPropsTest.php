<?php

namespace TALLKit\Tests\Feature\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use TALLKit\Tests\TestCase;

class ForwardedPropsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        View::share('errors', new ViewErrorBag);
    }

    protected function iconPath(string $name): string
    {
        preg_match('/ d="([^"]+)"/', Blade::render("<tk:icon name=\"{$name}\" />"), $match);

        return $match[1];
    }

    public function test_a_forwarded_prop_wins_over_the_value_written_on_the_tag(): void
    {
        $html = Blade::render('<tk:page.auth.two-factor subtitle="Custom subtitle" email-login-url="/m" />');
        $login = Blade::render('<tk:page.auth.login identifier:name="registration" identifier:label="Registration" email-login-url="/m" email-login:variant="danger" />');

        $this->assertStringContainsString('Custom subtitle', $html);
        $this->assertStringNotContainsString('Enter your authentication code', $html);
        $this->assertStringContainsString('name="registration"', $login);
        $this->assertStringContainsString('Registration', $login);
        $this->assertStringContainsString('tk-color-red', $login);
    }

    public function test_a_slot_wins_over_a_forwarded_prop(): void
    {
        $html = Blade::render('<tk:stepper current="2" icon-completed="check" :items="[[\'label\' => \'A\', \'icon\' => \'star\'], [\'label\' => \'B\']]" />');

        $this->assertStringContainsString($this->iconPath('check'), $html);
        $this->assertStringNotContainsString($this->iconPath('star'), $html);
    }

    public function test_the_sidebar_close_button_shows_the_close_icon(): void
    {
        $html = Blade::render('<tk:layout.sidebar :menu="[]">Content</tk:layout.sidebar>');

        $this->assertStringContainsString($this->iconPath('close'), $html);
    }

    public function test_defaults_the_docs_describe_can_be_replaced(): void
    {
        $dangerZone = Blade::render('<tk:danger-zone type="info" trigger:variant="ghost" />');

        $this->assertStringContainsString('tk-color-blue', $dangerZone);
        $this->assertSame(1, substr_count($dangerZone, 'tk-color-red'));
        $this->assertStringNotContainsString('data-tallkit-context', Blade::render('<tk:context open="hover"><button>Area</button><tk:menu><tk:menu.item label="Copy" /></tk:menu></tk:context>'));
        $this->assertStringContainsString($this->iconPath('star'), Blade::render('<tk:copyable content="x" icon="star" />'));
        $this->assertStringContainsString($this->iconPath('star'), Blade::render('<tk:combobox name="c"><tk:combobox.option value="1" icon="star">One</tk:combobox.option></tk:combobox>'));
    }

    public function test_values_the_components_force_are_kept(): void
    {
        $this->assertStringNotContainsString('wire:model', Blade::render('<tk:money name="price" />'));
        $this->assertStringNotContainsString('type="button"', Blade::render('<tk:submit type="button" />'));
        $this->assertStringContainsString('type="password"', Blade::render('<tk:password type="text" />'));
    }

    public function test_labels_the_components_render_themselves_are_not_repeated_by_the_wrapper(): void
    {
        $checkbox = Blade::render('<tk:checkbox name="c" label="Prop label">Slot label</tk:checkbox>');

        $this->assertSame(1, substr_count(Blade::render('<tk:toggle name="t" label="Toggle label" />'), 'Toggle label'));
        $this->assertStringContainsString('Slot label', $checkbox);
        $this->assertStringNotContainsString('Prop label', $checkbox);
        $this->assertStringNotContainsString('Search label', Blade::render('<tk:listbox.search label="Search label" />'));
        $this->assertStringNotContainsString('Prefix text', Blade::render('<tk:slider name="s" prefix="Prefix text" />'));
    }

    public function test_the_breadcrumb_keeps_the_current_page_of_a_middle_item(): void
    {
        $html = Blade::render('<tk:breadcrumb :items="[[\'label\' => \'Home\', \'href\' => \'http://localhost\'], [\'label\' => \'Last\']]" />');

        $this->assertSame(2, substr_count($html, 'aria-current="page"'));
    }

    public function test_an_info_icon_is_focusable_only_outside_a_link_or_button(): void
    {
        $this->assertStringContainsString('tabindex="0"', Blade::render('<tk:element info="Help">Text</tk:element>'));
        $this->assertStringNotContainsString('tabindex="0"', Blade::render('<tk:button info="Help">Save</tk:button>'));
        $this->assertStringNotContainsString('tabindex="0"', Blade::render('<tk:link href="/a" info="Help">Go</tk:link>'));
    }

    public function test_a_loading_variant_reaches_the_indicator(): void
    {
        $dots = Blade::render('<tk:loading variant="dots" />');
        $this->assertSame(1, preg_match('/style="([^"]{200})/', $dots, $match), 'The dots indicator has no inline style to compare.');
        $this->assertStringContainsString($match[1], Blade::render('<tk:button loading="dots" wire:click="save">Save</tk:button>'));
    }

    public function test_a_slide_position_wins_over_the_carousel_slide_position(): void
    {
        $html = Blade::render('<tk:carousel slide:position="top" :slides="[[\'src\' => \'/a.jpg\', \'content\' => \'A\', \'position\' => \'bottom\'], [\'src\' => \'/b.jpg\', \'content\' => \'B\']]" />');

        preg_match_all('/justify-(end|start)/', $html, $matches);

        $this->assertSame(['end', 'start'], $matches[1]);
    }

    public function test_the_close_button_content_takes_the_place_of_the_icon(): void
    {
        foreach (['alert', 'badge'] as $component) {
            $html = Blade::render("<tk:{$component}.close icon=\"star\">Dismiss</tk:{$component}.close>");

            $this->assertStringContainsString('Dismiss', $html);
            $this->assertStringNotContainsString($this->iconPath('star'), $html);
        }
    }
}
