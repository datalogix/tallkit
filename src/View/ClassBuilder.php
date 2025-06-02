<?php

namespace TALLKit\View;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

class ClassBuilder implements Stringable
{
    use Conditionable;

    protected $classes;

    public function __construct(null|array|string $classes = null)
    {
        $this->classes = collect();

        if ($classes) {
            $this->add(...$classes);
        }
    }

    public function add(null|array|string $classes = null)
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
            ->map(fn ($class) => str($class)->trim()->toString())
            ->join(' ');
    }
}
