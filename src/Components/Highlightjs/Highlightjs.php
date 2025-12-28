<?php

namespace TALLKit\Components\Highlightjs;

use TALLKit\View\BladeComponent;

class Highlightjs extends BladeComponent
{
    public function __construct(
        public ?string $code = null,
        public ?string $language = null,
    ) {}
}
