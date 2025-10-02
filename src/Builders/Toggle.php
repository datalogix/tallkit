<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Toggle extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:toggle :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
