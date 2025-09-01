<?php

namespace TALLKit\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use TALLKit\Attributes\Mount;

trait InteractsWithJsonOptions
{
    #[Mount(40)]
    protected function mountJsonOptions(array $data)
    {
        $this->manageProp($data, 'options');
    }

    protected function getDefaultOptions(...$args)
    {
        return [];
    }

    protected function getOptions(...$args)
    {
        return $this->options;
    }

    public function jsonOptions(...$args)
    {
        return Js::from(array_replace_recursive(
            Arr::wrap($this->getDefaultOptions(...$args)),
            Arr::wrap($this->getOptions(...$args)),
        ));
    }
}
