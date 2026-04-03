<?php

namespace TALLKit\Components\Pagination;

use TALLKit\View\BladeComponent;

class Page extends BladeComponent
{
    public function __construct(
        public int $page
    ) {}
}
