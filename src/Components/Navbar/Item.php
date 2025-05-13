<?php

namespace TALLKit\Components\Navbar;

use TALLKit\View\BladeComponent;

class Item extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
        <div {{ $attributes }}>
            {{ $slot }}
        </div>
        BLADE;
    }
}
