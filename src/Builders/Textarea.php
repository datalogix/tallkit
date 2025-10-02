<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Textarea extends Element
{
    public function render()
    {
        return Blade::render(<<<'BLADE'
            <tk:textarea :$attributes />
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
        ]);
    }
}
