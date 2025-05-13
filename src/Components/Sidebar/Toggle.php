<?php

namespace TALLKit\Components\Sidebar;

use TALLKit\View\BladeComponent;

class Toggle extends BladeComponent
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
