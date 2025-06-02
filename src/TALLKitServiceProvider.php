<?php

namespace TALLKit;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;
use TALLKit\Assets\AssetManager;
use TALLKit\Binders\FormDataBinder;
use TALLKit\View\BladeComponentRegistrar;
use TALLKit\View\BladeDirectives;
use TALLKit\View\Compilers\ComponentTagCompiler;
use TALLKit\View\ComponentAttributeBagMixin;

class TALLKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FormDataBinder::class);
        $this->app->singleton(TALLKit::class);
        $this->app->alias(TALLKit::class, 'tallkit');

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('TALLKit', \TALLKit\Facades\TALLKit::class);
    }

    public function boot()
    {
        ComponentAttributeBag::mixin(new ComponentAttributeBagMixin);
        BladeDirectives::register();

        $this->loadViewComponentsAs('tallkit', BladeComponentRegistrar::discover());
        $this->registerTagCompiler();

        AssetManager::boot();
    }

    protected function registerTagCompiler()
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
}
