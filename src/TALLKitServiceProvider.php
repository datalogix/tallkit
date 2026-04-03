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

class TALLKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->alias(TALLKit::class, 'tallkit');
        $this->app->singleton(TALLKit::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('TALLKit', Facades\TALLKit::class);
    }

    public function boot()
    {
        if (class_exists(Livewire::class)) {
            Component::mixin(new ComponentMixin);
        }

        BladeDirectives::register();

        $this->bootComponentPath();
        $this->bootTagCompiler();
        $this->bootMacros();

        AssetManager::boot();
    }

    public function bootComponentPath()
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

    public function bootMacros()
    {
        ComponentAttributeBag::mixin(new ComponentAttributeBagMixin);
    }
}
