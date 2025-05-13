<?php

namespace TALLKit\Components\Form;

use TALLKit\Concerns\BoundValues;
use TALLKit\Concerns\PrepareFormDataBinder;
use TALLKit\View\BladeComponent;

class Form extends BladeComponent
{
    use BoundValues, PrepareFormDataBinder;

    protected function props()
    {
        return [
            'method' => 'post',
            'enctype' => null,
            'bind' => null,
            'route' => null,
            'action' => null,
        ];
    }

    protected function mounted(array $data)
    {
        $this->method = strtoupper($this->method);
        $this->spoofMethod = in_array($this->method, ['PUT', 'PATCH', 'DELETE']);
        $this->setVariables('spoofMethod');
        $this->action ??= route_detect($this->route, $this->bind ?? $this->getBoundTarget(), request()->url());
        $this->startFormDataBinder($this->bind);
    }

    protected function finish()
    {
        $this->endFormDataBinder();
    }
}
