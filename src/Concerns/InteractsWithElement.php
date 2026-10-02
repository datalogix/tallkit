<?php

namespace TALLKit\Concerns;

trait InteractsWithElement
{
    public function elementProps(): array
    {
        return [
            'ariaLabel' => null,
            'iconDot' => null,
            'label' => null,
            'icon' => null,
            'prefix' => null,
            'suffix' => null,
            'iconTrailing' => null,
            'badge' => null,
            'prepend' => null,
            'append' => null,
            'kbd' => null,
        ];
    }
}
