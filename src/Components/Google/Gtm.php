<?php

namespace TALLKit\Components\Google;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Gtm extends BladeComponent
{
    public function __construct(
        public null|string|bool $id = true,
        public bool $noscript = false,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->id = $this->id === true ? config('services.google.gtm') : $this->id;
    }
}
