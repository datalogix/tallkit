<?php

namespace TALLKit\Components\Profile;

use TALLKit\View\BladeComponent;

class Profile extends BladeComponent
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
