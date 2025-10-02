<?php

namespace TALLKit\Builders;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Fluent;
use Illuminate\View\ComponentAttributeBag;
use Stringable;

abstract class Element extends Fluent implements Htmlable, Stringable
{
    abstract public function render();

    protected function getComponentAttributeBag()
    {
        return new ComponentAttributeBag($this->attributes);
    }

    public function toHtml()
    {
        return $this->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}
