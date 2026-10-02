<?php

namespace TALLKit;

use Composer\InstalledVersions;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Livewire;
use TALLKit\Assets\AssetManager;
use TALLKit\Http\Controllers\UploadController;
use TALLKit\Livewire\ComponentMixins;
use TALLKit\Livewire\DateRangeSynth;
use TALLKit\Livewire\NotificationActions;
use TALLKit\Livewire\ToastRedirects;
use TALLKit\View\BladeDirectives;
use TALLKit\View\Compilers\ComponentTagCompiler;
use TALLKit\View\Compilers\PropsCompiler;
use TALLKit\View\ComponentAttributeBagMixin;

class TALLKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->alias(TALLKit::class, 'tallkit');
        $this->app->singleton(TALLKit::class);

        $this->app->scoped('tallkit.ids', fn () => new \ArrayObject);
        $this->app->scoped('tallkit.field-bags', fn () => new \ArrayObject);
        $this->app->scoped('tallkit.uploads', fn () => new \ArrayObject);
        $this->app->scoped('tallkit.icons', fn () => new \ArrayObject);

        AliasLoader::getInstance()->alias('TALLKit', Facades\TALLKit::class);

        $this->mergeConfigFrom(__DIR__.'/../config/tallkit.php', 'tallkit');
    }

    public function boot()
    {
        if ($this->app->make(TALLKit::class)->livewireInstalled()) {
            ComponentMixins::register();

            Livewire::propertySynthesizer(DateRangeSynth::class);

            NotificationActions::register();
            ToastRedirects::register();
        }

        BladeDirectives::register();

        $this->loadJsonTranslationsFrom(__DIR__.'/../lang');

        $this->bootComponentPath();
        $this->bootTagCompiler();
        $this->bootCompiledViews();
        $this->bootMacros();
        $this->bootRoutes();
        $this->bootLivewireUploads();

        AssetManager::boot();

        if ($this->app->runningInConsole()) {
            $this->commands([Console\IconsCommand::class, Console\PruneUploadsCommand::class]);

            $this->optimizes(optimize: 'tallkit:icons', key: 'tallkit-icons');
        }

        $this->publishes([
            __DIR__.'/../config/tallkit.php' => config_path('tallkit.php'),
        ], 'tallkit-config');
    }

    protected function bootComponentPath()
    {
        if (file_exists(resource_path('views/tallkit'))) {
            Blade::anonymousComponentPath(resource_path('views/tallkit'), 'tallkit');
        }

        Blade::anonymousComponentPath(__DIR__.'/../resources/views', 'tallkit');
    }

    protected function bootTagCompiler()
    {
        $bladeCompiler = app('blade.compiler');

        app()->bind('tallkit.compiler', fn () => new ComponentTagCompiler(
            $bladeCompiler->getClassComponentAliases(),
            $bladeCompiler->getClassComponentNamespaces(),
            $bladeCompiler,
        ));

        $bladeCompiler->precompiler(fn ($value) => app('tallkit.compiler')->compile($value));

        $packageViews = array_filter([realpath(__DIR__.'/../resources/views'), realpath(resource_path('views/tallkit'))]);

        $bladeCompiler->precompiler(function ($value) use ($bladeCompiler, $packageViews) {
            $path = realpath((string) $bladeCompiler->getPath());

            foreach ($path ? $packageViews : [] as $directory) {
                if (str_starts_with($path, $directory.DIRECTORY_SEPARATOR)) {
                    return PropsCompiler::compile($value);
                }
            }

            return $value;
        });
    }

    protected function bootCompiledViews()
    {
        $path = config('view.compiled');

        if (! is_string($path) || ! is_dir($path) || ! InstalledVersions::isInstalled('datalogix/tallkit')) {
            return;
        }

        $version = InstalledVersions::getPrettyVersion('datalogix/tallkit').'@'.InstalledVersions::getReference('datalogix/tallkit');
        $stamp = storage_path('framework/tallkit-version');

        if (@file_get_contents($stamp) === $version || @file_put_contents($stamp, $version) === false) {
            return;
        }

        $files = new Filesystem;

        foreach ($files->glob($path.'/*') as $item) {
            $files->isDirectory($item) ? $files->deleteDirectory($item) : $files->delete($item);
        }
    }

    protected function bootMacros()
    {
        ComponentAttributeBag::mixin(new ComponentAttributeBagMixin);

        $this->app->terminating(fn () => View\ClassCache::persist());
    }

    protected function bootLivewireUploads()
    {
        if (! config('tallkit.upload.livewire_max_size', true)
            || ! class_exists(Livewire::class)
            || config('livewire.temporary_file_upload.rules') !== null) {
            return;
        }

        $sizes = config('tallkit.upload.max_size', 20480);
        $largest = max(array_map('intval', (array) $sizes) ?: [20480]);

        config(['livewire.temporary_file_upload.rules' => ['required', 'file', 'max:'.max($largest, 12288)]]);
    }

    protected function bootRoutes()
    {
        if (! config('tallkit.upload.enabled', true)) {
            return;
        }

        RateLimiter::for('tallkit.uploads', fn ($request) => Limit::perMinute(60)->by(
            ($id = $request->user(config('tallkit.upload.guard'))?->getAuthIdentifier()) !== null ? 'user:'.$id : 'ip:'.$request->ip()
        ));

        Route::middleware(config('tallkit.upload.middleware', ['web', 'throttle:tallkit.uploads']))
            ->post(config('tallkit.upload.route', '/tallkit/upload'), [UploadController::class, 'store'])
            ->name('tallkit.upload');
    }
}
