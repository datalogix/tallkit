<?php

namespace TALLKit;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Component;
use Livewire\Livewire;
use TALLKit\Assets\AssetManager;
use TALLKit\Livewire\ComponentMixin;
use TALLKit\View\BladeDirectives;
use TALLKit\View\Compilers\ComponentTagCompiler;
use TALLKit\View\ComponentAttributeBagMixin;

use function Livewire\on;

class TALLKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->alias(TALLKit::class, 'tallkit');
        $this->app->singleton(TALLKit::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('TALLKit', Facades\TALLKit::class);

        $this->mergeConfigFrom(__DIR__.'/../config/tallkit.php', 'tallkit');
    }

    public function boot()
    {
        if (class_exists(Livewire::class)) {
            Component::mixin(new ComponentMixin);

            $this->bootMagicActions();
        }

        BladeDirectives::register();

        $this->bootComponentPath();
        $this->bootTagCompiler();
        $this->bootMacros();

        AssetManager::boot();

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

        $compiler = new ComponentTagCompiler(
            $bladeCompiler->getClassComponentAliases(),
            $bladeCompiler->getClassComponentNamespaces(),
            $bladeCompiler
        );

        app()->bind('tallkit.compiler', fn () => $compiler);
        $bladeCompiler->precompiler(fn ($value) => $compiler->compile($value));
    }

    protected function bootMacros()
    {
        ComponentAttributeBag::mixin(new ComponentAttributeBagMixin);
    }

    protected function bootMagicActions()
    {
        // Methods added via `Component::mixin()` aren't visible to Livewire's
        // "public method" check when called directly from the browser (eg.
        // `wire:click="markNotificationAsRead(...)"`), so they need to be
        // dispatched manually through the `call` hook instead.
        on('call', function ($component, $method, $params, $componentContext, $returnEarly) {
            if (! in_array($method, ['markNotificationAsRead', 'markAllNotificationsAsRead', 'deleteNotification'])) {
                return;
            }

            $returnEarly($component->{$method}(...$params));
        });
    }
}
