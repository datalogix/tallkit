<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Checkbox extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:checkbox :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
