<?php

namespace TALLKit\Components\Select;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Native extends BladeComponent
{
    protected function props()
    {
        return [
            'name' => null,
            'label' => null,
            'id' => null,
            'description' => null,
            'help' => null,
            'size' => null,
            'placeholder' => '---',
            'invalid' => null,
            'options' => null,
        ];
    }

    #[Mount()]
    protected function mount()
    {
        if ($this->name) {
            $this->label ??= $this->name;
            $this->id ??= uniqid($this->name);
        }
    }
}
