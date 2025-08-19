<?php

namespace TALLKit\Components\Form;

use TALLKit\Attributes\Finish;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\BoundValues;
use TALLKit\Facades\TALLKit;
use TALLKit\View\BladeComponent;

class Form extends BladeComponent
{
    use BoundValues;

    public function __construct(
        public ?string $method = 'post',
        public ?string $enctype = null,
        public mixed $bind = null,
        public ?string $route = null,
        public ?string $action = null,
    ) {
        TALLKit::bind($bind);
    }

    #[Mount()]
    protected function mount()
    {
        $this->method = strtoupper($this->method);
        $this->spoofMethod = in_array($this->method, ['PUT', 'PATCH', 'DELETE']);
        $this->setVariables('spoofMethod');
        $this->action = in_livewire()
            ? ($this->action ?? 'submit')
            : route_detect([$this->route, $this->action], $this->bind ?? $this->getBoundTarget(), request()->url());
    }

    #[Finish()]
    protected function finish()
    {
        TALLKit::endBind();
    }
}
