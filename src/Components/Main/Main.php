<?php

namespace TALLKit\Components\Main;

use TALLKit\View\BladeComponent;

class Main extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
        @props([
            'container' => null,
        ])

        @php
        $classes = $classes('[grid-area:main]')
            ->add('p-6 lg:p-8')
            ->add('[[data-flux-container]_&]:px-0')
            ->add($container ? 'mx-auto w-full [:where(&)]:max-w-7xl' : '')
            ;
        @endphp

        <div {{ $attributes->class($classes) }} data-flux-main>
            {{ $slot }}
        </div>
        BLADE;
    }
}
