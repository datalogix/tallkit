<?php

namespace TALLKit\Concerns;

trait Componentable
{
    /**
     * The component key.
     *
     * @var string
     */
    protected $componentKey;

    /**
     * Get component key.
     *
     * @return string
     */
    protected function getComponentKey()
    {
        return $this->componentKey ??= str(class_basename($this))->kebab()->toString();
    }

    /**
     * Get component view.
     *
     * @return string
     */
    protected function getComponentView()
    {
        return str(static::class)
            ->replace('\\', '/')
            ->after('/')
            ->beforeLast('/')
            ->prepend(__DIR__.'/../')
            ->append('/')
            ->append($this->getComponentKey())
            ->append('.blade.php')
            ->toString();
    }

    /**
     * Get blade view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function blade()
    {
        return view()->file($this->getComponentView());
    }
}
