<?php

namespace TALLKit\Components\Menu;

use TALLKit\View\BladeComponent;

class Item extends BladeComponent
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
