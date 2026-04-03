<?php

namespace TALLKit\Components\Field\Group;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Suffix extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $size = null,
    ) {}
}
