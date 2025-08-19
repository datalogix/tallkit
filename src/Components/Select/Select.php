<?php

namespace TALLKit\Components\Select;

use Illuminate\Support\Arr;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\Concerns\InteractsWithOptions;
use TALLKit\View\BladeComponent;

class Select extends BladeComponent
{
    use InteractsWithField;
    use InteractsWithOptions;

    public function __construct(
        public ?bool $multiple = null,
        public ?int $rows = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->value = Arr::wrap($this->value);
    }
}
