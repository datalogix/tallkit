<?php

namespace TALLKit\Components\Heading;

use TALLKit\View\BladeComponent;

class Heading extends BladeComponent
{
    protected function props()
    {
        return [
            'size' => null,
            'level' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->level = (int) $this->level;
    }
}
