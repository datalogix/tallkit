<?php

namespace TALLKit\Components\Element;

use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Element extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public bool|string|ComponentSlot|null $label = null,
        public ?string $href = null,
        public bool|string|null $external = null,
        public ?string $route = null,
        public mixed $parameters = null,
        public ?bool $navigate = null,
        public ?string $action = null,
        public ?string $as = null,
        public ?string $type = null,
        public ?bool $exact = null,
        public ?bool $current = null,
        public bool|string|null $iconDot = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $prefix = null,
        public string|ComponentSlot|null $suffix = null,
        public string|ComponentSlot|null $iconTrailing = null,
        public string|ComponentSlot|null $info = null,
        public bool|string|ComponentSlot|null $badge = null,
        public string|ComponentSlot|null $kbd = null,
        public bool|string|ComponentSlot|null $prepend = null,
        public bool|string|ComponentSlot|null $append = null,
        public bool|string|null $ariaLabel = null,
        public ?string $tooltip = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->as ??= 'span';
        $this->href ??= route_detect($this->route, $this->parameters, $this->href);
        $this->ariaLabel = $this->ariaLabel === true || $this->ariaLabel === null ? $this->tooltip : $this->ariaLabel;

        if ($this->href) {
            $this->as = 'a';
        } elseif ($this->type || $this->action) {
            $this->as = 'button';
        }
    }

    public function baseComponentKey()
    {
        return $this->name;
    }
}
