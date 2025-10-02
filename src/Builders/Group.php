<?php

namespace TALLKit\Builders;

use Illuminate\Support\Facades\Blade;

class Group extends Element
{
    protected $elements = [];

    public function add(Element $element)
    {
        $this->elements[] = $element;

        return $this;
    }

    public function render()
    {
        return Blade::render(<<<'BLADE'
            <div {{ $attributes->class('grid gap-6')->style('grid-template-columns: repeat('.count($elements).', minmax(0, 1fr));') }}>
                @foreach ($elements as $element)
                    {{ $element }}
                @endforeach
            </div>
        BLADE, [
            'attributes' => $this->getComponentAttributeBag(),
            'elements' => $this->elements,
        ]);
    }
}
