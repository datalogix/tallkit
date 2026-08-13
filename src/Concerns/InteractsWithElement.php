<?php

namespace TALLKit\Concerns;

trait InteractsWithElement
{
    public function elementProps()
    {
        return [
            'label' => null,
            'icon' => null,
            'prefix' => null,
            'suffix' => null,
            'iconTrailing' => null,
            'info' => null,
            'badge' => null,
            'prepend' => null,
            'append' => null,
            'kbd' => null,
        ];
    }
}
