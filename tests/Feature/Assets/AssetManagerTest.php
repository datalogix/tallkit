<?php

namespace TALLKit\Tests\Feature\Assets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use TALLKit\Assets\AssetManager;
use TALLKit\Facades\TALLKit;
use TALLKit\Http\Controllers\AssetController;
use TALLKit\Tests\TestCase;

class AssetManagerTest extends TestCase
{
    protected function versionHash(): string
    {
        return json_decode(file_get_contents(__DIR__.'/../../../dist/manifest.json'), true)['/tallkit.js'];
    }

    public function test_is_bound_as_instance(): void
    {
        $this->assertInstanceOf(AssetManager::class, app(AssetManager::class));
        $this->assertSame(app(AssetManager::class), app(AssetManager::class));
    }

    public function test_registers_the_script_route(): void
    {
        $route = Route::getRoutes()->getByName('tallkit.script');

        $this->assertSame('tallkit/tallkit.js', $route->uri());
        $this->assertSame(AssetController::class.'@script', $route->getActionName());
    }

    public function test_scripts_renders_the_script_tag(): void
    {
        config(['tallkit.tooltip' => []]);

        $this->assertSame(
            '<script src="'.url('/tallkit/tallkit.js').'?id='.$this->versionHash().'" data-navigate-once></script>',
            AssetManager::scripts(),
        );
        $this->assertTrue(app(AssetManager::class)->hasRenderedScripts);
    }

    public function test_scripts_version_hash_is_cached(): void
    {
        $first = AssetManager::scripts();

        $this->assertSame($first, AssetManager::scripts());
        $this->assertStringContainsString('id='.$this->versionHash(), $first);
    }

    public function test_scripts_without_alpine(): void
    {
        config(['tallkit.load_alpine' => false]);

        $this->assertStringContainsString(' data-load-alpine="false"', AssetManager::scripts());
    }

    public function test_scripts_with_tooltip_config_drops_empty_values(): void
    {
        config(['tallkit.tooltip' => ['delay' => 100, 'position' => null, 'align' => '', 'arrow' => false]]);

        $this->assertStringContainsString(
            " data-tooltip='".e(json_encode(['delay' => 100, 'arrow' => false]))."'",
            AssetManager::scripts(),
        );
    }

    public function test_scripts_with_nonce_option(): void
    {
        $this->assertStringEndsWith(' nonce="a&quot;b"></script>', AssetManager::scripts(['nonce' => 'a"b']));
    }

    public function test_scripts_uses_the_vite_nonce(): void
    {
        Vite::useCspNonce('vite-nonce');

        $this->assertStringContainsString(' nonce="vite-nonce"', TALLKit::scripts());
    }

    public function test_directive_renders_scripts(): void
    {
        $this->assertStringContainsString('app(\'tallkit\')->scripts(["nonce" => "x"])', Blade::compileString('@tallkitScripts(["nonce" => "x"])'));
        $this->assertStringContainsString(' nonce="abc"', Blade::render('@tallkitScripts([\'nonce\' => \'abc\'])'));
    }

    public function test_listens_for_components_once_per_request(): void
    {
        $manager = app(AssetManager::class);
        $event = 'composing: '.hash('xxh128', 'tallkit').'::*';

        $this->assertTrue(app('events')->hasListeners($event));
        $this->assertFalse($manager->hasRenderedComponents);

        Blade::render('<x-tallkit::badge>Hi</x-tallkit::badge>');

        $this->assertTrue($manager->hasRenderedComponents);
        $this->assertFalse(app('events')->hasListeners($event));

        $manager->hasRenderedComponents = false;
        $manager->listenForComponents();
        $manager->listenForComponents();

        $this->assertTrue(app('events')->hasListeners($event));

        Blade::render('<x-tallkit::badge>Hi</x-tallkit::badge>');

        $this->assertTrue($manager->hasRenderedComponents);
        $this->assertFalse(app('events')->hasListeners($event));
    }
}
