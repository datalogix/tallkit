<?php

namespace TALLKit\View;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use ReflectionClass;

class BladeComponentRegistrar
{
    public static function discover(
        string $basePath = __DIR__.'/../Components',
        string $baseNamespace = 'TALLKit\\Components'
    ) {
        $components = [];

        foreach (File::allFiles($basePath) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $componentClass = Str::of($file->getRelativePathname())
                ->replace('/', '\\')
                ->prepend($baseNamespace.'\\')
                ->replaceLast('.php', '')
                ->toString();

            if (! class_exists($componentClass)) {
                continue;
            }

            $reflection = new ReflectionClass($componentClass);
            if (! $reflection->isSubclassOf(BladeComponent::class) && ! $reflection->isSubclassOf(Component::class)) {
                continue;
            }

            $componentName = Str::of($file->getRelativePathname())
                ->replaceLast('.php', '')
                ->replaceEnd('Index', '')
                ->explode('/')
                ->map(fn ($part) => Str::kebab($part))
                ->unique()
                ->join('.');

            $components[$componentName] = $componentClass;
        }

        return $components;
    }
}
