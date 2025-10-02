<?php

namespace TALLKit\Components\Form;

use TALLKit\View\BladeComponent;

class Card extends BladeComponent
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?bool $separator = null,
    ) {}
}
