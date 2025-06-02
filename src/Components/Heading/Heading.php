<?php

namespace TALLKit\Components\Heading;

use TALLKit\View\BladeComponent;

class Heading extends BladeComponent
{
    public function __construct(
        public ?string $text = null,
        public ?string $size = null,
        public ?bool $accent = null,
        public ?int $level = null
    ) {}
}
