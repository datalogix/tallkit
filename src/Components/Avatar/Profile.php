<?php

namespace TALLKit\Components\Avatar;

use TALLKit\Attributes\Mount;
use TALLKit\Concerns\InteractsWithUser;
use TALLKit\View\BladeComponent;

class Profile extends BladeComponent
{
    use InteractsWithUser;

    public function __construct(
        public ?string $size = null,
        public ?string $description = null,
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
