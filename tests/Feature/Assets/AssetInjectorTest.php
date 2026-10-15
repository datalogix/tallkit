<?php

namespace TALLKit\Tests\Feature\Assets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Assets\AssetManager;
use TALLKit\Tests\TestCase;

class AssetInjectorTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        Route::get('/plain', fn () => '<html><body><p>Hi</p></body></html>');
        Route::get('/component', fn () => Blade::render('<html><body><x-tallkit::badge>Hi</x-tallkit::badge></body></html>'));
        Route::get('/scripts', fn () => Blade::render('<html><head>@tallkitScripts</head><body></body></html>'));
        Route::get('/no-html-tag', fn () => '<body><x></x></body>');
        Route::get('/no-body', fn () => '<html><p>Hi</p></html>');
        Route::get('/inline-body', fn () => '<html><body><script>let a = "</body>";</script></body></html>');
        Route::get('/json', fn () => response()->json(['html' => '</body></html>']));
        Route::get('/redirect', fn () => redirect('/plain'));
        Route::get('/error', fn () => response('<html><body></body></html>', 500));
        Route::get('/view', fn () => view('tallkit-test::page'));
        Route::get('/livewire', fn () => Blade::render('<html><body>@livewire(\'tallkit-injector-probe\')</body></html>'));
    }

    public function test_does_not_inject_when_nothing_from_tallkit_rendered(): void
    {
        $this->get('/plain')->assertOk()->assertDontSee('tallkit/tallkit.js', false);
    }

    public function test_injects_before_body_when_a_component_rendered(): void
    {
        $content = $this->get('/component')->assertOk()->getContent();

        $this->assertSame(1, substr_count($content, '/tallkit/tallkit.js'));
        $this->assertStringEndsWith("></script>\n</body></html>", $content);
    }

    public function test_flags_are_reset_after_each_request(): void
    {
        $this->get('/component');

        $manager = app(AssetManager::class);
        $this->assertFalse($manager->hasRenderedComponents);
        $this->assertFalse($manager->hasRenderedScripts);

        $this->get('/plain')->assertDontSee('tallkit/tallkit.js', false);

        $this->get('/component')->assertSee('tallkit/tallkit.js', false);
    }

    public function test_does_not_inject_twice_when_the_directive_was_used(): void
    {
        $content = $this->get('/scripts')->getContent();

        $this->assertSame(1, substr_count($content, '/tallkit/tallkit.js'));
        $this->assertStringContainsString('<head><script', $content);
    }

    public function test_injects_always_when_config_is_true(): void
    {
        config(['tallkit.inject_assets' => true]);

        $this->get('/plain')->assertSee('tallkit/tallkit.js', false);
    }

    public function test_never_injects_when_config_is_false(): void
    {
        config(['tallkit.inject_assets' => false]);

        $this->get('/component')->assertDontSee('tallkit/tallkit.js', false);
    }

    public function test_injects_before_html_when_there_is_no_body(): void
    {
        config(['tallkit.inject_assets' => true]);

        $this->assertStringEndsWith("></script>\n</html>", $this->get('/no-body')->getContent());
    }

    public function test_skips_content_without_closing_html(): void
    {
        config(['tallkit.inject_assets' => true]);

        $this->assertSame('<body><x></x></body>', $this->get('/no-html-tag')->getContent());
    }

    public function test_injects_before_the_last_body(): void
    {
        config(['tallkit.inject_assets' => true]);

        $content = $this->get('/inline-body')->getContent();

        $this->assertStringContainsString('let a = "</body>";</script><script', $content);
        $this->assertStringEndsWith("></script>\n</body></html>", $content);
    }

    public function test_skips_non_html_responses(): void
    {
        config(['tallkit.inject_assets' => true]);

        $this->get('/json')->assertExactJson(['html' => '</body></html>']);
    }

    public function test_skips_redirects(): void
    {
        config(['tallkit.inject_assets' => true]);

        $this->get('/redirect')->assertRedirect('/plain')->assertDontSee('tallkit/tallkit.js', false);
    }

    public function test_skips_server_errors_only_in_debug(): void
    {
        config(['tallkit.inject_assets' => true, 'app.debug' => true]);
        $this->get('/error')->assertStatus(500)->assertDontSee('tallkit/tallkit.js', false);

        config(['app.debug' => false]);
        $this->get('/error')->assertStatus(500)->assertSee('tallkit/tallkit.js', false);
    }

    public function test_keeps_the_original_response_content(): void
    {
        $this->app['view']->addNamespace('tallkit-test', __DIR__.'/views');
        config(['tallkit.inject_assets' => true]);

        $response = $this->get('/view');

        $response->assertViewIs('tallkit-test::page');
        $response->assertSee('tallkit/tallkit.js', false);
    }

    public function test_injects_when_a_livewire_component_rendered(): void
    {
        Livewire::component('tallkit-injector-probe', InjectorProbe::class);

        $this->get('/livewire')->assertOk()->assertSee('Probe')->assertSee('tallkit/tallkit.js', false);
    }
}

class InjectorProbe extends Component
{
    public function render()
    {
        return '<div>Probe</div>';
    }
}
