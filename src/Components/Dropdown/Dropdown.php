<?php

namespace TALLKit\Components\Dropdown;

use TALLKit\View\BladeComponent;

class Dropdown extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
        <div>
            {{ $slot }}
        </div>
        BLADE;
    }
}
