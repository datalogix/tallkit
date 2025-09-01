<?php

namespace TALLKit\Components\Element;

use TALLKit\View\BladeComponent;

class Wrapper extends BladeComponent
{
    public function __construct(
        public null|bool|string $label = null,
    ) {}

    public function baseComponentKey()
    {
        return $this->attributes->get('name');
    }
}
