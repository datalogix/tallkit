<?php

namespace TALLKit\Components\Command;

use TALLKit\View\BladeComponent;

class Command extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
        public ?string $size = null,
        public ?bool $searchable = null,
        public ?bool $noRecords = null,
    ) {}
}
