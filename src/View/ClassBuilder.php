<?php

namespace TALLKit\View;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

class ClassBuilder implements Stringable
{
    use Conditionable;

    protected $classes;

    public function __construct($classes = null)
    {
        $this->classes = Collection::make();
        if ($classes) {
            $this->add(...$classes);
        }
    }

    public function add($classes = null)
    {
        $classes = is_array($classes) ? $classes : func_get_args();
        $names = explode(' ', Arr::toCssClasses($classes));

        $this->classes->push(...$names);

        return $this;
    }

    public function __toString()
    {
        return $this->classes->unique()
            ->filter()
            ->map(fn ($class) => str($class)->trim()->toString())->join(' ');
    }
}
