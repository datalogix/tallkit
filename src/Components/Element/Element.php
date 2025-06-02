<?php

namespace TALLKit\Components\Element;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Element extends BladeComponent
{
    public function __construct(
        public ?string $href = null,
        public ?string $as = null,
        public ?string $type = null,
        public ?string $tooltip = null,
        public ?bool $inline = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->as ??= $this->inline ? 'span' : 'div';

        if ($this->href) {
            $this->as = 'a';
        } else if ($this->type) {
            $this->as = 'button';
        }
    }
}
