<?php

namespace TALLKit\Components\Navlist;

use TALLKit\View\BladeComponent;

class Badge extends BladeComponent
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
