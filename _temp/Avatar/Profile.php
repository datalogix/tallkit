<?php

namespace TALLKit\Components\Avatar;

use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\BladeComponent;

class Profile extends BladeComponent
{
    use InteractsWithUser;

    public function __construct(
        public ?string $size = null,
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $name = null,
        public null|string|array $description = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
        public ?string $image = null,
        public bool $displayEmail = true,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->description ??= data_get($this->user, 'description', $this->displayEmail ? $this->email : null);
        $this->image ??= data_get($this->user, 'image');
    }
}
