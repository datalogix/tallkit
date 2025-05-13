<?php

namespace TALLKit\Components\Sidebar;

use TALLKit\View\BladeComponent;

class Sidebar extends BladeComponent
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
