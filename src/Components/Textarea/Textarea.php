<?php

namespace TALLKit\Components\Textarea;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Textarea extends BladeComponent
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
            'resize' => 'vertical',
            'rows' => 'auto',
            'invalid' => null,
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
