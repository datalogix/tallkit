<?php

namespace TALLKit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use ReflectionClass;
use TALLKit\Assets\AssetsManager;
use TALLKit\Binders\FormDataBinder;
use TALLKit\View\BladeComponent;
use TALLKit\View\ClassBuilder;
use TALLKit\View\TagCompiler;

class TALLKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FormDataBinder::class);
        $this->app->singleton(TALLKit::class);
        $this->app->alias(TALLKit::class, 'tallkit');

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('TALLKit', \TALLKit\Facades\TALLKit::class);
    }

    public function boot()
    {
        ComponentAttributeBag::macro('pluck', function ($key) {
            $result = $this->get($key);

            unset($this->attributes[$key]);

            return $result;
        });

        ComponentAttributeBag::macro('classes', function (...$classes) {
            // todo: apply twClass (merge tailwind class)
            return $this->class(new ClassBuilder($classes));
        });

        $components = [];

        foreach (File::allFiles(__DIR__.'/Components') as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $componentClass = str($file->getRelativePathname())
                ->replace('/', '\\')
                ->prepend('TALLKit\\Components\\')
                ->replaceLast('.php', '')
                ->toString();

            if (! class_exists($componentClass)) {
                continue;
            }

            $reflection = new ReflectionClass($componentClass);
            if (! $reflection->isSubclassOf(BladeComponent::class) && ! $reflection->isSubclassOf(Component::class)) {
                continue;
            }

            $componentName = str($file->getRelativePathname())
                ->replaceLast('.php', '')
                ->explode('/')
                ->map(fn ($part) => str($part)->kebab())
                ->unique()
                ->join('.');

            $components[$componentName] = $componentClass;
            $aliases = $componentClass::$aliases ?? [];

            foreach ($aliases as $alias) {
                $components[$alias] = $componentClass;
            }
        }

        $this->loadViewComponentsAs('tallkit', $components);
        $this->bootTagCompiler();

        AssetsManager::boot();

        Blade::directive('bind', fn ($e) => '<?php app(\'tallkit\')->bind('.$e.'); ?>');
        Blade::directive('endbind', fn ($e) => '<?php app(\'tallkit\')->endBind(); ?>');

        Blade::directive('scope', function ($expression) {
            // Split the expression by `top-level` commas (not in parentheses)
            $directiveArguments = preg_split("/,(?![^\(\(]*[\)\)])/", $expression);
            $directiveArguments = array_pad(array_map('trim', $directiveArguments), 2, '()');

            // Prepare arguments to uses
            if (! Str::startsWith($directiveArguments[1], '(')) {
                $ar = $directiveArguments;
                $directiveArguments = [];
                $directiveArguments[] = $ar[0];
                unset($ar[0]);
                $directiveArguments[] = '('.implode(', ', $ar).')';
            }

            // Ensure that the directive's arguments array has 3 elements - otherwise fill with `null`
            $directiveArguments = array_pad($directiveArguments, 3, null);

            // Extract values from the directive's arguments array
            [$name, $functionArguments, $functionUses] = $directiveArguments;

            // Connect the arguments to form a correct function declaration
            if ($functionArguments) {
                $functionArguments = "function {$functionArguments}";
            }

            $functionUses = array_filter(explode(',', trim($functionUses, '()')), 'strlen');

            // Add `$__env` and `$__bladeCompiler` to allow usage of other Blade directives inside the scoped slot
            array_push($functionUses, '$__env');
            array_push($functionUses, '$__bladeCompiler');

            $functionUses = implode(',', $functionUses);

            return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$__env->slot({$name}, {$functionArguments} use ({$functionUses}) { ?>";
        });

        Blade::directive('endscope', function () {
            return '<?php }); ?>';
        });
    }

    public function bootTagCompiler()
    {
        $compiler = new TagCompiler(
            app('blade.compiler')->getClassComponentAliases(),
            app('blade.compiler')->getClassComponentNamespaces(),
            app('blade.compiler')
        );

        app()->bind('tallkit.compiler', fn () => $compiler);

        app('blade.compiler')->precompiler(function ($in) use ($compiler) {
            return $compiler->compile($in);
        });
    }
}
