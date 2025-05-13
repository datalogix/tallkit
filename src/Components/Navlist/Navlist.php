<?php

namespace TALLKit\Components\Navlist;

use TALLKit\View\BladeComponent;

class Navlist extends BladeComponent
{
    public function __construct(
        public ?string $variant = null
    ) {}

    public function render()
    {
        return <<<'BLADE'
        <nav {{ $attributes->classes('flex flex-col overflow-visible min-h-auto') }}>
            {{ $slot }}
        </nav>
        BLADE;
    }
}
