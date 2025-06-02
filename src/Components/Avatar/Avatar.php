<?php

namespace TALLKit\Components\Avatar;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Avatar extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $alt = null,
        public ?string $src = null,
        public ?string $initials = null,
        public ?bool $square = null,
        public ?string $color = null,
        public ?string $icon = null,
        public ?string $name = null,
        public string|bool|null $tooltip = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->initials = $this->generateInitials($this->initials ?? $this->name);

        if ($this->color === 'auto') {
            $this->color = $this->generateColor();
        }

        if ($this->tooltip === true) {
            $this->tooltip = $this->name ?? false;
        }
    }

    protected function generateInitials($name)
    {
        $parts = Str::of($name)->title()->ucsplit()->filter();

        if ($parts->isEmpty()) {
            return null;
        }

        if ($this->attributes->pluck('initials:single') || $parts->count() === 1) {
            return strtoupper($parts[0][0]);
        }

        if ($parts->count() > 1) {
            return strtoupper($parts[0][0].$parts[1][0]);
        }

        return strtoupper($parts[0][0]).strtolower($parts[0][1]);
    }

    protected function generateColor()
    {
        $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
        $colorSeed = $this->attributes->pluck('color:seed') ?? $this->name ?? $this->icon ?? $this->initials;
        $hash = crc32((string) $colorSeed);

        return $colors[$hash % count($colors)];
    }
}
