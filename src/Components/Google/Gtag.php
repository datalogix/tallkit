<?php

namespace TALLKit\Components\Google;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Gtag extends BladeComponent
{
    public function __construct(
        public null|string|bool $id = true,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->id = $this->id === true ? config('services.google.gtag') : $this->id;
    }
}
