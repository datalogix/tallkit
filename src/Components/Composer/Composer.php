<?php

namespace TALLKit\Components\Composer;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Composer extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?int $rows = null,
        public ?int $maxRows = null,
        public ?bool $inline = null,
        public ?string $submit = null,
    ) {}
}
