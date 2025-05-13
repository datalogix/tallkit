<?php

namespace TALLKit\Components\Input;

use TALLKit\View\BladeComponent;

class Input extends BladeComponent
{
    protected function props()
    {
        return [
            'name' => null,
            'type' => 'text',
            'id' => null,
            'invalid' => null,
            'label' => null,
            'description' => null,
            'help' => null,
            'size' => null,
            'rounded' => true,
            'iconTrailing' => null,
            'iconLeading' => null,
            'loading' => true,
            'clearable' => null,
            'kbd' => null,
            'expandable' => null,
            'copyable' => null,
            'viewable' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->label ??= $this->name;
        $this->id ??= uniqid($this->name);
    }
}
