<?php

namespace TALLKit\Components\Modal;

use TALLKit\View\BladeComponent;

class Confirm extends BladeComponent
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $size = null,
        public ?string $confirm = null,
        public ?string $cancel = null,
        public ?string $variant = null,
        public ?bool $autoClose = null,
    ) {}
}
