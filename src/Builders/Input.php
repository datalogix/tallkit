<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Input extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:input :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
