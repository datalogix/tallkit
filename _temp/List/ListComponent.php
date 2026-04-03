<?php

namespace TALLKit\Components\List;

use TALLKit\View\BladeComponent;

class ListComponent extends BladeComponent
{
    protected $componentKey = 'list';

    public function __construct(
        public ?string $mode = null,
        public mixed $items = null,
    ) {}
}
