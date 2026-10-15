<?php

namespace TALLKit\Tests\Feature;

use Composer\InstalledVersions;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Facades\TALLKit as TALLKitFacade;
use TALLKit\Livewire\DateRange;
use TALLKit\Livewire\DateRangeSynth;
use TALLKit\TALLKit;
use TALLKit\TALLKitServiceProvider;
use TALLKit\Tests\TestCase;
use TALLKit\View\Compilers\ComponentTagCompiler;

class ServiceProviderTest extends TestCase
{
    protected ?string $temp = null;

    protected function tearDown(): void
    {
        if ($this->temp !== null) {
            (new Filesystem)->deleteDirectory($this->temp);
        }

        parent::tearDown();
    }

    protected function temp(): string
    {
        $this->temp ??= sys_get_temp_dir().'/tallkit-provider-'.bin2hex(random_bytes(6));

        (new Filesystem)->ensureDirectoryExists($this->temp);

        return $this->temp;
    }

    protected function provider(): TALLKitServiceProvider
    {
        return $this->app->getProvider(TALLKitServiceProvider::class);
    }

    protected function callProvider(string $method): void
    {
        (fn () => $this->{$method}())->call($this->provider());
    }

    public function test_register_binds_services(): void
    {
        $this->assertSame($this->app->make(TALLKit::class), $this->app->make('tallkit'));
        $this->assertTrue(class_exists('TALLKit'));
        $this->assertSame(TALLKitFacade::getFacadeRoot(), \TALLKit::getFacadeRoot());

        foreach (['ids', 'field-bags', 'uploads', 'icons', 'avatar-lookups', 'avatars', 'found-images'] as $name) {
            $store = $this->app->make("tallkit.{$name}");

            $this->assertInstanceOf(\ArrayObject::class, $store);
            $this->assertSame($store, $this->app->make("tallkit.{$name}"));
        }

        $ids = $this->app->make('tallkit.ids');
        $this->app->forgetScopedInstances();
        $this->assertNotSame($ids, $this->app->make('tallkit.ids'));

        $this->assertSame('/tallkit/upload', config('tallkit.upload.route'));
    }

    public function test_boot_registers_livewire_integration(): void
    {
        $this->assertNotNull(app('livewire')->findSynth(DateRange::today(), null));
        $this->assertInstanceOf(DateRangeSynth::class, app('livewire')->findSynth('tkdr', null));
        $this->assertTrue(Component::hasMacro('toast'));
    }

    public function test_boot_registers_blade_directives_and_translations(): void
    {
        $this->assertArrayHasKey('scope', Blade::getCustomDirectives());
        $this->assertArrayHasKey('endscope', Blade::getCustomDirectives());

        $this->app->setLocale('pt_BR');
        $this->assertSame('Hoje', __('Today'));
    }

    public function test_boot_registers_the_package_component_path(): void
    {
        $paths = collect(Blade::getAnonymousComponentPaths())->where('prefix', 'tallkit')->pluck('path');

        $this->assertContains(realpath(__DIR__.'/../../resources/views'), $paths->map(fn ($path) => realpath($path))->all());
    }

    public function test_app_views_override_the_package_component_path(): void
    {
        $base = $this->temp();
        mkdir($base.'/resources/views/tallkit', 0777, true);
        file_put_contents($base.'/composer.json', '{"autoload":{"psr-4":{"App\\\\":"app/"}}}');
        file_put_contents($base.'/resources/views/tallkit/custom-widget.blade.php', "@props(['label' => null])\n<span>{{ \$label }}</span>");

        $original = $this->app->basePath();
        $this->app->setBasePath($base);

        try {
            $this->callProvider('bootComponentPath');
            $this->callProvider('bootTagCompiler');

            $this->assertSame('<span>Hi</span>', trim(Blade::render('<x-tallkit::custom-widget label="Hi" />')));

            $compiler = app('blade.compiler');
            $compiler->setPath($base.'/resources/views/tallkit/custom-widget.blade.php');
            $this->assertStringContainsString('$__tkProps', $compiler->compileString("@props(['a' => 1])"));
        } finally {
            $this->app->setBasePath($original);
        }
    }

    public function test_props_compiler_only_runs_on_package_views(): void
    {
        $compiler = app('blade.compiler');

        $this->assertInstanceOf(ComponentTagCompiler::class, app('tallkit.compiler'));

        $compiler->setPath(realpath(__DIR__.'/../../resources/views/kbd/kbd.blade.php'));
        $this->assertStringContainsString('$__tkProps', $compiler->compileString("@props(['a' => 1])"));

        $compiler->setPath(__FILE__);
        $this->assertStringNotContainsString('$__tkProps', $compiler->compileString("@props(['a' => 1])"));

        $compiler->setPath('/does/not/exist.blade.php');
        $this->assertStringNotContainsString('$__tkProps', $compiler->compileString("@props(['a' => 1])"));
    }

    public function test_tag_compiler_is_applied_to_blade(): void
    {
        $this->assertStringContainsString('<kbd', Blade::render('<tk:kbd>K</tk:kbd>'));
    }

    public function test_compiled_views_are_cleared_when_the_version_changes(): void
    {
        $base = $this->temp();
        $compiled = $base.'/views';
        mkdir($compiled.'/nested', 0777, true);
        mkdir($base.'/storage/framework', 0777, true);
        file_put_contents($compiled.'/a.php', 'a');
        file_put_contents($compiled.'/nested/b.php', 'b');

        $this->app->useStoragePath($base.'/storage');
        config(['view.compiled' => $compiled]);

        $this->callProvider('bootCompiledViews');

        $version = InstalledVersions::getPrettyVersion('datalogix/tallkit').'@'.InstalledVersions::getReference('datalogix/tallkit');

        $this->assertSame([], glob($compiled.'/*'));
        $this->assertSame($version, file_get_contents($base.'/storage/framework/tallkit-version'));

        file_put_contents($compiled.'/c.php', 'c');
        $this->callProvider('bootCompiledViews');

        $this->assertFileExists($compiled.'/c.php');
    }

    public function test_compiled_views_are_kept_when_the_stamp_cannot_be_written(): void
    {
        $base = $this->temp();
        mkdir($base.'/views');
        file_put_contents($base.'/views/a.php', 'a');

        $this->app->useStoragePath($base.'/missing-storage');
        config(['view.compiled' => $base.'/views']);

        $this->callProvider('bootCompiledViews');

        $this->assertFileExists($base.'/views/a.php');
    }

    public function test_compiled_views_require_a_valid_directory(): void
    {
        $base = $this->temp();
        mkdir($base.'/storage/framework', 0777, true);
        $this->app->useStoragePath($base.'/storage');

        foreach ([null, $base.'/missing'] as $path) {
            config(['view.compiled' => $path]);

            $this->callProvider('bootCompiledViews');

            $this->assertFileDoesNotExist($base.'/storage/framework/tallkit-version');
        }
    }

    public function test_boot_registers_macros(): void
    {
        $this->assertTrue(ComponentAttributeBag::hasMacro('pluck'));
        $this->assertTrue(ComponentAttributeBag::hasMacro('classes'));
    }

    public function test_upload_route_and_rate_limiter(): void
    {
        $route = Route::getRoutes()->getByName('tallkit.upload');

        $this->assertSame('tallkit/upload', $route->uri());
        $this->assertSame(['POST'], $route->methods());
        $this->assertSame(['web', 'throttle:tallkit.uploads'], $route->middleware());

        $limiter = RateLimiter::limiter('tallkit.uploads');

        $guest = Request::create('/tallkit/upload', 'POST', server: ['REMOTE_ADDR' => '10.0.0.1']);
        $limit = $limiter($guest);
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertSame('ip:10.0.0.1', $limit->key);
        $this->assertSame(60, $limit->maxAttempts);

        config(['tallkit.upload.guard' => 'admin']);
        $user = (new User)->forceFill(['id' => 7]);
        $guards = [];
        $authenticated = Request::create('/tallkit/upload', 'POST');
        $authenticated->setUserResolver(function ($guard) use ($user, &$guards) {
            $guards[] = $guard;

            return $user;
        });

        $this->assertSame('user:7', $limiter($authenticated)->key);
        $this->assertSame(['admin'], $guards);
    }

    public function test_upload_route_follows_config_and_can_be_disabled(): void
    {
        config(['tallkit.upload.route' => '/custom/upload', 'tallkit.upload.middleware' => ['api']]);
        $this->callProvider('bootRoutes');

        $route = collect(Route::getRoutes()->getRoutes())->first(fn ($route) => $route->uri() === 'custom/upload');
        $this->assertSame('tallkit.upload', $route->getName());
        $this->assertSame(['api'], $route->middleware());

        $count = count(Route::getRoutes()->getRoutes());
        config(['tallkit.upload.enabled' => false]);
        $this->callProvider('bootRoutes');

        $this->assertCount($count, Route::getRoutes()->getRoutes());
    }

    public function test_livewire_upload_rules(): void
    {
        $this->assertSame(['required', 'file', 'max:51200'], config('livewire.temporary_file_upload.rules'));

        $rules = function (array $config) {
            config(['livewire.temporary_file_upload.rules' => null, ...$config]);
            $this->callProvider('bootLivewireUploads');

            return config('livewire.temporary_file_upload.rules');
        };

        $this->assertSame(['required', 'file', 'max:30000'], $rules(['tallkit.upload.max_size' => 30000]));
        $this->assertSame(['required', 'file', 'max:12288'], $rules(['tallkit.upload.max_size' => ['image' => 100]]));
        $this->assertSame(['required', 'file', 'max:20480'], $rules(['tallkit.upload.max_size' => []]));
        $this->assertNull($rules(['tallkit.upload.livewire_max_size' => false]));

        config(['livewire.temporary_file_upload.rules' => ['file'], 'tallkit.upload.livewire_max_size' => true]);
        $this->callProvider('bootLivewireUploads');
        $this->assertSame(['file'], config('livewire.temporary_file_upload.rules'));
    }

    public function test_console_commands_and_publishing(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('tallkit:icons', $commands);
        $this->assertArrayHasKey('tallkit:prune-uploads', $commands);
        $this->assertSame('tallkit:icons', ServiceProvider::$optimizeCommands['tallkit-icons'] ?? null);

        $this->assertSame(
            [realpath(__DIR__.'/../../src').'/../config/tallkit.php' => config_path('tallkit.php')],
            ServiceProvider::pathsToPublish(TALLKitServiceProvider::class, 'tallkit-config'),
        );
    }

    public function test_asset_route_and_scripts(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('tallkit.script'));

        $script = TALLKitFacade::scripts();

        $this->assertMatchesRegularExpression('#^<script src="http://localhost/tallkit/tallkit\.js\?id=[^"]*" data-navigate-once data-tooltip=\'[^\']+\'></script>$#', $script);
        $this->assertStringContainsString(e(json_encode(array_filter(config('tallkit.tooltip'), fn ($value) => $value !== null && $value !== ''))), $script);

        config(['tallkit.load_alpine' => false, 'tallkit.tooltip' => []]);

        $this->assertMatchesRegularExpression(
            '#^<script src="[^"]+" data-navigate-once data-load-alpine="false" nonce="a&quot;b"></script>$#',
            TALLKitFacade::scripts(['nonce' => 'a"b']),
        );
    }

    public function test_livewire_is_installed(): void
    {
        $this->assertTrue(app(TALLKit::class)->livewireInstalled());
        $this->assertTrue(class_exists(Livewire::class));
    }
}
