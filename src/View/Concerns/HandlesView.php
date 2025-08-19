<?php

namespace TALLKit\View\Concerns;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait HandlesView
{
    protected $componentKey;

    protected function getComponentKey()
    {
        return $this->componentKey ??= Str::kebab(class_basename($this));
    }

    protected function getComponentView()
    {
        return Str::of(static::class)
            ->replace('\\', '/')
            ->after('/')
            ->beforeLast('/')
            ->prepend(__DIR__.'/../../')
            ->append('/')
            ->append($this->getComponentKey())
            ->append('.blade.php')
            ->toString();
    }

    protected function blade()
    {
        return view()->file($this->getComponentView());
    }

    public function resolveView()
    {
        $view = $this->render();

        if ($view instanceof View) {
            return $view;
        }

        $resolver = function ($view) {
            if ($view instanceof View) {
                return new HtmlString($view->render());
            }

            return $this->extractBladeViewFromString($view);
        };

        if ($view instanceof Closure) {
            return fn (array $data = []) => $resolver($view($data));
        }

        return fn (array $data = []) => view($resolver($view))->with($this->run($data));
    }

    public function render()
    {
        return fn (array $data) => $this->blade()->with($this->run($data));
    }
}
