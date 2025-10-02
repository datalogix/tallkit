<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Select extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:select :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
