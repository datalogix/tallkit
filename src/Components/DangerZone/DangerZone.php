<?php

namespace TALLKit\Components\DangerZone;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class DangerZone extends BladeComponent
{
    public function __construct(
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $message = null,
    ) {}
}
