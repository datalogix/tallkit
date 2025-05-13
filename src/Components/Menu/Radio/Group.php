<?php

namespace TALLKit\Components\Menu\Radio;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
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
