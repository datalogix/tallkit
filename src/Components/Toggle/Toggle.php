<?php

namespace TALLKit\Components\Toggle;

use TALLKit\Components\Checkbox\Checkbox;

class Toggle extends Checkbox
{
    public function __construct(
        public mixed $checked = null,
        public ?string $variant = null,
        public ?string $iconOn = null,
        public ?string $iconOff = null,
    ) {
        parent::__construct($checked, $variant);
    }
}
