<?php

namespace TALLKit\Components\Separator;

use TALLKit\View\BladeComponent;

class Separator extends BladeComponent
{
    protected function props()
    {
        return [
            'orientation' => null,
            'vertical' => null,
            'variant' => null,
            'text' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->orientation ??= $this->vertical ? 'vertical' : 'horizontal';
    }
}
