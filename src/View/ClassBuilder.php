<?php

namespace TALLKit\View;

use Illuminate\Support\Traits\Conditionable;
use Stringable;

class ClassBuilder implements Stringable
{
    use Conditionable;

    protected array $classes = [];

    public function __construct(null|array|string $classes = null)
    {
        if ($classes) {
            $this->add($classes);
        }
    }

    public function add(...$classes)
    {
        array_push($this->classes, ...$this->flatten($classes));

        return $this;
    }

    protected function flatten(array $classes): array
    {
        $names = [];

        foreach ($classes as $key => $value) {
            if (is_string($key)) {
                if ($value) {
                    $names[] = $key;
                }
            } elseif (is_array($value)) {
                array_push($names, ...$this->flatten($value));
            } elseif ($value !== null && $value !== false) {
                $names[] = (string) $value;
            }
        }

        return $names;
    }

    public function __toString()
    {
        return implode(' ', array_unique(preg_split('/\s+/', implode(' ', $this->classes), -1, PREG_SPLIT_NO_EMPTY)));
    }
}
