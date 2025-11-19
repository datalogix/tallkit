<?php

namespace TALLKit\Components\DangerZone;

use TALLKit\View\BladeComponent;

class DangerZone extends BladeComponent
{
    public function __construct(
        public ?string $title = null,
        public ?string $message = null,
    ) {}
}
