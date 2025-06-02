<?php

namespace TALLKit\Components\Brand;

use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Brand extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public string|bool|null $name = null,
        public string|ComponentSlot|bool|null $logo = null,
        public string|bool|null $alt = null,
        public string|bool|null $href = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->name = $this->name === true ? config('app.name') : $this->name;
        $this->logo ??= find_image('logo');
        $this->alt ??= $this->name ?: config('app.name');
        $this->href ??= route_detect('home');
    }
}
