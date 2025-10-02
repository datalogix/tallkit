<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Radio extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:radio :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
